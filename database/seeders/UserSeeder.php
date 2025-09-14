<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('pt_BR');

        User::create([
            'nome' => 'Administrador JyM',
            'usuario' => 'adminjym',
            'email' => 'admin@jym.com',
            'senha' => Hash::make('password'),
            'nivelAcesso' => 'Administrador',
        ]);

        User::create([
            'nome' => 'Funcionario JyM',
            'usuario' => 'funcjym',
            'email' => 'funcionario@jym.com',
            'senha' => Hash::make('password'),
            'nivelAcesso' => 'Funcionário',
        ]);

        for ($i = 0; $i < 8; $i++) {
            User::create([
                'nome' => $faker->name,
                'usuario' => $faker->userName,
                'email' => $faker->unique()->safeEmail,
                'senha' => Hash::make('senha123'), // Senha para usuários de teste
                'nivelAcesso' => $faker->randomElement(['Administrador', 'Funcionário']),
            ]);
        }
    }
}