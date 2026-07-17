<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Rotas de login
Route::get('/', function () {
    return view('login');
});
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rotas para páginas que exigem autenticação
Route::middleware(['auth:funcionario'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::delete('/clientes/destruir-todos', [ClienteController::class, 'destroyAll'])->name('clientes.destroyAll');
    Route::resource('clientes', ClienteController::class)->only(['index', 'store', 'update', 'destroy']);
});
