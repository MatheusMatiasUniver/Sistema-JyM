<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Chamar outros seeders na ordem correta de dependência
        $this->call([
            UserSeeder::class, // Depende de nada
            AcademiaSeeder::class, // Depende de nada
            PlanoAssinaturaSeeder::class, // Depende de Academia
            ProdutoSeeder::class, // Depende de nada
            ClienteSeeder::class, // Depende de User
            MensalidadeSeeder::class, // Depende de Cliente
            EntradaSeeder::class, // Depende de Cliente
            VendaProdutoSeeder::class, // Depende de Cliente
            ItensVendaSeeder::class, // Depende de VendaProduto e Produto
        ]);
    }
}