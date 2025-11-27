<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Marca;
use App\Models\Academia;

class MarcaSeeder extends Seeder
{
    public function run(): void
    {
        $academias = Academia::all();

        $marcasGlobais = [
            ['nome' => 'Growth Supplements', 'paisOrigem' => 'Brasil', 'site' => 'https://www.growthsupplements.com.br', 'ativo' => true],
            ['nome' => 'Max Titanium', 'paisOrigem' => 'Brasil', 'site' => 'https://www.maxtitanium.com.br', 'ativo' => true],
            ['nome' => 'IntegralMedica', 'paisOrigem' => 'Brasil', 'site' => 'https://www.integralmedica.com.br', 'ativo' => true],
            ['nome' => 'Kalenji', 'paisOrigem' => 'França', 'site' => 'https://www.decathlon.com.br', 'ativo' => true],
            ['nome' => 'Everlast', 'paisOrigem' => 'Estados Unidos', 'site' => 'https://www.everlast.com', 'ativo' => true],
            ['nome' => 'Powerade', 'paisOrigem' => 'Estados Unidos', 'site' => 'https://www.powerade.com', 'ativo' => true],
            ['nome' => 'Domyos', 'paisOrigem' => 'França', 'site' => 'https://www.decathlon.com.br', 'ativo' => true],
        ];

        foreach ($academias as $academia) {
            foreach ($marcasGlobais as $m) {
                Marca::create(array_merge($m, ['idAcademia' => $academia->idAcademia]));
            }
        }
    }
}