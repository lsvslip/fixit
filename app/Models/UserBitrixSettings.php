<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserBitrixSettings extends Model
{
    protected $fillable = [
        'user_id',
        'domain',
        'access_token',
        'refresh_token',
        'token_expires_at',
    ];

    protected $dates = [
        'token_expires_at',
    ];

    /**
     * Отношение к Пользователю
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Получение настроек пользователя
     */
    public static function getData(int $userId): ?UserBitrixSettings
    {
        return self::query()
            ->where('user_id', $userId)
            ->first([
                'id',
                'domain',
                'access_token',
                'refresh_token',
                'token_expires_at',
            ]);
    }
}
