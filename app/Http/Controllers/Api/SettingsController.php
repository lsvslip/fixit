<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserBitrixSettings;
use App\Services\BitrixService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SettingsController extends Controller
{
    /**
     * Получение настроек пользователя
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        $settings = UserBitrixSettings::getData($user->id);

        return response()->json(['data' => $settings], Response::HTTP_OK);
    }

    /**
     * Сохранение настроек пользователя
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        // Валидация
        $validated = $request->validate([
            'domain' => 'required|string',
            'access_token' => 'required|string|size:40',
        ]);

        // Нормализация домена
        $domain = $this->normalizeDomain($validated['domain']);

        try {
            // Проверяем доступ к API
            $testSettings = new UserBitrixSettings([
                'domain' => $domain,
                'access_token' => $validated['access_token'],
            ]);

            $bitrixService = new BitrixService($testSettings);
            $userId = $bitrixService->getUserId();

            if (!$userId) {
                return response()->json([
                    'message' => 'Неправильные данные авторизации',
                ], Response::HTTP_UNAUTHORIZED);
            }

            // Сохраняем настройки
            UserBitrixSettings::updateOrCreate([
                'user_id' => $user->id,
            ], [
                'domain' => $domain,
                'access_token' => $validated['access_token'],
            ]);

            return response()->json([
                'message' => 'Настройки успешно сохранены!',
            ], Response::HTTP_OK);

        } catch (\Exception $exception) {
            Log::error('Error saving settings', [
                'user_id' => $user->id,
                'error' => $exception->getMessage(),
            ]);

            return response()->json([
                'message' => 'Ошибка при сохранении настроек: ' . $exception->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Проверка доступа к API
     */
    public function checkAccess(Request $request): JsonResponse
    {
        $user = $request->user();

        // Валидация
        $validated = $request->validate([
            'domain' => 'required|string',
            'access_token' => 'required|string|size:40',
        ]);

        // Нормализация домена
        $domain = $this->normalizeDomain($validated['domain']);

        try {
            $testSettings = new UserBitrixSettings([
                'domain' => $domain,
                'access_token' => $validated['access_token'],
            ]);

            $bitrixService = new BitrixService($testSettings);
            $userId = $bitrixService->getUserId();

            if (!$userId) {
                return response()->json([
                    'message' => 'Неправильные данные авторизации',
                ], Response::HTTP_UNAUTHORIZED);
            }

            return response()->json([
                'message' => 'Доступ подтвержден',
                'user_id' => $userId,
            ], Response::HTTP_OK);

        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Ошибка проверки доступа: ' . $exception->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Нормализация домена (как в fotiq)
     */
    private function normalizeDomain(string $domain): string
    {
        $domain = trim($domain, '/');
        if (!preg_match('#^https?://#i', $domain)) {
            $domain = 'https://' . $domain;
        }
        return $domain;
    }
}
