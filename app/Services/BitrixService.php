<?php

namespace App\Services;

use App\Models\BitrixInstallation;
use App\Models\UserBitrixSettings;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BitrixService
{
    protected $settings;

    public function __construct($settings = null)
    {
        if ($settings instanceof UserBitrixSettings) {
            $this->settings = $settings;
        } else {
            // Для обратной совместимости с существующим кодом
            $this->settings = BitrixInstallation::first();
            if (!$this->settings) throw new \Exception('Нет настроенной установки Bitrix24');
        }
    }

    public function ensureToken()
    {
        if ($this->settings instanceof UserBitrixSettings) {
            // Для персональных настроек
            if (!$this->settings->token_expires_at || Carbon::now()->gt($this->settings->token_expires_at)) {
                $resp = $this->refreshToken();

                if (empty($resp['access_token'])) {
                    Log::error('Error refreshing Bitrix token', $resp);
                    throw new \Exception('Не удалось обновить токен');
                }

                $this->settings->update([
                    'access_token' => $resp['access_token'],
                    'refresh_token' => $resp['refresh_token'] ?? $this->settings->refresh_token,
                    'token_expires_at' => Carbon::now()->addSeconds($resp['expires_in'] ?? 3600),
                ]);
            }
        } else {
            // Для глобальных настроек (обратная совместимость)
            if (!$this->settings->expires_at || Carbon::now()->gt($this->settings->expires_at)) {
                $resp = $this->refreshToken();

                if (empty($resp['access_token'])) {
                    Log::error('Error refreshing Bitrix token', $resp);
                    throw new \Exception('Не удалось обновить токен');
                }

                $this->settings->update([
                    'auth_id'      => $resp['access_token'],
                    'refresh_id'   => $resp['refresh_token'] ?? $this->settings->refresh_id,
                    'auth_expires' => $resp['expires_in'] ?? ($resp['expires'] ?? 3600),
                    'expires_at'   => Carbon::now()->addSeconds($resp['expires_in'] ?? ($resp['expires'] - time())),
                ]);
            }
        }

        return $this->settings;
    }

    protected function refreshToken(): array
    {
        $domain = $this->settings instanceof UserBitrixSettings 
            ? $this->settings->domain 
            : $this->settings->domain;
            
        $refreshToken = $this->settings instanceof UserBitrixSettings 
            ? $this->settings->refresh_token 
            : $this->settings->refresh_id;

        $url = "https://{$domain}/oauth/token/";
        $post = http_build_query([
            'grant_type'    => 'refresh_token',
            'client_id'     => config('services.bitrix.client_id'),
            'client_secret' => config('services.bitrix.client_secret'),
            'refresh_token' => $refreshToken,
        ]);

        $res = file_get_contents($url, false, stream_context_create([
            'http' => ['method'=>'POST','header'=>"Content-Type: application/x-www-form-urlencoded\r\n",'content'=>$post]
        ]));

        return json_decode($res, true);
    }

    public function getUserId(): int
    {
        $this->ensureToken();
        $domain = $this->settings instanceof UserBitrixSettings 
            ? $this->settings->domain 
            : $this->settings->domain;
            
        $token = $this->settings instanceof UserBitrixSettings 
            ? $this->settings->access_token 
            : $this->settings->auth_id;

        $url = "https://{$domain}/rest/user.current.json";
        $res = file_get_contents($url . '?auth=' . $token);
        $j = json_decode($res, true);
        return $j['result']['ID'] ?? 0;
    }

    public function addTask(string $title, string $desc): array
    {
        $this->ensureToken();
        $userId = $this->getUserId();

        $domain = $this->settings instanceof UserBitrixSettings 
            ? $this->settings->domain 
            : $this->settings->domain;
            
        $token = $this->settings instanceof UserBitrixSettings 
            ? $this->settings->access_token 
            : $this->settings->auth_id;

        $url = "https://{$domain}/rest/tasks.task.add.json";
        $payload = ['auth' => $token, 'fields'=>[
            'TITLE'=>$title, 'DESCRIPTION'=>$desc, 'RESPONSIBLE_ID'=>$userId
        ]];

        $res = file_get_contents($url, false, stream_context_create([
            'http'=>['method'=>'POST','header'=>"Content-Type: application/x-www-form-urlencoded\r\n",'content'=>http_build_query($payload)]
        ]));

        return json_decode($res, true);
    }

    /**
     * Создание задачи с расширенными параметрами
     */
    public function createTask(array $data): array
    {
        $this->ensureToken();
        $userId = $this->getUserId();

        $domain = $this->settings instanceof UserBitrixSettings 
            ? $this->settings->domain 
            : $this->settings->domain;
            
        $token = $this->settings instanceof UserBitrixSettings 
            ? $this->settings->access_token 
            : $this->settings->auth_id;

        $url = "https://{$domain}/rest/tasks.task.add.json";
        
        // Формируем описание с типом обращения
        $description = $data['description'];
        if (isset($data['issue_type'])) {
            $typeLabels = [
                'object' => 'Объект',
                'process' => 'Процесс', 
                'employee' => 'Сотрудник'
            ];
            $description = "Тип обращения: " . ($typeLabels[$data['issue_type']] ?? $data['issue_type']) . "\n\n" . $description;
        }

        $payload = [
            'auth' => $token, 
            'fields' => [
                'TITLE' => $data['title'],
                'DESCRIPTION' => $description,
                'RESPONSIBLE_ID' => $userId
            ]
        ];

        $res = file_get_contents($url, false, stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                'content' => http_build_query($payload)
            ]
        ]));

        return json_decode($res, true);
    }
}
