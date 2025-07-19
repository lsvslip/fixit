<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Issue;
use App\Models\UserBitrixSettings;
use App\Services\BitrixService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class IssueController extends Controller
{
    /**
     * Создание нового обращения
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        
        // Валидация
        $validated = $request->validate([
            'issue_type' => 'required|in:object,process,employee',
            'object_name' => 'required|string|max:255',
            'issue_description' => 'required|string',
            'expectations_description' => 'required|string',
            'file' => 'nullable|file|image|max:10240', // до 10MB
        ]);

        // Получаем настройки пользователя
        $settings = UserBitrixSettings::getData($user->id);
        if (!$settings) {
            return response()->json([
                'message' => 'Настройки Bitrix24 не найдены. Сначала настройте интеграцию.',
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            // Сохраняем файл, если он есть
            $filePath = null;
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('issues', $fileName, 'public');
            }

            // Создаем задачу в Bitrix24
            $bitrixService = new BitrixService($settings);
            $bitrixResponse = $bitrixService->createTask([
                'title' => $validated['object_name'],
                'description' => $validated['issue_description'] . "\n\nОжидания: " . $validated['expectations_description'],
                'issue_type' => $validated['issue_type'],
            ]);

            // Сохраняем в БД
            $issue = Issue::create([
                'user_id' => $user->id,
                'issue_type' => $validated['issue_type'],
                'object_name' => $validated['object_name'],
                'issue_description' => $validated['issue_description'],
                'expectations_description' => $validated['expectations_description'],
                'bitrix_task_id' => $bitrixResponse['result']['task']['id'] ?? null,
                'status' => 'new',
            ]);

            // Логируем запрос
            $this->logApiRequest(
                $settings->domain . '/rest/tasks.task.add',
                $user->only(['id', 'email']),
                $bitrixResponse
            );

            return response()->json([
                'message' => 'Обращение успешно создано!',
                'data' => $issue->only(['id', 'bitrix_task_id', 'status']),
            ], Response::HTTP_CREATED);

        } catch (\Exception $exception) {
            Log::error('Error creating issue', [
                'user_id' => $user->id,
                'error' => $exception->getMessage(),
            ]);

            return response()->json([
                'message' => 'Ошибка при создании обращения: ' . $exception->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Получение списка обращений пользователя
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        $data = Issue::getData($user->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    /**
     * Логирование API запросов (как в fotiq)
     */
    private function logApiRequest($url, $user, $data, bool $isError = false): void
    {
        $json = json_encode(compact('url', 'user', 'data'));
        if ($isError) {
            Log::error($json);
        } else {
            Log::debug($json);
        }
    }
}
