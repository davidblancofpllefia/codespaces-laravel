<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\TarjetaController;
use App\Http\Middleware\IsUserAuth;
use App\Http\Middleware\IsAdmin;
use App\Http\Controllers\GameController;
use App\Http\Controllers\CategoryController;


// Rutas públicas
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/tarjetas/{id}', [TarjetaController::class, 'show']);
Route::get('/tarjetas', [TarjetaController::class, 'index']);

// Rutas protegidas
Route::middleware([IsUserAuth::class])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'getUser']);

    // Tarjetas privadas
    Route::post('/tarjetas', [TarjetaController::class, 'store']);


    // Categorías
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{category}', [CategoryController::class, 'update']);
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

    // Juegos
    Route::post('/games', [GameController::class, 'store']);
        Route::get('/games', [GameController::class, 'index']);
    Route::put('/games/{game}/finish', [GameController::class, 'finish']);
    Route::get('/ranking', [GameController::class, 'ranking']);

    // Rutas admin protegidas - solo accesibles a usuarios con rol admin
    Route::middleware([IsAdmin::class])->group(function () {
        // Gestión usuarios
        Route::get('/users', [AuthController::class, 'all']);
        Route::get('/users/{id}', [AuthController::class, 'getUserById']);
        Route::put('/users/{id}', [AuthController::class, 'updateUser']);
        Route::delete('/users/{id}', [AuthController::class, 'adminDestroy']);
        //partidas

        Route::delete('/games/{game}', [GameController::class, 'destroy']);
        Route::get('/users/{id}/games', [GameController::class, 'getGamesByUserId']);

        //Tarjetas

    Route::put('/tarjetas/{id}', [TarjetaController::class, 'update']);
    Route::patch('/tarjetas/{id}', [TarjetaController::class, 'updatePartial']);
    Route::delete('/tarjetas/{id}', [TarjetaController::class, 'destroy']);


    });
});
