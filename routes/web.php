<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\UserController; // <<< ADICIONE ESTA LINHA!

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Rotas de Autenticação (públicas)
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login.show');
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Grupo de rotas protegidas por autenticação
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

    // Rotas de Registro (agora protegidas por 'auth' middleware e por lógica no Controller)
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // Rotas para o Módulo de Clientes
    Route::resource('clientes', ClienteController::class);

    // --- Rotas para o Módulo de Gerenciamento de Usuários (NOVO) ---
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    // Futuramente, se precisar de edição/exclusão de usuários, pode adicionar mais rotas aqui:
    // Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    // Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    // Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});