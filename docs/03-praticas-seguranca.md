# Práticas de Segurança

Este documento define as práticas de segurança que devem ser seguidas no desenvolvimento do Sistema JyM.

---

## Armazenamento de Senhas

### ❌ Nunca armazene senhas em texto plano

```php
// ERRADO - Nunca faça isso!
$user->password = $request->password;
```

### ✅ Utilize bcrypt ou argon2 para hash de senhas

```php
// CORRETO - Use Hash facade do Laravel
use Illuminate\Support\Facades\Hash;

$user->password = Hash::make($request->password);

// Ou usando bcrypt diretamente
$user->password = bcrypt($request->password);
```

### Verificação de Senhas

```php
if (Hash::check($request->password, $user->password)) {
    // Senha correta
}
```

---

## Rate Limiting

Implemente **rate limiting** em endpoints públicos para evitar ataques de força bruta.

### Configuração no Laravel

```php
// routes/api.php
Route::middleware('throttle:60,1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});
```

### Configuração Personalizada

```php
// app/Providers/RouteServiceProvider.php
RateLimiter::for('login', function (Request $request) {
    return Limit::perMinute(5)->by($request->ip());
});
```

---

## Prevenção de SQL Injection

### ✅ Sempre valide e sanitize todos os inputs

```php
// CORRETO - Use Eloquent ORM ou Query Builder
$user = User::where('email', $request->email)->first();

// CORRETO - Use prepared statements
DB::select('SELECT * FROM users WHERE email = ?', [$email]);
```

### ❌ Nunca concatene strings em queries

```php
// ERRADO - Vulnerável a SQL Injection!
DB::select("SELECT * FROM users WHERE email = '" . $email . "'");
```

---

## CORS (Cross-Origin Resource Sharing)

Configure CORS adequadamente para proteger contra ataques cross-origin.

### Configuração em `config/cors.php`

```php
return [
    'paths' => ['api/*'],
    'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE'],
    'allowed_origins' => ['http://localhost:8000', 'https://seudominio.com'],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['Content-Type', 'Authorization', 'X-Requested-With'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
```

---

## Autenticação e Autorização

### Autenticação com Laravel Sanctum

```php
// Protegendo rotas com autenticação
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
```

### Autorização com Policies

```php
// Verificar permissão antes de executar ação
public function update(Request $request, Cliente $cliente)
{
    $this->authorize('update', $cliente);
    
    // Atualizar cliente...
}
```

---

## HTTPS

### ✅ Utilize HTTPS em todas as comunicações

Configure o Laravel para forçar HTTPS em produção:

```php
// app/Providers/AppServiceProvider.php
public function boot(): void
{
    if (config('app.env') === 'production') {
        URL::forceScheme('https');
    }
}
```

### Configuração no `.env`

```env
APP_URL=https://seudominio.com
```

---

## Monitoramento e Logs de Segurança

### ✅ Monitore e registre atividades suspeitas

```php
// Registrar tentativas de login falhas
Log::warning('Tentativa de login falha', [
    'email' => $request->email,
    'ip' => $request->ip(),
    'user_agent' => $request->userAgent(),
    'timestamp' => now(),
]);

// Registrar ações sensíveis
Log::info('Usuário alterou senha', [
    'user_id' => $user->id,
    'ip' => $request->ip(),
]);
```

---

## Checklist de Segurança

| Item | Descrição | Status |
|------|-----------|--------|
| Senhas | Hash com bcrypt/argon2 | ⬜ |
| Rate Limiting | Implementado em endpoints públicos | ⬜ |
| SQL Injection | Queries parametrizadas | ⬜ |
| CORS | Configurado corretamente | ⬜ |
| Autenticação | Implementada com Sanctum | ⬜ |
| Autorização | Policies configuradas | ⬜ |
| HTTPS | Forçado em produção | ⬜ |
| Logs | Atividades suspeitas registradas | ⬜ |

---

## Referências

- [Laravel Security](https://laravel.com/docs/security)
- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [Laravel Sanctum](https://laravel.com/docs/sanctum)
