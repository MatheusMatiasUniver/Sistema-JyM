<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\VendaController;
use App\Http\Controllers\AcademiaController;
use App\Http\Controllers\PlanoAssinaturaController;
use App\Http\Controllers\FaceRecognitionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/


Route::get('/', [AuthController::class, 'showLoginForm'])->name('login.show');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/reconhecimento', function () {
        return view('reconhecimento');
    })->name('reconhecimento');

    Route::post('/face/register', [FaceRecognitionController::class, 'register']);
    Route::post('/face/authenticate', [FaceRecognitionController::class, 'authenticate']);

    Route::get('/clientes/{cliente}/capturar-rosto', [ClienteController::class, 'showFaceCapture'])->name('clientes.capturarRosto');

    // --- MÓDULOS DE GERENCIAMENTO ---

    Route::resource('clientes', ClienteController::class);

    Route::resource('produtos', ProdutoController::class);

    Route::resource('vendas', VendaController::class);

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy'); 

    Route::resource('academias', AcademiaController::class);

    Route::resource('planos', PlanoAssinaturaController::class);
});