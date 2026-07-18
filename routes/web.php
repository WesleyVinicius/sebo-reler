<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GeneroController;
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
    // Rota para o dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Rotas CRUD clientes
    Route::delete('/clientes/destruir-todos', [ClienteController::class, 'destroyAll'])->name('clientes.destroyAll');
    Route::resource('clientes', ClienteController::class)->only(['index', 'store', 'update', 'destroy']);

    // Rotas CRUD generos
    Route::delete('generos/destruir-todos', [GeneroController::class, 'destroyAll'])->name('generos.destroyAll');
    Route::resource('generos', GeneroController::class)->only(['index', 'store', 'update', 'destroy']);
});
