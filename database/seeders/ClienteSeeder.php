<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Cliente;
use App\Models\User;
use App\Models\PlanoAssinatura;
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
        $planos = PlanoAssinatura::all();

        if ($users->isEmpty()) {
            $this->call(UserSeeder::class);
            $users = User::all();
        }

        if ($planos->isEmpty()) {
            $this->call(PlanoAssinaturaSeeder::class);
            $planos = PlanoAssinatura::all();
            if ($planos->isEmpty()) {
                $this->command->warn('Nenhum plano de assinatura encontrado após executar PlanoAssinaturaSeeder. Clientes não terão plano vinculado.');
                return;
            }
        }
        
        $randomPlanoId = $faker->randomElement($planos->pluck('idPlano')->toArray());

        Cliente::create([
            'nome' => 'Ana Paula Silva',
            'cpf' => '11122233344',
            'dataNascimento' => '1990-01-15',
            'email' => 'joao.silva@example.com', 
            'telefone' => '11987654321',
            'status' => 'Ativo',
            'foto' => null,
            'idUsuario' => $users->where('nivelAcesso', 'Administrador')->first()->idUsuario ?? $users->first()->idUsuario,
            'idPlano' => $randomPlanoId,
        ]);

        for ($i = 0; $i < 15; $i++) {
            $randomPlanoIdForLoop = $faker->randomElement($planos->pluck('idPlano')->toArray());

            Cliente::create([
                'nome' => $faker->name,
                'cpf' => str_replace(['.', '-', ' '], '', $faker->unique()->cpf(false)),
                'dataNascimento' => $faker->dateTimeBetween('-30 years', '-18 years')->format('Y-m-d'),
                'email' => fake()->unique()->safeEmail(), 
                'telefone' => fake()->numerify('###########'), 
                'status' => $faker->randomElement(['Ativo', 'Inativo']),
                'foto' => null,
                'idUsuario' => $faker->randomElement($users->pluck('idUsuario')->toArray()),
                'idPlano' => $randomPlanoIdForLoop,
            ]);
        }
    }
}