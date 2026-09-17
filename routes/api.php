<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsuarioApiController;
use App\Http\Controllers\ProductoApiController;
use App\Http\Controllers\ClienteApiController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('jwt')->post('/logout', [AuthController::class, 'logout']);

Route::middleware('jwt')->group(function () {
    Route::apiResource('usuarios', UsuarioApiController::class);
    Route::apiResource('productos', ProductoApiController::class);
    Route::apiResource('clientes', ClienteApiController::class);
});