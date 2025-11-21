<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;
use App\Models\User;

class AdminActivityLogTest extends TestCase
{
    private function ensureDbAvailable(): void
    {
        try {
            DB::select('select 1');
        } catch (\Throwable $e) {
            $this->markTestSkipped('Banco de dados indisponível para testes administrativos.');
        }
    }

    private function createAdminWithAcademia(): User
    {
        Config::set('database.default', 'mysql');
        $this->ensureDbAvailable();
        $academiaId = DB::table('academias')->insertGetId(['nome' => 'Academia '.uniqid()]);
        $admin = User::create([
            'nome' => 'Admin',
            'email' => 'admin@test.local',
            'usuario' => 'admin',
            'senha' => 'password',
            'nivelAcesso' => 'Administrador',
        ]);
        DB::table('usuario_academia')->insert(['idUsuario' => $admin->idUsuario, 'idAcademia' => $academiaId]);
        return [$admin, $academiaId];
    }

    public function test_ajustes_update_logs_activity(): void
    {
        [$admin, $academiaId] = $this->createAdminWithAcademia();
        $this->ensureDbAvailable();
        $this->actingAs($admin);

        // Seleciona a academia para o contexto do middleware
        session(['academia_selecionada' => $academiaId]);

        $response = $this->put('/ajustes', ['diaVencimentoSalarios' => 10]);
        $response->assertRedirect(route('ajustes.index'));

        $log = DB::table('activity_logs')->where(['modulo' => 'AjustesSistema', 'acao' => 'update'])->first();
        $this->assertNotNull($log);
    }
}