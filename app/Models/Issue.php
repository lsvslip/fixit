<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Collection;

class Issue extends Model
{
    protected $fillable = [
        'user_id',
        'issue_type',
        'object_name',
        'issue_description',
        'expectations_description',
        'bitrix_task_id',
        'status',
    ];

    /**
     * Отношение к Пользователю
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Получение данных для API
     */
    public static function getData(int $userId): Collection
    {
        return self::query()
            ->where('user_id', $userId)
            ->latest()
            ->get([
                'id',
                'issue_type',
                'object_name',
                'issue_description',
                'expectations_description',
                'bitrix_task_id',
                'status',
                'created_at',
            ]);
    }
}
