<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
 

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('clientes')->insert([
            [
                'nome' => 'Pedro Henrique Alves',
                'cpf' => '123.456.789-01',
                'dataNascimento' => '1995-03-15',
                'telefone' => '(44) 99876-5432',
                'email' => 'pedro.alves@email.com',
                'codigo_acesso' => '123456',
                'status' => 'Ativo',
                'idPlano' => 1,
                'idAcademia' => 1,
                'idUsuario' => 2,
            ],
            [
                'nome' => 'Juliana Martins Costa',
                'cpf' => '234.567.890-12',
                'dataNascimento' => '1998-07-22',
                'telefone' => '(44) 99765-4321',
                'email' => 'juliana.costa@email.com',
                'codigo_acesso' => '654321',
                'status' => 'Ativo',
                'idPlano' => 2,
                'idAcademia' => 1,
                'idUsuario' => 2,
            ],
            [
                'nome' => 'Roberto Carlos Souza',
                'cpf' => '345.678.901-23',
                'dataNascimento' => '1990-11-08',
                'telefone' => '(44) 99654-3210',
                'email' => 'roberto.souza@email.com',
                'codigo_acesso' => '789012',
                'status' => 'Inadimplente',
                'idPlano' => 1,
                'idAcademia' => 1,
                'idUsuario' => 2,
            ],
            [
                'nome' => 'Fernanda Lima Santos',
                'cpf' => '456.789.012-34',
                'dataNascimento' => '2000-05-30',
                'telefone' => '(44) 99543-2109',
                'email' => 'fernanda.lima@email.com',
                'codigo_acesso' => '345678',
                'status' => 'Inativo',
                'idPlano' => 3,
                'idAcademia' => 1,
                'idUsuario' => 2,
            ],
            [
                'nome' => 'Lucas Oliveira Pereira',
                'cpf' => '567.890.123-45',
                'dataNascimento' => '1992-09-12',
                'telefone' => '(44) 99432-1098',
                'email' => 'lucas.pereira@email.com',
                'codigo_acesso' => '901234',
                'status' => 'Ativo',
                'idPlano' => 4,
                'idAcademia' => 2,
                'idUsuario' => 3,
            ],
            [
                'nome' => 'Camila Rodrigues Silva',
                'cpf' => '678.901.234-56',
                'dataNascimento' => '1997-12-25',
                'telefone' => '(44) 99321-0987',
                'email' => 'camila.silva@email.com',
                'codigo_acesso' => '567890',
                'status' => 'Ativo',
                'idPlano' => 5,
                'idAcademia' => 2,
                'idUsuario' => 3,
            ],
            [
                'nome' => 'Rafael Mendes Barbosa',
                'cpf' => '789.012.345-67',
                'dataNascimento' => '1988-04-18',
                'telefone' => '(44) 99210-9876',
                'email' => 'rafael.barbosa@email.com',
                'codigo_acesso' => '234567',
                'status' => 'Inativo',
                'idPlano' => 4,
                'idAcademia' => 2,
                'idUsuario' => 3,
            ],
            [
                'nome' => 'Mariana Ferreira Lima',
                'cpf' => '890.123.456-78',
                'dataNascimento' => '1994-08-05',
                'telefone' => '(44) 99109-8765',
                'email' => 'mariana.lima@email.com',
                'codigo_acesso' => '890123',
                'status' => 'Inativo',
                'idPlano' => 1,
                'idAcademia' => 1,
                'idUsuario' => 2,
            ],
        ]);
    }
}