# 📘 Tutorial de Instalação - Sistema JyM

Sistema multi-tenant de gestão de academias com reconhecimento facial, controle de acesso e módulos financeiros.

---

## 📋 Requisitos do Sistema

### Software Necessário

1. **PHP 8.2 ou superior**
   - Download: https://www.php.net/downloads
   - Extensões necessárias: `pdo`, `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`, `gd`

2. **Composer** (Gerenciador de dependências PHP)
   - Download: https://getcomposer.org/download/

3. **Node.js 18+ e NPM** (Para frontend assets)
   - Download: https://nodejs.org/

4. **MySQL 8.0 ou superior**
   - Download: https://dev.mysql.com/downloads/mysql/

### Alternativa Simplificada (Recomendado para Windows)

**Laragon** - Ambiente de desenvolvimento completo (inclui PHP, MySQL, Apache, Node.js)
- Download: https://laragon.org/download/
- Versão recomendada: Full

---

## 🚀 Passo 1: Obter o Código do Sistema

Download Direto
1. Baixe o ZIP do repositório
2. Extraia para uma pasta de sua escolha
3. Navegue até a pasta extraída

---

## ⚙️ Passo 2: Instalar Dependências

### 2.1 Dependências PHP (Composer)
```bash
composer install
```

### 2.2 Dependências JavaScript (NPM)
```bash
npm install
```

---

## 🔧 Passo 3: Configurar o Arquivo `.env`

### 3.1 Criar o arquivo `.env`
Copie o arquivo de exemplo:
```bash
copy .env.example .env
```

### 3.2 Gerar chave da aplicação
```bash
php artisan key:generate
```

### 3.3 Configurar variáveis do banco de dados

Abra o arquivo `.env` e configure:

```env
# Configurações da Aplicação
APP_NAME=Sistema-JyM
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Configurações do Banco de Dados
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jym
DB_USERNAME=root
DB_PASSWORD=
```

- `DB_DATABASE`: Nome do banco que será criado
- `DB_USERNAME`: Usuário do MySQL (padrão: `root`)
- `DB_PASSWORD`: Senha do MySQL (deixe vazio se não tiver senha)

### 3.4 Configurar WebSockets (Laravel Reverb)

As seguintes configurações já vêm pré-configuradas no `.env.example`:

```env
BROADCAST_DRIVER=reverb
QUEUE_CONNECTION=database

REVERB_APP_ID=local-kiosk
REVERB_APP_KEY=local-kiosk-key
REVERB_APP_SECRET=local-kiosk-secret
REVERB_HOST=127.0.0.1
REVERB_PORT=8080
REVERB_SCHEME=http
```

**Não é necessário alterar essas configurações para desenvolvimento local.**

---

## 💾 Passo 4: Configurar o Banco de Dados

Escolha **UMA** das duas formas abaixo:

---

### 📌 FORMA 1: Importar Scripts SQL

Esta forma usa scripts SQL prontos para criar toda a estrutura e dados de exemplo.

#### 4.1.1 Criar o banco de dados

**Via linha de comando MySQL:**
```bash
mysql -u root -p
```

Dentro do MySQL:
```sql
CREATE DATABASE jym CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit;
```

**OU via phpMyAdmin:**
- Acesse o painel (geralmente `http://localhost/phpmyadmin`)
- Clique em "Novo"
- Nome: `jym`
- Collation: `utf8mb4_unicode_ci`
- Clique em "Criar"

#### 4.1.2 Importar estrutura do banco

```bash
mysql -u root -p jym < database/schema/estrutura_banco.sql
```

#### 4.1.3 Importar dados iniciais

```bash
mysql -u root -p jym < database/schema/dados_seeders.sql
```

**✅ Pronto! O banco está configurado com estrutura e dados.**

---

### 📌 FORMA 2: Usar Migrations e Seeders (Laravel Nativo)

Esta forma usa os comandos nativos do Laravel para criar tudo do zero.

#### 4.2.1 Criar o banco de dados

Siga o mesmo processo da **Forma 1 - 4.1.1** para criar o banco `jym`.

#### 4.2.2 Executar as migrations

```bash
php artisan migrate
```

Este comando criará todas as tabelas necessárias no banco de dados.

#### 4.2.3 Executar as seeders

```bash
php artisan db:seed --class=SimulationSeeder
```

Este comando populará o banco com:
- 2 academias de exemplo
- Usuários (admin e funcionários)
- Clientes (32 na primeira academia, 24 na segunda)
- Planos de assinatura
- Produtos, categorias, marcas e fornecedores
- Equipamentos e materiais
- 4 meses de dados simulados (entradas, vendas, mensalidades, etc.)

**✅ Pronto! O banco está configurado com estrutura e dados.**

---

## 👥 Passo 5: Credenciais de Acesso

Após configurar o banco (por qualquer uma das formas acima), utilize as seguintes credenciais:

### 🔑 Administrador (Acesso Total)
- **Usuário:** `admin`
- **Senha:** `admin123`
- **Permissões:** Acesso a todas as academias, pode trocar contexto

### 🏋️ Funcionários

#### Academia 1: Iron Fitness Academia
- **Usuário:** `maria.souza`
- **Senha:** `func123`
- **Permissões:** Acesso apenas à Iron Fitness Academia

#### Academia 2: Power House Gym
- **Usuário:** `pedro.lima`
- **Senha:** `func123`
- **Permissões:** Acesso apenas à Power House Gym

---

## ▶️ Passo 6: Iniciar os Servidores

O sistema requer **4 servidores** rodando simultaneamente:

Abra **4 terminais diferentes** no diretorio do sistema e execute um comando em cada:

**Terminal 1 - Servidor Web:**
```bash
php artisan serve
```

**Terminal 2 - WebSocket (Reverb):**
```bash
php artisan reverb:start
```

**Terminal 3 - Fila de Jobs:**
```bash
php artisan queue:listen
```

**Terminal 4 - Assets Frontend:**
```bash
npm run dev
```

---

## 🌐 Passo 7: Acessar o Sistema

Após iniciar os servidores, acesse:

```
http://localhost:8000
```

---

## ✅ Checklist de Instalação

- [ ] PHP 8.2+ instalado
- [ ] Composer instalado
- [ ] Node.js 18+ instalado
- [ ] MySQL 8.0+ instalado e rodando
- [ ] Repositório clonado/baixado
- [ ] `composer install` executado
- [ ] `npm install` executado
- [ ] Arquivo `.env` criado e configurado
- [ ] `php artisan key:generate` executado
- [ ] Banco de dados criado
- [ ] Migrations executadas OU script SQL importado
- [ ] Seeders executadas OU dados SQL importados
- [ ] Servidores iniciados (`composer dev`)
- [ ] Sistema acessível em `http://localhost:8000`
- [ ] Login funcionando com credenciais fornecidas

---
