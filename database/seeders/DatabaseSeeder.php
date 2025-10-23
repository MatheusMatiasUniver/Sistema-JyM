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
            CategoriaSeeder::class,
            ProdutoSeeder::class,
            MensalidadeSeeder::class,
            VendaProdutoSeeder::class,
            EntradaSeeder::class,
        ]);
    }
}