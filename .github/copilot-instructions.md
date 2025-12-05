# GitHub Copilot Instructions - Sistema JyM

Sistema multi-tenant de gestão de academias com reconhecimento facial, controle de acesso e módulos financeiros.

## Regras para o Agente

- **Sempre aplicar as instruções deste arquivo** ao modificar ou criar código neste projeto.
- **Não adicionar comentários no código gerado** — o código deve ser autoexplicativo.
- Seguir rigorosamente as convenções e padrões definidos abaixo.

## Stack e Comandos

**Stack:** Laravel 12 + PHP 8.2 | Blade + Tailwind + Vite | Laravel Reverb (WebSockets) | MySQL

```sh
composer dev          # Inicia todos os servidores (serve, queue, pail, vite)
composer test         # Roda PHPUnit
php artisan db:seed --class=SimulationSeeder  # Popula 4 meses de dados simulados
```

## Arquitetura Multi-Academia (CRÍTICO)

**Toda query deve filtrar por academia.** O middleware `AcademiaContext` define `config('app.academia_atual')` baseado no usuário logado.

```php
// SEMPRE usar este padrão em queries
$academiaId = config('app.academia_atual');
$clientes = Cliente::where('idAcademia', $academiaId)->get();

// Para queries condicionais (ex: admin pode ver todas)
->when($academiaId, fn($q) => $q->where('idAcademia', $academiaId))
```

**Middlewares de autorização:**
- `admin` → Somente administradores (podem trocar academia via `academia.trocar`)
- `funcionario` → Funcionários + administradores (academia fixa do `$user->idAcademia`)

## Convenções do Projeto

### Models Eloquent
- **Primary keys customizadas:** `idCliente`, `idAcademia`, `idPlano` (não `id`)
- **SoftDeletes** em entidades críticas (ver `Cliente.php`)
- Sempre definir: `$table`, `$primaryKey`, `$fillable`, `$casts`

### Validação
Usar Form Requests em `app/Http/Requests/`. CPF é armazenado sem formatação (11 dígitos):
```php
'cpf' => ['required', 'size:11', 'regex:/^[0-9]{11}$/', Rule::unique('clientes', 'cpf')],
```

### Services
Lógica complexa em `app/Services/`. Exemplo: `EntradaService` registra acesso + dispara `DashboardUpdated`.

## Broadcasting (Tempo Real)

Eventos para atualizar Kiosk e Dashboard sem refresh:
```php
event(new DashboardUpdated('entrada'));      // Canal: dashboard
event(new KioskStatusChanged($status, $msg)); // Canal: kiosk-status
```

## Fluxo de Mensalidades

| Status Cliente | Condição |
|----------------|----------|
| `Ativo` | Mensalidades em dia |
| `Inadimplente` | Job `VerificarMensalidadesVencidas` atualiza diariamente |
| `Inativo` | Definido manualmente |

**Acesso no Kiosk:** Permitido apenas para `Ativo`. `Inadimplente` e `Inativo` = acesso negado.

## Relatórios PDF

Templates em `resources/views/relatorios/pdf/`. Usar DomPDF com fonte `DejaVu Sans`:
```php
return Pdf::loadView('relatorios.pdf.faturamento', $dados)->download('relatorio.pdf');
```

## Rate Limiting

Aplicar em endpoints sensíveis:
- `throttle:login` → 10 req/min (autenticação)
- `throttle:face` → 30 req/min (reconhecimento facial)

## Arquivos de Referência

| Conceito | Arquivo |
|----------|---------|
| Contexto multi-academia | `app/Http/Middleware/AcademiaContext.php` |
| Registro facial | `app/Http/Controllers/FaceRecognitionController.php` |
| Eventos tempo real | `app/Events/DashboardUpdated.php`, `KioskStatusChanged.php` |
| Serviços de negócio | `app/Services/EntradaService.php`, `VendaService.php` |
| Validação exemplo | `app/Http/Requests/StoreClienteRequest.php` |
| Job automático | `app/Jobs/VerificarMensalidadesVencidas.php` |
| Dados de teste | `database/seeders/SimulationSeeder.php` |
