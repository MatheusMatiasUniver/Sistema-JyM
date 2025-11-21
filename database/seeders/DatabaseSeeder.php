<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AcademiaSeeder::class,
            UserSeeder::class,
            PlanoAssinaturaSeeder::class,
            ClienteSeeder::class,
            MarcaSeeder::class,
            FornecedorSeeder::class,
            CategoriaSeeder::class,
            ProdutoSeeder::class,
            EquipamentoSeeder::class,
            ManutencaoEquipamentoSeeder::class,
            MaterialSeeder::class,
            MensalidadeSeeder::class,
            VendaProdutoSeeder::class,
            EntradaSeeder::class,
        ]);
    }
}
