<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;
use App\Models\User;

class SaleFlowTest extends TestCase
{
    private function ensureDbAvailable(): void
    {
        try {
            DB::select('select 1');
        } catch (\Throwable $e) {
            $this->markTestSkipped('Banco de dados indisponível para testes de fluxo.');
        }
    }

    private function createFuncionario(int $academiaId): User
    {
        $user = User::create([
            'nome' => 'Func Test',
            'email' => 'func@test.local',
            'usuario' => 'func',
            'senha' => 'password',
            'nivelAcesso' => 'Funcionário',
            'idAcademia' => $academiaId,
        ]);
        return $user;
    }

    public function test_complete_sale_generates_contas_receber_and_movements(): void
    {
        Config::set('database.default', 'mysql');
        $this->ensureDbAvailable();
        $academiaId = DB::table('academias')->insertGetId(['nome' => 'Academia '.uniqid()]);
        $clienteId = DB::table('clientes')->insertGetId([
            'idAcademia' => $academiaId,
            'nome' => 'Cliente 1',
            'cpf' => '00000000000',
            'dataNascimento' => '2000-01-01',
            'status' => 'Ativo',
        ]);
        $categoriaId = DB::table('categorias')->insertGetId(['idAcademia' => $academiaId, 'nome' => 'Cat '.uniqid(), 'status' => 'Ativo']);
        $marcaId = DB::table('marcas')->insertGetId(['nome' => 'Marca '.uniqid()]);
        $produtoNome = 'Produto A '.uniqid();
        $produtoId = DB::table('produtos')->insertGetId([
            'idAcademia' => $academiaId,
            'idCategoria' => $categoriaId,
            'idMarca' => $marcaId,
            'nome' => $produtoNome,
            'preco' => 10.00,
            'estoque' => 10,
        ]);

        $user = $this->createFuncionario($academiaId);
        $this->actingAs($user);

        $payload = [
            'idCliente' => $clienteId,
            'formaPagamento' => 'PIX',
            'produtos' => [
                ['idProduto' => $produtoId, 'quantidade' => 2],
            ],
        ];

        $response = $this->post('/vendas', $payload);
        $response->assertRedirect(route('vendas.index'));

        $venda = DB::table('venda_produtos')->orderByDesc('idVenda')->first();
        $this->assertNotNull($venda);
        $this->assertEquals(20.00, (float) $venda->valorTotal);

        $item = DB::table('itens_vendas')->where('idVenda', $venda->idVenda)->first();
        $this->assertNotNull($item);
        $this->assertEquals(2, (int) $item->quantidade);

        $produto = DB::table('produtos')->where('idProduto', $produtoId)->first();
        $this->assertEquals(8, (int) $produto->estoque);

        $mov = DB::table('movimentacoes_estoque')->where(['idProduto' => $produtoId, 'referenciaId' => $venda->idVenda, 'origem' => 'venda'])->first();
        $this->assertNotNull($mov);
        $this->assertEquals('saida', $mov->tipo);
        $this->assertEquals(2, (int) $mov->quantidade);

        $conta = DB::table('contas_receber')->where(['documentoRef' => $venda->idVenda, 'idCliente' => $clienteId])->first();
        $this->assertNotNull($conta);
        $this->assertEquals('recebida', $conta->status);
        $this->assertEquals('PIX', $conta->formaRecebimento);

        $log = DB::table('activity_logs')->where(['modulo' => 'Vendas', 'acao' => 'criar', 'entidadeId' => $venda->idVenda])->first();
        $this->assertNotNull($log);
    }

    public function test_cancel_sale_restores_stock_and_logs_activity(): void
    {
        Config::set('database.default', 'mysql');
        $this->ensureDbAvailable();
        $academiaId = DB::table('academias')->insertGetId(['nome' => 'Academia '.uniqid()]);
        $categoriaId = DB::table('categorias')->insertGetId(['idAcademia' => $academiaId, 'nome' => 'Cat '.uniqid(), 'status' => 'Ativo']);
        $marcaId = DB::table('marcas')->insertGetId(['nome' => 'Marca '.uniqid()]);
        $produtoId = DB::table('produtos')->insertGetId([
            'idAcademia' => $academiaId,
            'idCategoria' => $categoriaId,
            'idMarca' => $marcaId,
            'nome' => 'Produto A '.uniqid(),
            'preco' => 10.00,
            'estoque' => 5,
        ]);
        $vendaId = DB::table('venda_produtos')->insertGetId([
            'idCliente' => null,
            'idAcademia' => $academiaId,
            'dataVenda' => now(),
            'formaPagamento' => 'Dinheiro',
            'valorTotal' => 20.00,
        ]);
        DB::table('itens_vendas')->insert([
            'idVenda' => $vendaId,
            'idProduto' => $produtoId,
            'quantidade' => 2,
            'precoUnitario' => 10.00,
        ]);

        $user = $this->createFuncionario($academiaId);
        $this->actingAs($user);

        $response = $this->delete('/vendas/'.$vendaId);
        $response->assertRedirect(route('vendas.index'));

        $produto = DB::table('produtos')->where('idProduto', $produtoId)->first();
        $this->assertEquals(7, (int) $produto->estoque);

        $mov = DB::table('movimentacoes_estoque')->where(['idProduto' => $produtoId, 'referenciaId' => $vendaId, 'origem' => 'devolucao'])->first();
        $this->assertNotNull($mov);
        $this->assertEquals('entrada', $mov->tipo);

        $log = DB::table('activity_logs')->where(['modulo' => 'Vendas', 'acao' => 'estornar', 'entidadeId' => $vendaId])->first();
        $this->assertNotNull($log);
    }
}