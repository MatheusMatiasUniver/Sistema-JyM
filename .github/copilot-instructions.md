# GitHub Copilot Instructions - Sistema JyM

Sistema de gestão multi-academia com reconhecimento facial, controle de acesso e módulos financeiros.

## Arquitetura e Stack

- **Backend:** Laravel 12, PHP 8.2
- **Frontend:** Blade + Tailwind CSS + Vite, face-api.js (reconhecimento facial)
- **Tempo Real:** Laravel Reverb + Pusher (WebSockets para dashboard e kiosk)
- **Banco de Dados:** MySQL (padrão), suporte a SQLite para testes

### Inicialização do Projeto
```sh
php artisan serve
php artisan reverb:start
npm run dev
```
Ou use: `composer dev` (executa todos os servidores via concurrently)

## Padrão Multi-Academia

Todos os dados são **segregados por academia**. Use `config('app.academia_atual')` ou `session('academia_selecionada')` para obter o contexto.

```php
// Exemplo: filtrar dados pela academia ativa
$clientes = Cliente::where('idAcademia', config('app.academia_atual'))->get();

// Em queries condicionais no Dashboard
->when($academiaId, fn($q) => $q->where('idAcademia', $academiaId))
```

**Middlewares críticos:**
- `AcademiaContext` - Define academia ativa na sessão/config
- `admin` - Somente administradores
- `funcionario` - Funcionários e administradores

## Convenções de Código

### Nomenclatura
- **camelCase**: variáveis e métodos (`userName`, `registrarEntrada()`)
- **PascalCase**: classes (`ClienteController`, `PlanoAssinatura`)
- **UPPER_SNAKE_CASE**: constantes

### Models Eloquent
- Primary keys customizadas: `idCliente`, `idAcademia`, `idPlano`
- Usar `SoftDeletes` para exclusão lógica (ver `Cliente.php`)
- Definir `$table`, `$primaryKey`, `$fillable`, `$casts` explicitamente

### Form Requests
Validação em classes separadas (`app/Http/Requests/`):
```php
// Exemplo: StoreClienteRequest
'cpf' => ['required', 'string', 'size:11', 'regex:/^[0-9]{11}$/', Rule::unique('clientes', 'cpf')],
```

### Services
Lógica de negócio complexa em `app/Services/`:
- `EntradaService` - Registra acessos e dispara eventos de dashboard

## Broadcasting e Tempo Real

Eventos para atualização em tempo real (kiosk e dashboard):
```php
event(new DashboardUpdated('entrada'));
event(new KioskStatusChanged($isRegistering, $message));
```

Canais públicos:
- `kiosk-status` - Status do modo de registro facial
- `dashboard` - Atualizações de métricas

## Rate Limiting

Configurado em `AppServiceProvider`:
- `login` - 10 req/min por usuário+IP
- `face` - 30 req/min por IP (endpoints de reconhecimento facial)

Aplicar via middleware: `->middleware('throttle:face')`

## Estrutura de Rotas

- Rotas públicas: apenas login (`/`, `/login`)
- Rotas autenticadas: agrupadas em `middleware('auth')`
- Recursos CRUD: usar `Route::resource()` com middleware apropriado

```php
Route::resource('clientes', ClienteController::class)->middleware('funcionario');
Route::resource('academias', AcademiaController::class)->middleware('admin');
```

## Fluxo de Mensalidades e Status do Cliente

O sistema gerencia automaticamente o status do cliente baseado em mensalidades:

**Status possíveis:** `Ativo`, `Inativo`, `Inadimplente`

**Job automático** (`VerificarMensalidadesVencidas`):
- Verifica diariamente clientes `Ativo` com mensalidades `Pendente` vencidas
- Atualiza automaticamente para `Inadimplente`

```php
// Verificação de acesso no Kiosk (FaceRecognitionController)
if (!in_array($cliente->status, ['Ativo', 'Inadimplente'])) {
    return response()->json(['success' => false, 'message' => 'Acesso negado']);
}
```

**Ao pagar mensalidade:** status volta para `Ativo` automaticamente.

## Activity Logs (Auditoria)

Registrar ações críticas usando `ActivityLog::create()`:

```php
\App\Models\ActivityLog::create([
    'usuarioId' => Auth::id(),
    'modulo' => 'Vendas',           // Nome do módulo
    'acao' => 'criar',              // criar, atualizar, cancelar, etc.
    'entidade' => 'VendaProduto',   // Nome da Model
    'entidadeId' => $vendaId,
    'dados' => [                    // Dados relevantes em array
        'valorTotal' => $valorTotal,
        'formaPagamento' => $formaPagamento,
    ],
]);
```

**Módulos que já logam:** Vendas, Produtos, Usuários, ContasPagar, ReconhecimentoFacial, AjustesSistema.

## Relatórios PDF

Templates em `resources/views/relatorios/pdf/`. Use DomPDF:

```php
use Barryvdh\DomPDF\Facade\Pdf;

return Pdf::loadView('relatorios.pdf.faturamento', $dados)->download('relatorio.pdf');
```

**Header padrão para novos relatórios PDF:**
```html
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18px; }
        .header .academia { font-size: 14px; color: #666; }
        .header .data { font-size: 10px; color: #999; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $tituloRelatorio }}</h1>
        <div class="academia">{{ $nomeAcademia ?? 'Sistema JyM' }}</div>
        <div class="data">Gerado em: {{ now()->format('d/m/Y H:i') }}</div>
    </div>
    <!-- Conteúdo do relatório -->
</body>
</html>
```

## Testes

```sh
composer test  # ou: php artisan test
```

- Testes em `tests/Feature/` e `tests/Unit/`
- PHPUnit 11 + Mockery

## Referências Importantes

| Módulo | Arquivos Chave |
|--------|----------------|
| Autenticação | `AuthController.php`, `AdminMiddleware.php`, `FuncionarioMiddleware.php` |
| Multi-Academia | `AcademiaContext.php`, `AcademiaController.php` |
| Reconhecimento Facial | `FaceRecognitionController.php`, `FaceDescriptor.php`, `face-api.js` |
| Dashboard Tempo Real | `DashboardController.php`, `DashboardUpdated.php` |
| Clientes | `ClienteController.php`, `StoreClienteRequest.php`, `Cliente.php` |
| Mensalidades | `MensalidadeController.php`, `VerificarMensalidadesVencidas.php` |
| Relatórios PDF | `RelatorioController.php`, `resources/views/relatorios/pdf/` |
| Auditoria | `ActivityLog.php` |
