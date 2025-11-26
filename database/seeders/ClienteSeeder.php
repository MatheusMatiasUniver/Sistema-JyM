<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


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

        $academias = [1, 2];
        $planosPorAcademia = [];
        foreach ($academias as $idAcademia) {
            $planosPorAcademia[$idAcademia] = DB::table('plano_assinaturas')
                ->where('idAcademia', $idAcademia)
                ->pluck('idPlano')
                ->all();
        }

        $funcionariosPorAcademia = [];
        foreach ($academias as $idAcademia) {
            $funcionarios = DB::table('users')
                ->where('nivelAcesso', 'Funcionário')
                ->where('idAcademia', $idAcademia)
                ->pluck('idUsuario')
                ->all();
            if (empty($funcionarios)) {
                $adminId = DB::table('users')->where('nivelAcesso', 'Administrador')->value('idUsuario');
                $funcionarios = $adminId ? [$adminId] : [null];
            }
            $funcionariosPorAcademia[$idAcademia] = $funcionarios;
        }

        $firstNames = ['Pedro','Juliana','Roberto','Fernanda','Lucas','Camila','Rafael','Mariana','Bruno','Carla','Thiago','Patrícia','André','Aline','Gustavo','Letícia','Diego','Renata','Marcelo','Bianca','Eduardo','Natália','Felipe','Sabrina','João','Ana','Paulo','Isabela','Mateus','Larissa'];
        $lastNames = ['Silva','Santos','Oliveira','Souza','Pereira','Costa','Ferreira','Rodrigues','Almeida','Nascimento','Lima','Araújo','Carvalho','Gomes','Martins','Barbosa','Ribeiro','Mendes','Teixeira','Fernandes'];
        $statuses = ['Ativo','Inadimplente','Inativo'];

        $existingCpfs = DB::table('clientes')->pluck('cpf')->all();
        $existingEmails = DB::table('clientes')->pluck('email')->all();
        $existingCodes = DB::table('clientes')->pluck('codigo_acesso')->all();

        $generateCpf = function () use (&$existingCpfs) {
            do {
                $a = random_int(100, 999);
                $b = random_int(100, 999);
                $c = random_int(100, 999);
                $d = random_int(10, 99);
                $cpf = sprintf('%03d.%03d.%03d-%02d', $a, $b, $c, $d);
            } while (in_array($cpf, $existingCpfs, true));
            $existingCpfs[] = $cpf;
            return $cpf;
        };

        $generateCode = function () use (&$existingCodes) {
            do {
                $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            } while (in_array($code, $existingCodes, true));
            $existingCodes[] = $code;
            return $code;
        };

        $generateEmail = function (string $nome, int $i) use (&$existingEmails) {
            $base = strtolower(str_replace([' ', 'á','à','â','ã','é','ê','í','ó','ô','õ','ú','ç'], ['.','a','a','a','a','e','e','i','o','o','o','u','c'], $nome));
            $email = $base.'.'.$i.'@example.local';
            while (in_array($email, $existingEmails, true)) {
                $suffix = random_int(1, 9999);
                $email = $base.'.'.$suffix.'@example.local';
            }
            $existingEmails[] = $email;
            return $email;
        };

        $novosClientes = [];
        foreach ($academias as $idAcademia) {
            $qtd = 150;
            for ($i = 1; $i <= $qtd; $i++) {
                $nome = $firstNames[array_rand($firstNames)].' '.$lastNames[array_rand($lastNames)].' '.$lastNames[array_rand($lastNames)];
                $cpf = $generateCpf();
                $dataNascimento = Carbon::now()->subYears(random_int(18, 55))->subDays(random_int(0, 365))->toDateString();
                $telefone = sprintf('(44) 9%04d-%04d', random_int(1000, 9999), random_int(1000, 9999));
                $email = $generateEmail($nome, $i);
                $codigoAcesso = $generateCode();
                $statusRand = random_int(1, 100);
                $status = $statusRand <= 60 ? 'Ativo' : ($statusRand <= 85 ? 'Inadimplente' : 'Inativo');
                $planos = $planosPorAcademia[$idAcademia] ?? [];
                $idPlano = !empty($planos) ? $planos[array_rand($planos)] : null;
                $funcs = $funcionariosPorAcademia[$idAcademia] ?? [null];
                $idUsuario = $funcs[array_rand($funcs)] ?? null;

                $novosClientes[] = [
                    'nome' => $nome,
                    'cpf' => $cpf,
                    'dataNascimento' => $dataNascimento,
                    'telefone' => $telefone,
                    'email' => $email,
                    'codigo_acesso' => $codigoAcesso,
                    'status' => $status,
                    'idPlano' => $idPlano,
                    'idAcademia' => $idAcademia,
                    'idUsuario' => $idUsuario,
                ];
            }
        }

        $chunks = array_chunk($novosClientes, 500);
        foreach ($chunks as $chunk) {
            DB::table('clientes')->insert($chunk);
        }
    }
}
