<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Cliente;

class ClienteSoftDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function testClienteDeletePerformsSoftDelete(): void
    {
        $cliente = Cliente::create([
            'nome' => 'Teste SoftDelete',
            'cpf' => '12345678901',
            'dataNascimento' => '1990-01-01',
            'email' => null,
            'telefone' => null,
            'status' => 'Inativo',
        ]);

        $this->assertNotNull($cliente->idCliente);

        $cliente->delete();

        $this->assertNull(Cliente::find($cliente->idCliente));

        $trashed = Cliente::withTrashed()->find($cliente->idCliente);
        $this->assertNotNull($trashed);
        $this->assertNotNull($trashed->deleted_at);
    }
}