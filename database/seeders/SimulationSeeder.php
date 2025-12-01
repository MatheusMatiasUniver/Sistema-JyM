<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\Academia;
use App\Models\User;
use App\Models\Cliente;
use App\Models\PlanoAssinatura;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Fornecedor;
use App\Models\Produto;
use App\Models\Equipamento;
use App\Models\Material;
use App\Models\Entrada;
use App\Models\VendaProduto;
use App\Models\ItemVenda;
use App\Models\Compra;
use App\Models\ItemCompra;
use App\Models\Mensalidade;
use App\Models\ContaPagar;
use App\Models\ContaPagarCategoria;
use App\Models\ContaReceber;
use App\Models\ManutencaoEquipamento;
use App\Models\MovimentacaoEstoque;

class SimulationSeeder extends Seeder
{
    private Academia $academiaAtual;
    private User $adminAtual;
    private User $funcionarioAtual;
    private $clientesAtual;
    private $produtosAtual;
    private $planosAtual;
    private $fornecedoresAtual;
    private $equipamentosAtual;
    private $categoriasContaPagarAtual;

    private $academias;
    private $dadosPorAcademia = [];

    private array $formasPagamento = ['Dinheiro', 'PIX', 'Cartão de Crédito', 'Cartão de Débito'];

    public function run(): void
    {
        $this->command->info('🏋️ Iniciando simulação de 120 dias de operação para DUAS academias...');
        
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $this->truncateTables();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->criarTodasAcademias();
        $this->simularOperacaoDiaria();

        $this->command->info('✅ Simulação concluída com sucesso para DUAS academias!');
    }

    private function truncateTables(): void
    {
        $tables = [
            'movimentacoes_estoque',
            'itens_vendas',
            'venda_produtos',
            'itens_compras',
            'compras',
            'mensalidades',
            'entradas',
            'contas_pagar',
            'contas_receber',
            'manutencoes_equipamento',
            'equipamentos',
            'materiais',
            'produtos',
            'categorias',
            'marcas',
            'fornecedores',
            'categorias_contas_pagar',
            'clientes',
            'plano_assinaturas',
            'usuario_academia',
            'users',
            'academias',
        ];

        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }
    }

    private function criarTodasAcademias(): void
    {
        $this->command->info('📦 Criando dados estáticos para DUAS academias...');

        $academiasConfig = [
            [
                'academia' => [
                    'nome' => 'Iron Fitness Academia',
                    'CNPJ' => '12345678000190',
                    'telefone' => '(44) 99999-8888',
                    'email' => 'contato@ironfitness.com.br',
                    'endereco' => 'Av. Brasil, 1500 - Centro, Maringá - PR',
                    'responsavel' => 'Carlos Eduardo Silva',
                ],
                'funcionario' => [
                    'nome' => 'Maria Souza',
                    'usuario' => 'maria.souza',
                    'email' => 'maria@ironfitness.com.br',
                ],
                'clientes' => [
                    'Ana Clara Oliveira', 'Bruno Santos Silva', 'Carla Mendes', 'Daniel Ferreira Costa',
                    'Elena Rodrigues', 'Felipe Almeida', 'Gabriela Lima', 'Henrique Souza',
                    'Isabela Martins', 'João Pedro Nascimento', 'Karina Dias', 'Lucas Ribeiro',
                    'Mariana Costa', 'Nicolas Pereira', 'Olivia Santos', 'Paulo Henrique',
                    'Rafaela Gomes', 'Samuel Alves', 'Tatiana Moreira', 'Vinícius Castro',
                    'Amanda Barbosa', 'Ricardo Teixeira', 'Juliana Cardoso', 'Thiago Fernandes',
                    'Letícia Araújo', 'Gustavo Rocha', 'Camila Nunes', 'André Monteiro',
                    'Beatriz Correia', 'Matheus Pinto', 'Larissa Duarte', 'Fernando Lopes',
                ],
            ],
            [
                'academia' => [
                    'nome' => 'Power House Gym',
                    'CNPJ' => '98765432000110',
                    'telefone' => '(43) 98888-7777',
                    'email' => 'contato@powerhousegym.com.br',
                    'endereco' => 'Rua Sergipe, 850 - Centro, Londrina - PR',
                    'responsavel' => 'Roberto Almeida Santos',
                ],
                'funcionario' => [
                    'nome' => 'Pedro Henrique Lima',
                    'usuario' => 'pedro.lima',
                    'email' => 'pedro@powerhousegym.com.br',
                ],
                'clientes' => [
                    'Fernanda Costa Silva', 'Rodrigo Pereira', 'Aline Moreira Santos', 'Diego Alves',
                    'Patrícia Ferreira', 'Marcelo Souza', 'Renata Dias Lima', 'Eduardo Martins',
                    'Vanessa Ribeiro', 'Leonardo Nascimento', 'Cristiane Gomes', 'Fábio Teixeira',
                    'Débora Cardoso', 'Guilherme Araújo', 'Simone Rocha', 'Anderson Monteiro',
                    'Priscila Correia', 'Vinícius Pinto', 'Natália Duarte', 'Caio Lopes',
                    'Michele Barbosa', 'Rafael Santos', 'Jéssica Oliveira', 'Leandro Fernandes',
                ],
            ],
        ];

        $admin = User::create([
            'nome' => 'Administrador Sistema',
            'usuario' => 'admin',
            'email' => 'admin@sistemajym.com.br',
            'senha' => Hash::make('admin123'),
            'nivelAcesso' => 'Administrador',
            'idAcademia' => null,
            'salarioMensal' => 10000.00,
        ]);

        $this->academias = collect();

        foreach ($academiasConfig as $index => $config) {
            $this->command->info("  🏢 Criando academia: {$config['academia']['nome']}");
            
            $academia = Academia::create($config['academia']);
            $this->academias->push($academia);

            DB::table('usuario_academia')->insert([
                'idUsuario' => $admin->idUsuario,
                'idAcademia' => $academia->idAcademia,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $funcionario = User::create([
                'nome' => $config['funcionario']['nome'],
                'usuario' => $config['funcionario']['usuario'],
                'email' => $config['funcionario']['email'],
                'senha' => Hash::make('func123'),
                'nivelAcesso' => 'Funcionário',
                'idAcademia' => $academia->idAcademia,
                'salarioMensal' => 2500.00,
            ]);

            $dadosAcademia = $this->criarDadosEstaticosAcademia($academia, $funcionario);
            $clientes = $this->criarClientesAcademia($academia, $funcionario, $dadosAcademia['planos'], $config['clientes']);

            $this->dadosPorAcademia[$academia->idAcademia] = [
                'academia' => $academia,
                'admin' => $admin,
                'funcionario' => $funcionario,
                'clientes' => $clientes,
                'produtos' => $dadosAcademia['produtos'],
                'planos' => $dadosAcademia['planos'],
                'fornecedores' => $dadosAcademia['fornecedores'],
                'equipamentos' => $dadosAcademia['equipamentos'],
                'categoriasContaPagar' => $dadosAcademia['categoriasContaPagar'],
            ];

            $this->command->info("    ✓ {$config['academia']['nome']}: " . count($config['clientes']) . " clientes criados");
        }

        $this->command->info('  ✓ Todas as academias criadas com sucesso!');
    }

    private function criarDadosEstaticosAcademia(Academia $academia, User $funcionario): array
    {
        $planos = collect();
        $planosData = [
            ['nome' => 'Mensal Básico', 'descricao' => 'Acesso à musculação', 'valor' => 89.90, 'duracaoDias' => 30],
            ['nome' => 'Mensal Completo', 'descricao' => 'Musculação + Aulas coletivas', 'valor' => 129.90, 'duracaoDias' => 30],
            ['nome' => 'Trimestral', 'descricao' => 'Acesso completo por 3 meses', 'valor' => 299.90, 'duracaoDias' => 90],
            ['nome' => 'Semestral', 'descricao' => 'Acesso completo por 6 meses', 'valor' => 499.90, 'duracaoDias' => 180],
            ['nome' => 'Anual', 'descricao' => 'Melhor custo-benefício', 'valor' => 799.90, 'duracaoDias' => 365],
        ];

        foreach ($planosData as $plano) {
            $planos->push(PlanoAssinatura::create(array_merge($plano, [
                'idAcademia' => $academia->idAcademia,
            ])));
        }

        $categoriasData = [
            ['nome' => 'Suplementos', 'descricao' => 'Suplementos alimentares'],
            ['nome' => 'Bebidas', 'descricao' => 'Bebidas e isotônicos'],
            ['nome' => 'Acessórios', 'descricao' => 'Acessórios para treino'],
            ['nome' => 'Roupas', 'descricao' => 'Vestuário esportivo'],
        ];

        $categorias = collect();
        foreach ($categoriasData as $cat) {
            $categorias->push(Categoria::create(array_merge($cat, [
                'status' => 'Ativo',
                'idAcademia' => $academia->idAcademia,
            ])));
        }

        $marcasData = [
            ['nome' => 'Growth Supplements', 'paisOrigem' => 'Brasil', 'site' => 'https://www.gsuplementos.com.br'],
            ['nome' => 'Max Titanium', 'paisOrigem' => 'Brasil', 'site' => 'https://www.maxtitanium.com.br'],
            ['nome' => 'IntegralMedica', 'paisOrigem' => 'Brasil', 'site' => 'https://www.integralmedica.com.br'],
            ['nome' => 'Probiótica', 'paisOrigem' => 'Brasil', 'site' => 'https://www.probiotica.com.br'],
            ['nome' => 'Everlast', 'paisOrigem' => 'Estados Unidos', 'site' => 'https://www.everlast.com'],
        ];

        $marcas = collect();
        foreach ($marcasData as $marca) {
            $marcas->push(Marca::create(array_merge($marca, [
                'ativo' => true,
                'idAcademia' => $academia->idAcademia,
            ])));
        }

        $fornecedoresData = [
            ['razaoSocial' => 'Distribuidora de Suplementos Ltda', 'cnpjCpf' => '11222333000144', 'contato' => 'João Silva', 'telefone' => '(11) 3333-4444', 'email' => 'vendas@distsuplementos.com.br', 'endereco' => 'São Paulo - SP', 'condicaoPagamentoPadrao' => '30 dias'],
            ['razaoSocial' => 'Bebidas & Cia ME', 'cnpjCpf' => '22333444000155', 'contato' => 'Ana Paula', 'telefone' => '(44) 3232-5555', 'email' => 'contato@bebidasecia.com.br', 'endereco' => 'Maringá - PR', 'condicaoPagamentoPadrao' => '15 dias'],
            ['razaoSocial' => 'Artigos Esportivos Brasil', 'cnpjCpf' => '33444555000166', 'contato' => 'Roberto Carlos', 'telefone' => '(21) 2222-6666', 'email' => 'comercial@artigosbr.com.br', 'endereco' => 'Rio de Janeiro - RJ', 'condicaoPagamentoPadrao' => '28 dias'],
        ];

        $fornecedores = collect();
        foreach ($fornecedoresData as $forn) {
            $fornecedores->push(Fornecedor::create(array_merge($forn, [
                'ativo' => true,
                'idAcademia' => $academia->idAcademia,
            ])));
        }

        $produtosData = [
            ['nome' => 'Whey Protein 1kg', 'idCategoria' => $categorias[0]->idCategoria, 'idMarca' => $marcas[0]->idMarca, 'idFornecedor' => $fornecedores[0]->idFornecedor, 'preco' => 129.90, 'precoCompra' => 89.90, 'estoque' => 50, 'estoqueMinimo' => 10],
            ['nome' => 'Creatina 300g', 'idCategoria' => $categorias[0]->idCategoria, 'idMarca' => $marcas[0]->idMarca, 'idFornecedor' => $fornecedores[0]->idFornecedor, 'preco' => 79.90, 'precoCompra' => 52.90, 'estoque' => 40, 'estoqueMinimo' => 8],
            ['nome' => 'BCAA 120 caps', 'idCategoria' => $categorias[0]->idCategoria, 'idMarca' => $marcas[1]->idMarca, 'idFornecedor' => $fornecedores[0]->idFornecedor, 'preco' => 59.90, 'precoCompra' => 38.90, 'estoque' => 35, 'estoqueMinimo' => 8],
            ['nome' => 'Pré-Treino 300g', 'idCategoria' => $categorias[0]->idCategoria, 'idMarca' => $marcas[2]->idMarca, 'idFornecedor' => $fornecedores[0]->idFornecedor, 'preco' => 89.90, 'precoCompra' => 59.90, 'estoque' => 25, 'estoqueMinimo' => 5],
            ['nome' => 'Glutamina 300g', 'idCategoria' => $categorias[0]->idCategoria, 'idMarca' => $marcas[3]->idMarca, 'idFornecedor' => $fornecedores[0]->idFornecedor, 'preco' => 69.90, 'precoCompra' => 45.90, 'estoque' => 30, 'estoqueMinimo' => 6],
            ['nome' => 'Barra de Proteína', 'idCategoria' => $categorias[0]->idCategoria, 'idMarca' => $marcas[0]->idMarca, 'idFornecedor' => $fornecedores[0]->idFornecedor, 'preco' => 8.90, 'precoCompra' => 5.50, 'estoque' => 100, 'estoqueMinimo' => 20],
            ['nome' => 'Água Mineral 500ml', 'idCategoria' => $categorias[1]->idCategoria, 'idMarca' => null, 'idFornecedor' => $fornecedores[1]->idFornecedor, 'preco' => 4.00, 'precoCompra' => 1.80, 'estoque' => 200, 'estoqueMinimo' => 50],
            ['nome' => 'Isotônico 500ml', 'idCategoria' => $categorias[1]->idCategoria, 'idMarca' => null, 'idFornecedor' => $fornecedores[1]->idFornecedor, 'preco' => 7.50, 'precoCompra' => 4.50, 'estoque' => 80, 'estoqueMinimo' => 20],
            ['nome' => 'Energético 250ml', 'idCategoria' => $categorias[1]->idCategoria, 'idMarca' => null, 'idFornecedor' => $fornecedores[1]->idFornecedor, 'preco' => 9.90, 'precoCompra' => 6.50, 'estoque' => 60, 'estoqueMinimo' => 15],
            ['nome' => 'Luva de Treino', 'idCategoria' => $categorias[2]->idCategoria, 'idMarca' => $marcas[4]->idMarca, 'idFornecedor' => $fornecedores[2]->idFornecedor, 'preco' => 49.90, 'precoCompra' => 32.00, 'estoque' => 20, 'estoqueMinimo' => 5],
            ['nome' => 'Squeeze 1L', 'idCategoria' => $categorias[2]->idCategoria, 'idMarca' => null, 'idFornecedor' => $fornecedores[2]->idFornecedor, 'preco' => 25.90, 'precoCompra' => 15.90, 'estoque' => 30, 'estoqueMinimo' => 8],
            ['nome' => 'Toalha Esportiva', 'idCategoria' => $categorias[2]->idCategoria, 'idMarca' => null, 'idFornecedor' => $fornecedores[2]->idFornecedor, 'preco' => 29.90, 'precoCompra' => 18.90, 'estoque' => 25, 'estoqueMinimo' => 6],
            ['nome' => 'Camiseta Dry Fit', 'idCategoria' => $categorias[3]->idCategoria, 'idMarca' => null, 'idFornecedor' => $fornecedores[2]->idFornecedor, 'preco' => 59.90, 'precoCompra' => 35.00, 'estoque' => 40, 'estoqueMinimo' => 10],
            ['nome' => 'Shorts Academia', 'idCategoria' => $categorias[3]->idCategoria, 'idMarca' => null, 'idFornecedor' => $fornecedores[2]->idFornecedor, 'preco' => 49.90, 'precoCompra' => 28.00, 'estoque' => 35, 'estoqueMinimo' => 8],
        ];

        $produtos = collect();
        foreach ($produtosData as $prod) {
            $produtos->push(Produto::create(array_merge($prod, [
                'custoMedio' => $prod['precoCompra'],
                'idAcademia' => $academia->idAcademia,
            ])));
        }

        $equipamentosData = [
            ['descricao' => 'Esteira Elétrica Profissional', 'fabricante' => 'Technogym', 'modelo' => 'Skillrun', 'valorAquisicao' => 45000.00],
            ['descricao' => 'Bicicleta Ergométrica Vertical', 'fabricante' => 'Life Fitness', 'modelo' => 'Integrity', 'valorAquisicao' => 25000.00],
            ['descricao' => 'Elíptico Profissional', 'fabricante' => 'Precor', 'modelo' => 'EFX 885', 'valorAquisicao' => 38000.00],
            ['descricao' => 'Power Rack', 'fabricante' => 'Hammer Strength', 'modelo' => 'HD Elite', 'valorAquisicao' => 18000.00],
            ['descricao' => 'Leg Press 45°', 'fabricante' => 'Technogym', 'modelo' => 'Selection Pro', 'valorAquisicao' => 22000.00],
            ['descricao' => 'Supino Reto', 'fabricante' => 'Life Fitness', 'modelo' => 'Signature', 'valorAquisicao' => 12000.00],
            ['descricao' => 'Puxador Alto', 'fabricante' => 'Hammer Strength', 'modelo' => 'MTS', 'valorAquisicao' => 15000.00],
            ['descricao' => 'Cross Cable', 'fabricante' => 'Technogym', 'modelo' => 'Element+', 'valorAquisicao' => 35000.00],
            ['descricao' => 'Cadeira Extensora', 'fabricante' => 'Life Fitness', 'modelo' => 'Optima', 'valorAquisicao' => 11000.00],
            ['descricao' => 'Mesa Flexora', 'fabricante' => 'Hammer Strength', 'modelo' => 'Select', 'valorAquisicao' => 11500.00],
        ];

        $equipamentos = collect();
        $dataAquisicao = Carbon::now()->subYears(2);
        foreach ($equipamentosData as $equip) {
            $equipamentos->push(Equipamento::create(array_merge($equip, [
                'numeroSerie' => strtoupper(fake()->bothify('??-##-????-###')),
                'dataAquisicao' => $dataAquisicao->format('Y-m-d'),
                'garantiaFim' => $dataAquisicao->copy()->addMonths(24)->format('Y-m-d'),
                'centroCusto' => 'Academia',
                'status' => 'Ativo',
                'idAcademia' => $academia->idAcademia,
            ])));
        }

        $materiaisData = [
            ['descricao' => 'Álcool 70%', 'estoque' => 20, 'unidadeMedida' => 'L', 'estoqueMinimo' => 5],
            ['descricao' => 'Toalhas de Papel', 'estoque' => 50, 'unidadeMedida' => 'pct', 'estoqueMinimo' => 10],
            ['descricao' => 'Desinfetante', 'estoque' => 15, 'unidadeMedida' => 'L', 'estoqueMinimo' => 5],
            ['descricao' => 'Lubrificante Esteira', 'estoque' => 8, 'unidadeMedida' => 'L', 'estoqueMinimo' => 3],
        ];

        foreach ($materiaisData as $mat) {
            Material::create(array_merge($mat, [
                'idAcademia' => $academia->idAcademia,
            ]));
        }

        $categoriasContaPagarData = [
            'Aluguel', 'Energia Elétrica', 'Água', 'Internet', 'Telefone',
            'Manutenção', 'Fornecedores', 'Salários', 'Impostos', 'Outros'
        ];

        $categoriasContaPagar = collect();
        foreach ($categoriasContaPagarData as $nome) {
            $categoriasContaPagar->push(ContaPagarCategoria::create([
                'idAcademia' => $academia->idAcademia,
                'nome' => $nome,
                'ativa' => true,
            ]));
        }

        return [
            'planos' => $planos,
            'categorias' => $categorias,
            'marcas' => $marcas,
            'fornecedores' => $fornecedores,
            'produtos' => $produtos,
            'equipamentos' => $equipamentos,
            'categoriasContaPagar' => $categoriasContaPagar,
        ];
    }

    private function criarClientesAcademia(Academia $academia, User $funcionario, $planos, array $nomes): \Illuminate\Support\Collection
    {
        $clientes = collect();

        foreach ($nomes as $index => $nome) {
            $plano = $planos->random();
            
            $cliente = Cliente::create([
                'nome' => $nome,
                'cpf' => $this->gerarCpf(),
                'dataNascimento' => fake()->dateTimeBetween('-50 years', '-18 years')->format('Y-m-d'),
                'telefone' => sprintf('(%02d) 9%04d-%04d', rand(11, 99), rand(1000, 9999), rand(1000, 9999)),
                'email' => strtolower(str_replace(' ', '.', fake()->unique()->firstName() . '.' . fake()->lastName())) . '@email.com',
                'codigo_acesso' => str_pad((string) (($academia->idAcademia * 1000) + $index + 1), 6, '0', STR_PAD_LEFT),
                'status' => 'Ativo',
                'idAcademia' => $academia->idAcademia,
                'idPlano' => $plano->idPlano,
                'idUsuario' => $funcionario->idUsuario,
            ]);

            $clientes->push($cliente);
        }

        return $clientes;
    }

    private function simularOperacaoDiaria(): void
    {
        $this->command->info('📅 Simulando operações diárias (120 dias) para cada academia...');

        $dataInicio = Carbon::now()->subDays(120);
        $dataFim = Carbon::now();
        $diaAtual = $dataInicio->copy();

        $totalDias = 120;
        $progressBar = $this->command->getOutput()->createProgressBar($totalDias * count($this->dadosPorAcademia));
        $progressBar->start();

        while ($diaAtual->lte($dataFim)) {
            foreach ($this->dadosPorAcademia as $academiaId => $dados) {
                $this->carregarContextoAcademia($dados);
                $this->processarDia($diaAtual);
                $progressBar->advance();
            }
            $diaAtual->addDay();
        }

        $progressBar->finish();
        $this->command->newLine();
        $this->command->info('  ✓ Operações diárias simuladas para todas as academias');
    }

    private function carregarContextoAcademia(array $dados): void
    {
        $this->academiaAtual = $dados['academia'];
        $this->adminAtual = $dados['admin'];
        $this->funcionarioAtual = $dados['funcionario'];
        $this->clientesAtual = $dados['clientes'];
        $this->produtosAtual = $dados['produtos'];
        $this->planosAtual = $dados['planos'];
        $this->fornecedoresAtual = $dados['fornecedores'];
        $this->equipamentosAtual = $dados['equipamentos'];
        $this->categoriasContaPagarAtual = $dados['categoriasContaPagar'];
    }

    private function processarDia(Carbon $data): void
    {
        $diaSemana = $data->dayOfWeek;
        $isFimDeSemana = in_array($diaSemana, [0, 6]);

        $numEntradas = $isFimDeSemana ? rand(5, 12) : rand(10, 20);
        $this->gerarEntradas($data, $numEntradas);

        $numVendas = $isFimDeSemana ? rand(1, 4) : rand(3, 8);
        $this->gerarVendas($data, $numVendas);

        if (!$isFimDeSemana && rand(1, 100) <= 40) {
            $this->gerarCompra($data);
        }

        $this->processarMensalidades($data);

        if ($data->day == 1) {
            $this->gerarContasFixasMensais($data);
        }

        if (!$isFimDeSemana && rand(1, 100) <= 15) {
            $this->gerarManutencao($data);
        }

        if (rand(1, 100) <= 8) {
            $this->gerarNovoCliente($data);
        }
    }

    private function gerarEntradas(Carbon $data, int $quantidade): void
    {
        $clientesAtivos = $this->clientesAtual->where('status', 'Ativo');
        
        if ($clientesAtivos->isEmpty()) {
            return;
        }

        $clientesDoDia = $clientesAtivos->random(min($quantidade, $clientesAtivos->count()));

        foreach ($clientesDoDia as $cliente) {
            $hora = rand(6, 21);
            $minuto = rand(0, 59);
            $dataHora = $data->copy()->setTime($hora, $minuto);

            Entrada::create([
                'idCliente' => $cliente->idCliente,
                'idAcademia' => $this->academiaAtual->idAcademia,
                'dataHora' => $dataHora,
                'metodo' => fake()->randomElement(['Reconhecimento Facial', 'Reconhecimento Facial', 'CPF/Senha', 'Manual']),
            ]);
        }
    }

    private function gerarVendas(Carbon $data, int $quantidade): void
    {
        for ($i = 0; $i < $quantidade; $i++) {
            $cliente = $this->clientesAtual->random();
            $hora = rand(6, 21);
            $minuto = rand(0, 59);
            $dataHora = $data->copy()->setTime($hora, $minuto);

            $numItens = rand(1, 4);
            $produtosSelecionados = $this->produtosAtual->random(min($numItens, $this->produtosAtual->count()));
            
            $valorTotal = 0;
            $itensVenda = [];

            foreach ($produtosSelecionados as $produto) {
                if ($produto->estoque <= 0) {
                    continue;
                }

                $qtd = rand(1, min(3, $produto->estoque));
                $subtotal = $qtd * $produto->preco;
                $valorTotal += $subtotal;

                $itensVenda[] = [
                    'idProduto' => $produto->idProduto,
                    'quantidade' => $qtd,
                    'precoUnitario' => $produto->preco,
                ];

                $produto->estoque -= $qtd;
                $produto->save();
            }

            if (empty($itensVenda)) {
                continue;
            }

            $venda = VendaProduto::create([
                'idCliente' => $cliente->idCliente,
                'idAcademia' => $this->academiaAtual->idAcademia,
                'idUsuario' => $this->funcionarioAtual->idUsuario,
                'dataVenda' => $dataHora,
                'valorTotal' => $valorTotal,
                'formaPagamento' => fake()->randomElement($this->formasPagamento),
            ]);

            foreach ($itensVenda as $item) {
                ItemVenda::create([
                    'idVenda' => $venda->idVenda,
                    'idProduto' => $item['idProduto'],
                    'quantidade' => $item['quantidade'],
                    'precoUnitario' => $item['precoUnitario'],
                ]);

                MovimentacaoEstoque::create([
                    'idAcademia' => $this->academiaAtual->idAcademia,
                    'idProduto' => $item['idProduto'],
                    'tipo' => 'saida',
                    'quantidade' => $item['quantidade'],
                    'custoUnitario' => $item['precoUnitario'],
                    'custoTotal' => $item['quantidade'] * $item['precoUnitario'],
                    'origem' => 'venda',
                    'referenciaId' => $venda->idVenda,
                    'motivo' => 'Venda de produto',
                    'dataMovimentacao' => $dataHora,
                    'usuarioId' => $this->funcionarioAtual->idUsuario,
                ]);
            }
        }
    }

    private function gerarCompra(Carbon $data): void
    {
        $fornecedor = $this->fornecedoresAtual->random();
        
        $produtosBaixoEstoque = $this->produtosAtual->filter(function ($produto) {
            return $produto->estoque <= $produto->estoqueMinimo * 1.5;
        });

        if ($produtosBaixoEstoque->isEmpty()) {
            $produtosBaixoEstoque = $this->produtosAtual->random(rand(2, 5));
        }

        $valorProdutos = 0;
        $itensCompra = [];

        foreach ($produtosBaixoEstoque as $produto) {
            $quantidade = rand(10, 30);
            $precoUnitario = $produto->precoCompra;
            $subtotal = $quantidade * $precoUnitario;
            $valorProdutos += $subtotal;

            $itensCompra[] = [
                'idProduto' => $produto->idProduto,
                'quantidade' => $quantidade,
                'precoUnitario' => $precoUnitario,
            ];
        }

        $valorFrete = rand(0, 100) > 70 ? round(rand(50, 150), 2) : 0;
        $valorDesconto = round($valorProdutos * (rand(0, 5) / 100), 2);
        $valorImpostos = round($valorProdutos * 0.08, 2);
        $valorTotal = $valorProdutos + $valorFrete + $valorImpostos - $valorDesconto;

        $compra = Compra::create([
            'idAcademia' => $this->academiaAtual->idAcademia,
            'idFornecedor' => $fornecedor->idFornecedor,
            'dataEmissao' => $data,
            'status' => 'recebida',
            'valorProdutos' => $valorProdutos,
            'valorFrete' => $valorFrete,
            'valorDesconto' => $valorDesconto,
            'valorImpostos' => $valorImpostos,
            'valorTotal' => $valorTotal,
            'observacoes' => null,
        ]);

        foreach ($itensCompra as $item) {
            ItemCompra::create([
                'idCompra' => $compra->idCompra,
                'idProduto' => $item['idProduto'],
                'quantidade' => $item['quantidade'],
                'precoUnitario' => $item['precoUnitario'],
                'descontoPercent' => 0,
                'custoRateadoTotal' => 0,
            ]);

            $produto = $this->produtosAtual->firstWhere('idProduto', $item['idProduto']);
            if ($produto) {
                $produto->estoque += $item['quantidade'];
                $produto->save();
            }

            MovimentacaoEstoque::create([
                'idAcademia' => $this->academiaAtual->idAcademia,
                'idProduto' => $item['idProduto'],
                'tipo' => 'entrada',
                'quantidade' => $item['quantidade'],
                'custoUnitario' => $item['precoUnitario'],
                'custoTotal' => $item['quantidade'] * $item['precoUnitario'],
                'origem' => 'compra',
                'referenciaId' => $compra->idCompra,
                'motivo' => 'Compra de fornecedor',
                'dataMovimentacao' => $data,
                'usuarioId' => $this->funcionarioAtual->idUsuario,
            ]);
        }

        $categoriaFornecedor = $this->categoriasContaPagarAtual->firstWhere('nome', 'Fornecedores');
        $dataVencimento = $data->copy()->addDays(30);

        ContaPagar::create([
            'idAcademia' => $this->academiaAtual->idAcademia,
            'idFornecedor' => $fornecedor->idFornecedor,
            'idCategoriaContaPagar' => $categoriaFornecedor->idCategoriaContaPagar,
            'documentoRef' => $compra->idCompra,
            'descricao' => 'Compra de produtos',
            'valorTotal' => $valorTotal,
            'status' => $dataVencimento->lt(Carbon::now()) ? 'paga' : 'aberta',
            'dataVencimento' => $dataVencimento,
            'dataPagamento' => $dataVencimento->lt(Carbon::now()) ? $dataVencimento : null,
            'formaPagamento' => $dataVencimento->lt(Carbon::now()) ? 'Boleto' : null,
        ]);
    }

    private function processarMensalidades(Carbon $data): void
    {
        foreach ($this->clientesAtual as $cliente) {
            $mensalidadeAtual = Mensalidade::where('idCliente', $cliente->idCliente)
                ->whereMonth('dataVencimento', $data->month)
                ->whereYear('dataVencimento', $data->year)
                ->first();

            if (!$mensalidadeAtual) {
                $plano = PlanoAssinatura::find($cliente->idPlano);
                if (!$plano) continue;

                $dataVencimento = $data->copy()->day(10);
                
                $status = 'Pendente';
                $dataPagamento = null;
                $formaPagamento = null;

                if ($data->gt($dataVencimento) && rand(1, 100) <= 70) {
                    $status = 'Paga';
                    $dataPagamento = $dataVencimento->copy()->addDays(rand(-5, 10));
                    $formaPagamento = fake()->randomElement($this->formasPagamento);
                }

                Mensalidade::create([
                    'idCliente' => $cliente->idCliente,
                    'idPlano' => $plano->idPlano,
                    'idAcademia' => $this->academiaAtual->idAcademia,
                    'dataVencimento' => $dataVencimento,
                    'valor' => $plano->valor,
                    'status' => $status,
                    'dataPagamento' => $dataPagamento,
                    'formaPagamento' => $formaPagamento,
                ]);

                if ($status === 'Paga') {
                    ContaReceber::create([
                        'idAcademia' => $this->academiaAtual->idAcademia,
                        'idCliente' => $cliente->idCliente,
                        'descricao' => 'Mensalidade ' . $data->format('m/Y') . ' - ' . $plano->nome,
                        'valorTotal' => $plano->valor,
                        'status' => 'recebida',
                        'dataVencimento' => $dataVencimento,
                        'dataRecebimento' => $dataPagamento,
                        'formaRecebimento' => $formaPagamento,
                    ]);
                }
            }

            $mensalidadesPendentes = Mensalidade::where('idCliente', $cliente->idCliente)
                ->where('status', 'Pendente')
                ->where('dataVencimento', '<=', $data)
                ->get();

            foreach ($mensalidadesPendentes as $mensalidade) {
                if (rand(1, 100) <= 20) {
                    $mensalidade->status = 'Paga';
                    $mensalidade->dataPagamento = $data;
                    $mensalidade->formaPagamento = fake()->randomElement($this->formasPagamento);
                    $mensalidade->save();

                    ContaReceber::create([
                        'idAcademia' => $this->academiaAtual->idAcademia,
                        'idCliente' => $cliente->idCliente,
                        'descricao' => 'Mensalidade paga - ' . $mensalidade->dataVencimento->format('m/Y'),
                        'valorTotal' => $mensalidade->valor,
                        'status' => 'recebida',
                        'dataVencimento' => $mensalidade->dataVencimento,
                        'dataRecebimento' => $data,
                        'formaRecebimento' => $mensalidade->formaPagamento,
                    ]);
                }
            }
        }
    }

    private function gerarContasFixasMensais(Carbon $data): void
    {
        $contasFixas = [
            ['categoria' => 'Aluguel', 'descricao' => 'Aluguel do imóvel', 'valor' => 5000.00, 'diaVenc' => 5],
            ['categoria' => 'Energia Elétrica', 'descricao' => 'Conta de energia', 'valor' => rand(800, 1500), 'diaVenc' => 15],
            ['categoria' => 'Água', 'descricao' => 'Conta de água', 'valor' => rand(200, 400), 'diaVenc' => 15],
            ['categoria' => 'Internet', 'descricao' => 'Serviço de internet', 'valor' => 299.90, 'diaVenc' => 10],
            ['categoria' => 'Telefone', 'descricao' => 'Telefone fixo', 'valor' => 89.90, 'diaVenc' => 10],
        ];

        foreach ($contasFixas as $conta) {
            $categoria = $this->categoriasContaPagarAtual->firstWhere('nome', $conta['categoria']);
            $dataVencimento = $data->copy()->day($conta['diaVenc']);
            
            $isPaga = $dataVencimento->lt(Carbon::now());

            ContaPagar::create([
                'idAcademia' => $this->academiaAtual->idAcademia,
                'idFornecedor' => null,
                'idCategoriaContaPagar' => $categoria ? $categoria->idCategoriaContaPagar : null,
                'descricao' => $conta['descricao'] . ' - ' . $data->format('m/Y'),
                'valorTotal' => $conta['valor'],
                'status' => $isPaga ? 'paga' : 'aberta',
                'dataVencimento' => $dataVencimento,
                'dataPagamento' => $isPaga ? $dataVencimento : null,
                'formaPagamento' => $isPaga ? 'Boleto' : null,
            ]);
        }

        if ($data->day == 1) {
            $categoriaSalario = $this->categoriasContaPagarAtual->firstWhere('nome', 'Salários');
            $dataVencimento = $data->copy()->day(5);
            $isPaga = $dataVencimento->lt(Carbon::now());

            ContaPagar::create([
                'idAcademia' => $this->academiaAtual->idAcademia,
                'idFornecedor' => null,
                'idFuncionario' => $this->funcionarioAtual->idUsuario,
                'idCategoriaContaPagar' => $categoriaSalario ? $categoriaSalario->idCategoriaContaPagar : null,
                'descricao' => 'Salário funcionário - ' . $this->funcionarioAtual->nome,
                'valorTotal' => $this->funcionarioAtual->salarioMensal,
                'status' => $isPaga ? 'paga' : 'aberta',
                'dataVencimento' => $dataVencimento,
                'dataPagamento' => $isPaga ? $dataVencimento : null,
                'formaPagamento' => $isPaga ? 'Transferência' : null,
            ]);
        }
    }

    private function gerarManutencao(Carbon $data): void
    {
        $equipamento = $this->equipamentosAtual->random();
        $fornecedor = $this->fornecedoresAtual->random();

        $tipos = ['preventiva', 'preventiva', 'corretiva'];
        $tipo = fake()->randomElement($tipos);

        $custo = $tipo === 'corretiva' ? rand(200, 1500) : rand(100, 500);

        ManutencaoEquipamento::create([
            'idEquipamento' => $equipamento->idEquipamento,
            'tipo' => $tipo,
            'dataSolicitacao' => $data,
            'dataProgramada' => $data->copy()->addDays(rand(1, 7)),
            'dataExecucao' => rand(1, 100) <= 60 ? $data : null,
            'descricao' => $tipo === 'preventiva' ? 'Manutenção preventiva programada' : 'Reparo necessário',
            'servicoRealizado' => rand(1, 100) <= 60 ? 'Serviço realizado conforme solicitado' : null,
            'custo' => rand(1, 100) <= 60 ? $custo : null,
            'fornecedorId' => $fornecedor->idFornecedor,
            'responsavel' => $this->funcionarioAtual->nome,
            'status' => rand(1, 100) <= 60 ? 'Concluída' : 'Pendente',
        ]);
    }

    private function gerarNovoCliente(Carbon $data): void
    {
        $plano = $this->planosAtual->random();
        
        $cliente = Cliente::create([
            'nome' => fake('pt_BR')->name(),
            'cpf' => $this->gerarCpf(),
            'dataNascimento' => fake()->dateTimeBetween('-50 years', '-18 years')->format('Y-m-d'),
            'telefone' => sprintf('(%02d) 9%04d-%04d', rand(11, 99), rand(1000, 9999), rand(1000, 9999)),
            'email' => fake('pt_BR')->unique()->safeEmail(),
            'codigo_acesso' => str_pad((string) (($this->academiaAtual->idAcademia * 1000) + $this->clientesAtual->count() + 1), 6, '0', STR_PAD_LEFT),
            'status' => 'Ativo',
            'idAcademia' => $this->academiaAtual->idAcademia,
            'idPlano' => $plano->idPlano,
            'idUsuario' => $this->funcionarioAtual->idUsuario,
        ]);

        $this->clientesAtual->push($cliente);
        $this->dadosPorAcademia[$this->academiaAtual->idAcademia]['clientes'] = $this->clientesAtual;

        $dataVencimento = $data->copy()->addMonth()->day(10);
        
        Mensalidade::create([
            'idCliente' => $cliente->idCliente,
            'idPlano' => $plano->idPlano,
            'idAcademia' => $this->academiaAtual->idAcademia,
            'dataVencimento' => $dataVencimento,
            'valor' => $plano->valor,
            'status' => 'Pendente',
            'dataPagamento' => null,
            'formaPagamento' => null,
        ]);
    }

    private function gerarCpf(): string
    {
        return sprintf(
            '%03d%03d%03d%02d',
            rand(100, 999),
            rand(100, 999),
            rand(100, 999),
            rand(10, 99)
        );
    }
}
