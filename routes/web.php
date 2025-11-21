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
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\MensalidadeController;
use App\Http\Controllers\FornecedorController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\ContaPagarController;
use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\EquipamentoController;
use App\Http\Controllers\ManutencaoEquipamentoController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\RequisicaoMaterialController;
use App\Http\Controllers\AjusteSistemaController;
use App\Http\Controllers\CategoriaContaPagarController;
use App\Http\Controllers\ContaReceberController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/


Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login')->name('login.submit');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/metrics', [App\Http\Controllers\DashboardController::class, 'metrics'])
        ->middleware('throttle:30,1')
        ->name('dashboard.metrics');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout'); 

    Route::post('/academia/trocar', [AcademiaController::class, 'trocar'])->name('academia.trocar');

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

    Route::get('/reconhecimento', function () {
        return view('reconhecimento');
    })->name('reconhecimento');

    Route::post('/face/register', [FaceRecognitionController::class, 'register'])->middleware('throttle:face');
    Route::post('/face/authenticate', [FaceRecognitionController::class, 'authenticate'])->middleware('throttle:face');
    Route::post('/face/authenticate-code', [FaceRecognitionController::class, 'authenticateByCode'])->middleware('throttle:face');

    Route::get('/clientes/{cliente}/capturar-rosto', [ClienteController::class, 'showFaceCapture'])->name('clientes.capturarRosto');

    Route::post('/clientes/{cliente}/renovar', [ClienteController::class, 'renewPlan'])
        ->name('clientes.renew')
        ->middleware('funcionario');

    Route::get('/face/kiosk-status', [FaceRecognitionController::class, 'getKioskStatus']);
    Route::post('/face/set-kiosk-registering', [FaceRecognitionController::class, 'setKioskRegistering'])->middleware('throttle:face');

    Route::get('/clientes/excluidos', [ClienteController::class, 'deletedIndex'])->name('clientes.excluidos.index')->middleware('admin');
    Route::post('/clientes/{id}/restore', [ClienteController::class, 'restore'])->name('clientes.restore')->middleware('admin');
    Route::get('/clientes/{id}/confirm-force-delete', [ClienteController::class, 'confirmForceDelete'])->name('clientes.confirmForceDelete')->middleware('admin');
    Route::delete('/clientes/{id}/force-delete', [ClienteController::class, 'forceDelete'])->name('clientes.forceDelete')->middleware('admin');

    Route::resource('clientes', ClienteController::class)->middleware('funcionario');

    Route::resource('produtos', ProdutoController::class)->middleware('funcionario');

    Route::resource('categorias', CategoriaController::class)->middleware('funcionario');
    Route::resource('marcas', MarcaController::class)->middleware('funcionario');

    Route::resource('vendas', VendaController::class)->middleware('funcionario');

    Route::resource('fornecedores', FornecedorController::class)->middleware('funcionario');

    Route::resource('compras', CompraController::class)->middleware('funcionario');
    Route::post('/compras/{compra}/receber', [CompraController::class, 'receber'])->name('compras.receber')->middleware('funcionario');

    Route::get('/financeiro/contas-pagar', [ContaPagarController::class, 'index'])->name('financeiro.contas_pagar.index')->middleware('funcionario');
    Route::post('/financeiro/contas-pagar/{conta}/pagar', [ContaPagarController::class, 'pagar'])->name('financeiro.contas_pagar.pagar')->middleware('funcionario');
    Route::get('/financeiro/contas-receber', [ContaReceberController::class, 'index'])->name('financeiro.contas_receber.index')->middleware('funcionario');
    Route::get('/financeiro/categorias-contas-pagar', [CategoriaContaPagarController::class, 'index'])->name('financeiro.categorias_contas_pagar.index')->middleware('funcionario');
    Route::get('/financeiro/categorias-contas-pagar/create', [CategoriaContaPagarController::class, 'create'])->name('financeiro.categorias_contas_pagar.create')->middleware('funcionario');
    Route::post('/financeiro/categorias-contas-pagar', [CategoriaContaPagarController::class, 'store'])->name('financeiro.categorias_contas_pagar.store')->middleware('funcionario');

    Route::get('/relatorios/compras', [RelatorioController::class, 'compras'])->name('relatorios.compras')->middleware('funcionario');
    Route::get('/relatorios/margem', [RelatorioController::class, 'margemProdutos'])->name('relatorios.margem')->middleware('funcionario');
    Route::get('/relatorios/ruptura', [RelatorioController::class, 'ruptura'])->name('relatorios.ruptura')->middleware('funcionario');

    Route::get('/relatorios/faturamento', [RelatorioController::class, 'faturamentoLucro'])->name('relatorios.faturamento')->middleware('funcionario');
    Route::get('/relatorios/gastos', [RelatorioController::class, 'gastos'])->name('relatorios.gastos')->middleware('funcionario');
    Route::get('/relatorios/inadimplencia', [RelatorioController::class, 'inadimplencia'])->name('relatorios.inadimplencia')->middleware('funcionario');
    Route::get('/relatorios/frequencia', [RelatorioController::class, 'frequencia'])->name('relatorios.frequencia')->middleware('funcionario');
    Route::get('/relatorios/vendas', [RelatorioController::class, 'vendas'])->name('relatorios.vendas')->middleware('funcionario');
    Route::get('/relatorios/por-funcionario', [RelatorioController::class, 'porFuncionario'])->name('relatorios.porFuncionario')->middleware('funcionario');

    Route::get('/relatorios/faturamento/pdf', [RelatorioController::class, 'faturamentoLucroPdf'])->name('relatorios.faturamento.pdf')->middleware('funcionario');
    Route::get('/relatorios/gastos/pdf', [RelatorioController::class, 'gastosPdf'])->name('relatorios.gastos.pdf')->middleware('funcionario');
    Route::get('/relatorios/inadimplencia/pdf', [RelatorioController::class, 'inadimplenciaPdf'])->name('relatorios.inadimplencia.pdf')->middleware('funcionario');
    Route::get('/relatorios/frequencia/pdf', [RelatorioController::class, 'frequenciaPdf'])->name('relatorios.frequencia.pdf')->middleware('funcionario');
    Route::get('/relatorios/vendas/pdf', [RelatorioController::class, 'vendasPdf'])->name('relatorios.vendas.pdf')->middleware('funcionario');
    Route::get('/relatorios/por-funcionario/pdf', [RelatorioController::class, 'porFuncionarioPdf'])->name('relatorios.porFuncionario.pdf')->middleware('funcionario');

    Route::resource('equipamentos', EquipamentoController::class)->middleware('funcionario');
    Route::resource('manutencoes', ManutencaoEquipamentoController::class)->only(['index','create','store'])->middleware('funcionario');

    Route::get('/materiais/requisicoes', [RequisicaoMaterialController::class, 'index'])->name('materiais.requisicoes.index')->middleware('funcionario');
    Route::get('/materiais/requisicoes/create', [RequisicaoMaterialController::class, 'create'])->name('materiais.requisicoes.create')->middleware('funcionario');
    Route::post('/materiais/requisicoes', [RequisicaoMaterialController::class, 'store'])->name('materiais.requisicoes.store')->middleware('funcionario');
    Route::resource('materiais', MaterialController::class)->middleware('funcionario');

    Route::post('/mensalidades/{mensalidade}/pagar', [MensalidadeController::class, 'pagar'])
        ->name('mensalidades.pagar')
        ->middleware('funcionario');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy'); 

    Route::resource('academias', AcademiaController::class)->middleware('admin');

    Route::resource('planos', PlanoAssinaturaController::class);

    Route::get('/ajustes', [AjusteSistemaController::class, 'index'])->name('ajustes.index')->middleware('admin');
    Route::put('/ajustes', [AjusteSistemaController::class, 'update'])->name('ajustes.update')->middleware('admin');
    
    Route::get('/test-notifications', function () {
        return view('test-notifications');
    })->name('test.notifications');
});
