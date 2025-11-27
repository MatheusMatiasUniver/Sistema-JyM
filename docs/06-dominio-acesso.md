# Domínio de Acesso

Este documento define as configurações de acesso ao Sistema JyM em diferentes ambientes.

---

## Ambiente de Desenvolvimento

### URL Principal

O acesso ao sistema em ambiente de desenvolvimento deve ser feito via:

```
http://localhost:8000
```

### URLs dos Serviços

| Serviço | URL | Descrição |
|---------|-----|-----------|
| Aplicação Principal | `http://localhost:8000` | Interface web do sistema |
| API | `http://localhost:8000/api` | Endpoints da API REST |
| Vite Dev Server | `http://localhost:5173` | Servidor de assets (desenvolvimento) |
| Reverb WebSocket | `ws://localhost:8080` | Conexões WebSocket |

---

## Configuração no `.env`

### Desenvolvimento Local

```env
APP_URL=http://localhost:8000

REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_APP_URL="${APP_URL}"
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

### Produção

```env
APP_URL=https://seudominio.com

REVERB_HOST=seudominio.com
REVERB_PORT=443
REVERB_SCHEME=https

VITE_APP_URL="${APP_URL}"
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

---

## Estrutura de Rotas

### Rotas Web

Acessíveis via navegador em `http://localhost:8000`:

```php
// routes/web.php
Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::resource('/clientes', ClienteController::class);
    Route::resource('/produtos', ProdutoController::class);
    // ... outras rotas
});
```

### Rotas API

Acessíveis via `http://localhost:8000/api`:

```php
// routes/api.php
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn(Request $request) => $request->user());
    Route::apiResource('/clientes', Api\ClienteController::class);
    // ... outras rotas da API
});
```

---

## Acesso via Rede Local

Para acessar o sistema de outros dispositivos na mesma rede:

### 1. Inicie o servidor com bind em todas as interfaces

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

### 2. Descubra seu IP local

```bash
# Linux/Mac
ip addr show | grep "inet "

# Windows
ipconfig
```

### 3. Acesse via IP

```
http://192.168.1.XXX:8000
```

---

## Configuração de CORS

Para permitir acesso de outros domínios à API:

```php
// config/cors.php
return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'http://localhost:8000',
        'http://localhost:5173',
        // Adicione outros domínios conforme necessário
    ],
    'allowed_headers' => ['*'],
    'supports_credentials' => true,
];
```

---

## Certificados SSL (Produção)

Em produção, sempre utilize HTTPS:

### Configuração com Nginx

```nginx
server {
    listen 443 ssl http2;
    server_name seudominio.com;

    ssl_certificate /etc/letsencrypt/live/seudominio.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/seudominio.com/privkey.pem;

    root /var/www/sistema-jym/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

---

## Portas Utilizadas

| Porta | Serviço | Ambiente |
|-------|---------|----------|
| 8000 | Laravel (PHP) | Desenvolvimento |
| 5173 | Vite (Node.js) | Desenvolvimento |
| 8080 | Reverb (WebSocket) | Desenvolvimento |
| 80 | HTTP | Produção |
| 443 | HTTPS | Produção |
| 3306 | MySQL | Todos |
| 5432 | PostgreSQL | Todos |

---

## Troubleshooting

### Porta em uso

```bash
# Verificar processos usando a porta
lsof -i :8000

# Matar processo
kill -9 <PID>
```

### Firewall bloqueando acesso

```bash
# Ubuntu/Debian
sudo ufw allow 8000

# CentOS/RHEL
sudo firewall-cmd --add-port=8000/tcp --permanent
sudo firewall-cmd --reload
```

---

## Referências

- [Laravel Deployment](https://laravel.com/docs/deployment)
- [Nginx Configuration](https://laravel.com/docs/deployment#nginx)
