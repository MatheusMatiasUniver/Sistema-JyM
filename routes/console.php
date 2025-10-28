<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Agendar verificação de mensalidades vencidas diariamente às 08:00
Schedule::command('mensalidades:verificar')->dailyAt('08:00');
