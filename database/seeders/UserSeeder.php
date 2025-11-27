<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = DB::table('users')->insertGetId([
            'nome' => 'Administrador Sistema',
            'usuario' => 'admin',
            'email' => 'admin@jym.com.br',
            'senha' => Hash::make('admin123'),
            'nivelAcesso' => 'Administrador',
            'idAcademia' => null,
            'salarioMensal' => 5000.00,
        ]);

        DB::table('usuario_academia')->insert([
            ['idUsuario' => $adminId, 'idAcademia' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['idUsuario' => $adminId, 'idAcademia' => 2, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('users')->insert([
            [
                'nome' => 'Carlos Eduardo Santos',
                'usuario' => 'carlos.santos',
                'email' => 'carlos@jymcentro.com.br',
                'senha' => Hash::make('func123'),
                'nivelAcesso' => 'Funcionário',
                'idAcademia' => 1,
                'salarioMensal' => 2500.00,
            ],
            [
                'nome' => 'Ana Paula Oliveira',
                'usuario' => 'ana.oliveira',
                'email' => 'ana@jymzonasul.com.br',
                'senha' => Hash::make('func123'),
                'nivelAcesso' => 'Funcionário',
                'idAcademia' => 2,
                'salarioMensal' => 2800.00,
            ],
        ]);
    }
}