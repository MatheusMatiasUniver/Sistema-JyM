# 🚀 Guia Rápido - Sistema JyM

Referência rápida para desenvolvedores que já conhecem o sistema.

---

## ⚡ Instalação Express (5 minutos)

```bash
# Clone e configure
git clone https://github.com/MatheusMatiasUniver/Sistema-JyM.git
cd Sistema-JyM
composer install && npm install
copy .env.example .env
php artisan key:generate

# Configure .env
# DB_DATABASE=jym, DB_USERNAME=root, DB_PASSWORD=

# Banco de dados (escolha uma opção)
# Opção A: SQL (mais rápido)
mysql -u root -p -e "CREATE DATABASE jym CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
mysql -u root -p jym < database/schema/estrutura_banco.sql
mysql -u root -p jym < database/schema/dados_seeders.sql

# Opção B: Laravel (mais flexível)
php artisan migrate
php artisan db:seed --class=SimulationSeeder

# Iniciar
composer dev
```

**Acesso:** http://localhost:8000  
**Login:** `admin` / `admin123`

---

## 🎯 Comandos Essenciais

### Desenvolvimento
```bash
composer dev              # Inicia tudo (serve + queue + pail + vite)
php artisan serve        # Apenas servidor web
php artisan reverb:start # WebSocket server
php artisan queue:listen # Processar jobs
npm run dev              # Assets frontend
```

### Banco de Dados
```bash
php artisan migrate                        # Rodar migrations
php artisan migrate:fresh --seed          # Resetar e popular
php artisan db:seed --class=SimulationSeeder # Dados de 4 meses
php artisan migrate:status                # Ver status
```

### Cache e Otimização
```bash
php artisan optimize:clear  # Limpar todos os caches
php artisan config:clear    # Limpar cache de config
php artisan view:clear      # Limpar views compiladas
php artisan cache:clear     # Limpar cache da aplicação
```

### Testes
```bash
composer test              # Rodar PHPUnit
php artisan test          # Laravel test runner
```

---

## 👥 Credenciais Padrão

| Tipo | Usuário | Senha | ID Academia | Acesso |
|------|---------|-------|-------------|--------|
| **Administrador** | `admin` | `admin123` | `null` | Todas academias |
| **Funcionário 1** | `maria.souza` | `func123` | `1` | Iron Fitness |
| **Funcionário 2** | `pedro.lima` | `func123` | `2` | Power House Gym |

---

## 📁 Estrutura de Pastas Importantes

```
app/
├── Http/
│   ├── Controllers/      # Controllers MVC
│   ├── Middleware/       # AcademiaContext (CRÍTICO)
│   └── Requests/         # Form Requests (validação)
├── Models/               # Eloquent Models
├── Services/             # Lógica de negócio
├── Events/               # Eventos Broadcasting
└── Jobs/                 # Jobs assíncronos

database/
├── migrations/           # Estrutura do banco
├── seeders/              # Dados de teste
└── schema/               # Scripts SQL prontos

resources/
├── views/                # Blade templates
│   ├── dashboard/        # Dashboard principal
│   ├── kiosk/            # Interface reconhecimento facial
│   └── relatorios/pdf/   # Templates de relatórios
└── js/                   # JavaScript/Alpine.js

routes/
├── web.php               # Rotas HTTP
└── channels.php          # Canais Broadcasting
```

---

## 🔑 Conceitos-Chave do Sistema

### 1. Multi-tenancy (Academia Context)

**TODA query deve filtrar por academia:**

```php
// SEMPRE usar
$academiaId = config('app.academia_atual');
$clientes = Cliente::where('idAcademia', $academiaId)->get();

// Para admin (pode ver todas)
->when($academiaId, fn($q) => $q->where('idAcademia', $academiaId))
```

**Middlewares:**
- `admin` → Apenas admins (podem trocar academia)
- `funcionario` → Funcionários + admins (academia fixa)

### 2. Primary Keys Customizadas

```php
// NÃO use "id", use o padrão do projeto:
Cliente::class → $primaryKey = 'idCliente'
Academia::class → $primaryKey = 'idAcademia'
Produto::class → $primaryKey = 'idProduto'
```

### 3. Broadcasting (Tempo Real)

```php
// Atualizar Dashboard
event(new DashboardUpdated('entrada'));

// Atualizar Kiosk
event(new KioskStatusChanged($status, $msg));
```

**Canais:**
- `dashboard` → Atualizações gerais
- `kiosk-status` → Status do reconhecimento facial

### 4. Jobs Automáticos

```php
VerificarMensalidadesVencidas // Diário (atualiza status clientes)
GerarSalariosMensais          // Mensal (dia 1)
```

---

## 🛠️ Tarefas Comuns

### Adicionar Nova Tabela
```bash
php artisan make:migration create_nome_tabela --create=nome
# Editar migration
php artisan migrate
```

### Criar Model + Controller + Request
```bash
php artisan make:model NomeModel
php artisan make:controller NomeController
php artisan make:request StoreNomeRequest
```

### Gerar PDF
```php
use Barryvdh\DomPDF\Facade\Pdf;

return Pdf::loadView('relatorios.pdf.nome', $dados)
    ->download('arquivo.pdf');
```

### Validar CPF (11 dígitos sem formatação)
```php
'cpf' => ['required', 'size:11', 'regex:/^[0-9]{11}$/'],
```

---

## 🐛 Debug Rápido

### Logs em Tempo Real
```bash
php artisan pail
```

### Consultar Banco via Tinker
```bash
php artisan tinker
>>> Cliente::count()
>>> User::where('usuario', 'admin')->first()
>>> DB::table('mensalidades')->count()
```

### Verificar Migrations
```bash
php artisan migrate:status
```

### Problemas com Broadcasting
```bash
# Verificar se Reverb está rodando
# URL: ws://localhost:8080
# Console do navegador deve mostrar conexão ativa
```

---

## 📊 Dados de Simulação

Executar `php artisan db:seed --class=SimulationSeeder` cria:

- **2 academias** (Iron Fitness, Power House Gym)
- **3 usuários** (1 admin, 2 funcionários)
- **56 clientes** (32 + 24)
- **10 planos** (5 por academia)
- **28 produtos**
- **20 equipamentos**
- **120 dias** de operação (entradas, vendas, mensalidades)

---

## 🔒 Segurança

### Rate Limiting
```php
Route::middleware('throttle:login')->group(...);  // 10 req/min
Route::middleware('throttle:face')->group(...);   // 30 req/min
```

### Validação de Inputs
```php
// SEMPRE usar Form Requests
StoreClienteRequest::class
UpdateProdutoRequest::class
```

### Senhas
```php
// Usar bcrypt (já configurado)
Hash::make('senha123')
Hash::check('senha123', $hash)
```

---

## 📖 Referências Rápidas

| Documentação | Link |
|--------------|------|
| **Instalação Completa** | [INSTALACAO.md](INSTALACAO.md) |
| **Scripts SQL** | [database/schema/README.md](database/schema/README.md) |
| **Documentação Técnica** | [docs/Documentação - Sistema JyM.md](docs/Documentação%20-%20Sistema%20JyM.md) |
| **Laravel 12 Docs** | https://laravel.com/docs/12.x |
| **Tailwind CSS** | https://tailwindcss.com/docs |
| **Alpine.js** | https://alpinejs.dev/start-here |

---

## ⚠️ Avisos Importantes

1. **Nunca** commite o `.env` (já está no .gitignore)
2. **Sempre** filtre por `idAcademia` em queries
3. **Use** Form Requests para validação
4. **Mantenha** Services para lógica complexa
5. **Documente** alterações em `CHANGELOG.md`
6. **Teste** antes de fazer push

---

## 🆘 Problemas Comuns - Soluções Rápidas

| Problema | Solução |
|----------|---------|
| Assets não carregam | `npm run build && php artisan optimize:clear` |
| Erro de conexão DB | Verificar `.env` e `php artisan config:clear` |
| WebSocket não conecta | `php artisan reverb:start` |
| Queue não processa | `php artisan queue:listen` |
| Permissões (Linux) | `chmod -R 775 storage bootstrap/cache` |
| Composer lento | `composer install --no-dev` (produção) |

---

**📅 Última atualização:** Dezembro 2025  
**👨‍💻 Desenvolvido por:** João Piaia & Matheus Almeida
