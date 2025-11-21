<?php

namespace Tests\Feature;

use Tests\TestCase;

class RelatoriosFeatureTest extends TestCase
{
    public function test_telas_de_relatorios_retornam_200(): void
    {
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('SQLite não disponível no ambiente de testes');
        }
        $this->withoutMiddleware();
        $rotas = [
            route('relatorios.faturamento'),
            route('relatorios.gastos'),
            route('relatorios.inadimplencia'),
            route('relatorios.frequencia'),
            route('relatorios.vendas'),
            route('relatorios.porFuncionario'),
        ];
        foreach ($rotas as $r) {
            $resp = $this->get($r);
            $resp->assertStatus(200);
        }
    }

    public function test_pdfs_de_relatorios_sao_gerados(): void
    {
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('SQLite não disponível no ambiente de testes');
        }
        $this->withoutMiddleware();
        $rotasPdf = [
            route('relatorios.faturamento.pdf'),
            route('relatorios.gastos.pdf'),
            route('relatorios.inadimplencia.pdf'),
            route('relatorios.frequencia.pdf'),
            route('relatorios.vendas.pdf'),
            route('relatorios.porFuncionario.pdf'),
        ];
        foreach ($rotasPdf as $r) {
            $resp = $this->get($r);
            $resp->assertStatus(200);
            $this->assertTrue(str_contains($resp->headers->get('content-type'), 'application/pdf'));
        }
    }
}
