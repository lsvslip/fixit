<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BitrixInstallController;
use App\Http\Controllers\TaskController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//Route::get('/', function () {
//    return view('welcome');
//});

Route::get('/', [TaskController::class, 'showForm']);  // открытие по корневому URL
Route::get('/task/form', [TaskController::class, 'showForm']);  // для совместимости
Route::post('/task/create', [TaskController::class, 'create']);

// install.php для Bitrix24
Route::any('/bitrix/install', [BitrixInstallController::class, 'install']);

// Создание задачи
//Route::get('/task/form', [App\Http\Controllers\TaskController::class, 'showForm']);
//Route::post('/task/create', [App\Http\Controllers\TaskController::class, 'create']);

