# Logs de Modificações

Este documento define as práticas de registro de logs de modificações no Sistema JyM.

---

## Visão Geral

**Sim**, todos os logs de modificações devem ser gerados para manter um histórico completo das alterações realizadas no sistema.

---

## Pasta de Logs

### Localização

Os logs de modificações devem ser armazenados na pasta:

```
.logs_modificacoes/
```

### Estrutura da Pasta

```
.logs_modificacoes/
├── 2024-01-15.log
├── 2024-01-16.log
├── seguranca/
│   └── 2024-01-15-seguranca.log
├── entrada-dados/
│   └── 2024-01-15-entrada.log
└── nomenclatura/
    └── 2024-01-15-nomenclatura.log
```

---

## Tipos de Logs

### 1. Alterações de Código

Registre todas as alterações de código que impactam:

- **Segurança** - Mudanças em autenticação, autorização, criptografia
- **Entrada de dados** - Alterações em validações, sanitização
- **Nomenclatura** - Refatorações de nomes de variáveis, funções, classes

### 2. Formato do Log

```log
[2024-01-15 10:30:45] INFO: Alteração em UserController.php
- Tipo: Segurança
- Descrição: Implementação de rate limiting no endpoint de login
- Autor: usuario@email.com
- Commit: abc123def

[2024-01-15 11:15:22] INFO: Alteração em ClienteRequest.php
- Tipo: Entrada de dados
- Descrição: Adicionada validação de CPF
- Autor: usuario@email.com
- Commit: def456ghi
```

---

## Implementação no Laravel

### Configuração de Canal de Log Personalizado

```php
// config/logging.php
'channels' => [
    // ... outros canais
    
    'modificacoes' => [
        'driver' => 'daily',
        'path' => storage_path('logs/modificacoes/modificacoes.log'),
        'level' => 'info',
        'days' => 90,
    ],
    
    'seguranca' => [
        'driver' => 'daily',
        'path' => storage_path('logs/modificacoes/seguranca.log'),
        'level' => 'info',
        'days' => 365,
    ],
],
```

### Uso no Código

```php
use Illuminate\Support\Facades\Log;

// Log de modificação geral
Log::channel('modificacoes')->info('Alteração em ClienteController', [
    'tipo' => 'entrada_dados',
    'descricao' => 'Adicionada validação de email',
    'autor' => auth()->user()->email ?? 'sistema',
    'arquivo' => 'app/Http/Controllers/ClienteController.php',
]);

// Log de segurança
Log::channel('seguranca')->warning('Alteração de senha realizada', [
    'user_id' => $user->id,
    'ip' => request()->ip(),
    'user_agent' => request()->userAgent(),
]);
```

---

## Model ActivityLog

O sistema já possui um modelo `ActivityLog` para registrar atividades:

```php
use App\Models\ActivityLog;

ActivityLog::create([
    'user_id' => auth()->id(),
    'action' => 'update',
    'model_type' => 'Cliente',
    'model_id' => $cliente->id,
    'changes' => json_encode($changes),
    'ip_address' => request()->ip(),
]);
```

---

## Boas Práticas

1. **Separação por Data** - Utilize arquivos separados por data para facilitar a busca.

2. **Separação por Funcionalidade** - Organize logs por tipo (segurança, entrada de dados, nomenclatura).

3. **Informações Essenciais** - Sempre inclua:
   - Data/hora
   - Tipo de alteração
   - Descrição
   - Autor
   - Arquivo/Commit relacionado

4. **Retenção** - Defina políticas de retenção de logs:
   - Logs gerais: 90 dias
   - Logs de segurança: 365 dias

5. **Não Logar Dados Sensíveis** - Nunca registre senhas, tokens ou dados pessoais sensíveis nos logs.

---

## Integração com Git

Considere integrar os logs com o histórico do Git:

```bash
# Exemplo de script para gerar log de commit
git log --oneline --since="1 day ago" >> .logs_modificacoes/$(date +%Y-%m-%d)-commits.log
```

---

## Referências

- [Laravel Logging](https://laravel.com/docs/logging)
- [Modelo ActivityLog do Sistema](../app/Models/ActivityLog.php)
