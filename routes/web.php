<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AutorController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ConservacaoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DescontoController;
use App\Http\Controllers\EditoraController;
use App\Http\Controllers\FuncionarioController;
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

    //Rotas CRUD editoras
    Route::delete('editoras/destruir-todos', [EditoraController::class, 'destroyAll'])->name('editoras.destroyAll');
    Route::resource('editoras', EditoraController::class)->only(['index', 'store', 'update', 'destroy']);

    // Rotas CRUD estado de conservação
    Route::delete('/conservacoes/destruir-todos', [ConservacaoController::class, 'destroyAll'])->name('conservacoes.destroyAll');
    Route::resource('conservacoes', ConservacaoController::class)->parameters(['conservacoes' => 'conservacao'])->only(['index', 'store', 'update', 'destroy']);

    // Rotas CRUD autores
    Route::delete('/autores/destruir-todos', [AutorController::class, 'destroyAll'])->name('autores.destroyAll');
    Route::resource('autores', AutorController::class)->parameters(['autores' => 'autor'])->only(['index', 'store', 'update', 'destroy']);

    // Rotas CRUD funcionários
    Route::delete('/funcionarios/destruir-todos', [FuncionarioController::class, 'destroyAll'])->name('funcionarios.destroyAll');
    Route::resource('funcionarios', FuncionarioController::class)->parameters(['funcionarios' => 'funcionario'])->only(['index', 'store', 'update', 'destroy']);

    // Rotas CRUD descontos
    Route::delete('/descontos/destruir-todos', [DescontoController::class, 'destroyAll'])->name('descontos.destroyAll');
    Route::resource('descontos', DescontoController::class)->parameters(['descontos' => 'desconto'])->only(['index', 'store', 'update', 'destroy']);
});
