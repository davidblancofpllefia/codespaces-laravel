<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\MascotaController;
use App\Http\Middleware\IsUserAuth;
use App\Http\Middleware\IsAdmin;

// Rutas públicas
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Rutas protegidas para usuarios autenticados
Route::middleware([IsUserAuth::class])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'getUser']);

    Route::get('/pets', [MascotaController::class, 'index']);
    Route::post('/pets', [MascotaController::class, 'store']);
    Route::get('/pets/{id}', [MascotaController::class, 'show']);
    Route::put('/pets/{id}', [MascotaController::class, 'update']);
    Route::patch('/pets/{id}', [MascotaController::class, 'updatePartial']);
    Route::delete('/pets/{id}', [MascotaController::class, 'destroy']);
});

// Rutas protegidas solo para administradores
Route::middleware([IsUserAuth::class, IsAdmin::class])->group(function () {
    Route::get('/users', [AuthController::class, 'index']);
    Route::get('/users/{id}', [AuthController::class, 'show']);
    Route::put('/users/{id}', [AuthController::class, 'update']);
    Route::delete('/users/{id}', [AuthController::class, 'destroy']);
});



