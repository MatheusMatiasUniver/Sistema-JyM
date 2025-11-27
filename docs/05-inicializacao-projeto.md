# Inicialização do Projeto

Este documento descreve os passos necessários para inicializar e executar o Sistema JyM.

---

## Pré-requisitos

Antes de iniciar o projeto, certifique-se de ter instalado:

- **PHP** >= 8.2
- **Composer** >= 2.x
- **Node.js** >= 18.x
- **NPM** >= 9.x
- **MySQL** >= 8.0 ou **PostgreSQL** >= 15

---

## Instalação Inicial

### 1. Clonar o Repositório

```bash
git clone https://github.com/MatheusMatiasUniver/Sistema-JyM.git
cd Sistema-JyM
```

### 2. Instalar Dependências PHP

```bash
composer install
```

### 3. Instalar Dependências JavaScript

```bash
npm install
```

### 4. Configurar Ambiente

Copie o arquivo de exemplo e configure as variáveis de ambiente:

```bash
cp .env.example .env
```

Edite o arquivo `.env` com as configurações do seu ambiente:

```env
APP_NAME="Sistema JyM"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sistema_jym
DB_USERNAME=root
DB_PASSWORD=

REVERB_APP_ID=
REVERB_APP_KEY=
REVERB_APP_SECRET=
```

### 5. Gerar Chave da Aplicação

```bash
php artisan key:generate
```

### 6. Executar Migrations

```bash
php artisan migrate
```

### 7. (Opcional) Popular Banco de Dados

```bash
php artisan db:seed
```

---

## Iniciar os Servidores

Para o projeto funcionar corretamente, **inicie os seguintes servidores**:

### Terminal 1: Servidor PHP (Laravel)

```bash
php artisan serve
```

O servidor Laravel estará disponível em: `http://localhost:8000`

### Terminal 2: Servidor WebSocket (Reverb)

```bash
php artisan reverb:start
```

O servidor Reverb gerencia as conexões WebSocket para funcionalidades em tempo real.

### Terminal 3: Servidor de Assets (Vite)

```bash
npm run dev
```

O Vite compila e serve os assets (CSS, JavaScript) em modo de desenvolvimento.

---

## Scripts Disponíveis

### PHP/Laravel

| Comando | Descrição |
|---------|-----------|
| `php artisan serve` | Inicia o servidor de desenvolvimento |
| `php artisan migrate` | Executa as migrations |
| `php artisan migrate:fresh` | Recria o banco de dados |
| `php artisan db:seed` | Popula o banco com dados de teste |
| `php artisan test` | Executa os testes |
| `php artisan reverb:start` | Inicia o servidor WebSocket |

### NPM

| Comando | Descrição |
|---------|-----------|
| `npm run dev` | Inicia o Vite em modo desenvolvimento |
| `npm run build` | Compila os assets para produção |

---

## Verificação da Instalação

Após iniciar todos os servidores, verifique se o sistema está funcionando:

1. Acesse `http://localhost:8000` no navegador
2. Verifique se não há erros no console
3. Teste o login/registro se disponível

---

## Problemas Comuns

### Erro: "Could not find driver"

Instale a extensão PDO do PHP para seu banco de dados:

```bash
# Para MySQL
sudo apt install php-mysql

# Para PostgreSQL
sudo apt install php-pgsql
```

### Erro: "npm command not found"

Instale o Node.js e NPM:

```bash
# Ubuntu/Debian
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs
```

### Erro: "SQLSTATE[HY000] [2002]"

Verifique se o serviço de banco de dados está rodando:

```bash
# MySQL
sudo systemctl start mysql

# PostgreSQL
sudo systemctl start postgresql
```

---

## Ambiente de Produção

Para produção, utilize:

```bash
# Compilar assets
npm run build

# Otimizar Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Iniciar com supervisor ou systemd
# Consulte a documentação de deployment do Laravel
```

---

## Referências

- [Laravel Installation](https://laravel.com/docs/installation)
- [Vite with Laravel](https://laravel.com/docs/vite)
- [Laravel Reverb](https://laravel.com/docs/reverb)
