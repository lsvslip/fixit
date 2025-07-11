<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class BitrixInstallation extends Model {
    protected $fillable = [
        'domain','app_sid','auth_id','refresh_id','auth_expires','expires_at'
    ];

    protected $dates = [
        'expires_at',
    ];
}
