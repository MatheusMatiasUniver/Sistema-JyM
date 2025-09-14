<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Cliente;
use App\Models\User;
use App\Models\PlanoAssinatura; // <-- NOVO: Importe o Model PlanoAssinatura
use Faker\Factory as Faker;

class ClienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('pt_BR');
        $users = User::all();
        $planos = PlanoAssinatura::all(); // <-- NOVO: Obtenha todos os planos de assinatura

        // Garante que Usuários existam
        if ($users->isEmpty()) {
            $this->call(UserSeeder::class);
            $users = User::all();
        }

        // Garante que Planos de Assinatura existam
        if ($planos->isEmpty()) {
            $this->call(PlanoAssinaturaSeeder::class); // Garante que Planos existam
            $planos = PlanoAssinatura::all();
            if ($planos->isEmpty()) { // Se ainda estiver vazio, é um problema
                $this->command->warn('Nenhum plano de assinatura encontrado após executar PlanoAssinaturaSeeder. Clientes não terão plano vinculado.');
                return; // Impede erro se não houver planos
            }
        }
        
        // Obtenha um ID de plano aleatório para usar na atribuição
        $randomPlanoId = $faker->randomElement($planos->pluck('idPlano')->toArray());

        // Cliente padrão
        Cliente::create([
            'nome' => 'Ana Paula Silva',
            'cpf' => '11122233344',
            'dataNascimento' => '1990-01-15',
            'status' => 'Ativo',
            'foto' => null,
            'idUsuario' => $users->where('nivelAcesso', 'Administrador')->first()->idUsuario ?? $users->first()->idUsuario,
            'idPlano' => $randomPlanoId, // <-- NOVO: Atribui um plano ao cliente padrão
        ]);

        // 15 clientes aleatórios
        for ($i = 0; $i < 15; $i++) {
            // Obtenha um ID de plano diferente para cada cliente aleatório, se desejar mais variação
            $randomPlanoIdForLoop = $faker->randomElement($planos->pluck('idPlano')->toArray());

            Cliente::create([
                'nome' => $faker->name,
                'cpf' => str_replace(['.', '-', ' '], '', $faker->unique()->cpf(false)), // Remove pontos, traços e espaços, e garante CPF único
                'dataNascimento' => $faker->dateTimeBetween('-30 years', '-18 years')->format('Y-m-d'),
                'status' => $faker->randomElement(['Ativo', 'Inativo']),
                'foto' => null,
                'idUsuario' => $faker->randomElement($users->pluck('idUsuario')->toArray()),
                'idPlano' => $randomPlanoIdForLoop, // <-- NOVO: Atribui um plano aos clientes aleatórios
            ]);
        }
    }
}