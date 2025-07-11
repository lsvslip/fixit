<?php
namespace App\Services;

use App\Models\BitrixInstallation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BitrixService
{
    protected $inst;

    public function __construct()
    {
        $this->inst = BitrixInstallation::first();
        if (!$this->inst) throw new \Exception('Нет настроенной установки Bitrix24');
    }

    public function ensureToken()
    {
        if (!$this->inst->expires_at || Carbon::now()->gt($this->inst->expires_at)) {
            $resp = $this->refreshToken();

            if (empty($resp['access_token'])) {
                Log::error('Error refreshing Bitrix token', $resp);
                throw new \Exception('Не удалось обновить токен');
            }

            $this->inst->update([
                'auth_id'      => $resp['access_token'],
                'refresh_id'   => $resp['refresh_token'] ?? $this->inst->refresh_id,
                'auth_expires' => $resp['expires_in'] ?? ($resp['expires'] ?? 3600),
                'expires_at'   => Carbon::now()->addSeconds($resp['expires_in'] ?? ($resp['expires'] - time())),
            ]);
        }

        return $this->inst;
    }

    protected function refreshToken(): array
    {
        $url = "https://{$this->inst->domain}/oauth/token/";
        $post = http_build_query([
            'grant_type'    => 'refresh_token',
            'client_id'     => config('services.bitrix.client_id'),
            'client_secret' => config('services.bitrix.client_secret'),
            'refresh_token' => $this->inst->refresh_id,
        ]);

        $res = file_get_contents($url, false, stream_context_create([
            'http' => ['method'=>'POST','header'=>"Content-Type: application/x-www-form-urlencoded\r\n",'content'=>$post]
        ]));

        return json_decode($res, true);
    }

    public function getUserId(): int
    {
        $this->ensureToken();
        $url = "https://{$this->inst->domain}/rest/user.current.json";
        $res = file_get_contents($url . '?auth=' . $this->inst->auth_id);
        $j = json_decode($res, true);
        return $j['result']['ID'] ?? 0;
    }

    public function addTask(string $title, string $desc): array
    {
        $this->ensureToken();
        $userId = $this->getUserId();

        $url = "https://{$this->inst->domain}/rest/tasks.task.add.json";
        $payload = ['auth' => $this->inst->auth_id, 'fields'=>[
            'TITLE'=>$title, 'DESCRIPTION'=>$desc, 'RESPONSIBLE_ID'=>$userId
        ]];

        $res = file_get_contents($url, false, stream_context_create([
            'http'=>['method'=>'POST','header'=>"Content-Type: application/x-www-form-urlencoded\r\n",'content'=>http_build_query($payload)]
        ]));

        return json_decode($res, true);
    }
}
