<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Cliente;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('mensalidades:verificar')
    ->dailyAt('00:05')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/scheduler.log'));

Artisan::command('verify:cliente-soft-delete', function () {
    $cpf = (string) random_int(10000000000, 99999999999);
    $cliente = Cliente::create([
        'nome' => 'Verificacao SoftDelete',
        'cpf' => $cpf,
        'dataNascimento' => '1990-01-01',
        'status' => 'Inativo',
    ]);

    $id = $cliente->idCliente;
    $cliente->delete();

    $existsActive = Cliente::find($id) !== null;
    $trashed = Cliente::withTrashed()->find($id);
    $deletedAtSet = $trashed && $trashed->deleted_at !== null;

    if (!$existsActive && $deletedAtSet) {
        $this->info('Soft delete verificado com sucesso para cliente ID '.$id.'.');
    } else {
        $this->error('Falha na verificação de soft delete para cliente ID '.$id.'.');
    }
})->purpose('Verifica se a exclusão de Cliente é soft delete');
