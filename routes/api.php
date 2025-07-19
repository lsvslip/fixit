<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\IssueController;
use App\Http\Controllers\Api\SettingsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API для обращений (задач)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/issue', [IssueController::class, 'store']);
    Route::get('/issue', [IssueController::class, 'show']);
});

// API для настроек Bitrix24
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/settings', [SettingsController::class, 'show']);
    Route::post('/settings', [SettingsController::class, 'store']);
    Route::get('/check-access', [SettingsController::class, 'checkAccess']);
});
