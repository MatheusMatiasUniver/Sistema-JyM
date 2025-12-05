#!/bin/bash

# Cores
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Função para imprimir cabeçalho
print_header() {
    echo ""
    echo -e "${BLUE}╔════════════════════════════════════════════════════════════════╗${NC}"
    echo -e "${BLUE}║         SISTEMA JYM - INSTALAÇÃO AUTOMATIZADA                 ║${NC}"
    echo -e "${BLUE}║         Gestão de Academias com Reconhecimento Facial         ║${NC}"
    echo -e "${BLUE}╚════════════════════════════════════════════════════════════════╝${NC}"
    echo ""
}

# Função para verificar comando
check_command() {
    if ! command -v $1 &> /dev/null; then
        echo -e "${RED}[ERRO] $2 não encontrado!${NC}"
        echo -e "${YELLOW}$3${NC}"
        exit 1
    else
        echo -e "${GREEN}[OK] $2 instalado${NC}"
    fi
}

# Função para verificar versão PHP
check_php_version() {
    php_version=$(php -r "echo PHP_VERSION;")
    required_version="8.2.0"
    
    if [ "$(printf '%s\n' "$required_version" "$php_version" | sort -V | head -n1)" != "$required_version" ]; then
        echo -e "${RED}[ERRO] PHP 8.2+ necessário. Versão atual: $php_version${NC}"
        exit 1
    fi
}

# Início
clear
print_header

# Verificações
echo -e "${BLUE}[1/10] Verificando PHP...${NC}"
check_command php "PHP" "Instale PHP 8.2+: https://www.php.net/downloads"
check_php_version
echo ""

echo -e "${BLUE}[2/10] Verificando Composer...${NC}"
check_command composer "Composer" "Instale Composer: https://getcomposer.org/download/"
echo ""

echo -e "${BLUE}[3/10] Verificando Node.js...${NC}"
check_command node "Node.js" "Instale Node.js: https://nodejs.org/"
echo ""

# Instalar dependências
echo -e "${BLUE}[4/10] Instalando dependências PHP (isso pode demorar)...${NC}"
composer install --no-interaction --prefer-dist --optimize-autoloader
if [ $? -ne 0 ]; then
    echo -e "${RED}[ERRO] Falha ao instalar dependências PHP${NC}"
    exit 1
fi
echo -e "${GREEN}[OK] Dependências PHP instaladas${NC}"
echo ""

echo -e "${BLUE}[5/10] Instalando dependências JavaScript (isso pode demorar)...${NC}"
npm install
if [ $? -ne 0 ]; then
    echo -e "${RED}[ERRO] Falha ao instalar dependências JavaScript${NC}"
    exit 1
fi
echo -e "${GREEN}[OK] Dependências JavaScript instaladas${NC}"
echo ""

# Configurar .env
echo -e "${BLUE}[6/10] Configurando arquivo .env...${NC}"
if [ ! -f .env ]; then
    cp .env.example .env
    echo -e "${GREEN}[OK] Arquivo .env criado${NC}"
else
    echo -e "${YELLOW}[AVISO] Arquivo .env já existe. Pulando...${NC}"
fi
echo ""

# Gerar chave
echo -e "${BLUE}[7/10] Gerando chave da aplicação...${NC}"
php artisan key:generate --force
echo -e "${GREEN}[OK] Chave gerada${NC}"
echo ""

# Verificar MySQL
echo -e "${BLUE}[8/10] Verificando MySQL...${NC}"
if command -v mysql &> /dev/null; then
    echo -e "${GREEN}[OK] MySQL instalado${NC}"
else
    echo -e "${YELLOW}[AVISO] MySQL não encontrado no PATH${NC}"
    echo -e "${YELLOW}Você precisará criar o banco manualmente${NC}"
fi
echo ""

# Configurar banco
echo -e "${BLUE}[9/10] Configuração do Banco de Dados${NC}"
echo ""
echo "Escolha o método de instalação:"
echo "[1] Scripts SQL (Recomendado - mais rápido)"
echo "[2] Migrations + Seeders (Laravel nativo)"
echo ""
read -p "Opção [1-2]: " install_method

if [ "$install_method" == "1" ]; then
    echo ""
    echo "=== INSTALAÇÃO VIA SQL ==="
    echo ""
    
    read -p "Usuário MySQL [root]: " db_user
    db_user=${db_user:-root}
    
    read -sp "Senha MySQL (deixe vazio se não tiver): " db_pass
    echo ""
    
    read -p "Nome do banco [jym]: " db_name
    db_name=${db_name:-jym}
    
    echo ""
    echo "Criando banco de dados..."
    if [ -z "$db_pass" ]; then
        mysql -u "$db_user" -e "CREATE DATABASE IF NOT EXISTS $db_name CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
    else
        mysql -u "$db_user" -p"$db_pass" -e "CREATE DATABASE IF NOT EXISTS $db_name CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
    fi
    
    if [ $? -ne 0 ]; then
        echo -e "${RED}[ERRO] Falha ao criar banco de dados${NC}"
        exit 1
    fi
    echo -e "${GREEN}[OK] Banco criado${NC}"
    
    echo ""
    echo "Importando estrutura..."
    if [ -z "$db_pass" ]; then
        mysql -u "$db_user" "$db_name" < database/schema/estrutura_banco.sql
    else
        mysql -u "$db_user" -p"$db_pass" "$db_name" < database/schema/estrutura_banco.sql
    fi
    
    if [ $? -ne 0 ]; then
        echo -e "${RED}[ERRO] Falha ao importar estrutura${NC}"
        exit 1
    fi
    echo -e "${GREEN}[OK] Estrutura importada${NC}"
    
    echo ""
    echo "Importando dados iniciais..."
    if [ -z "$db_pass" ]; then
        mysql -u "$db_user" "$db_name" < database/schema/dados_seeders.sql
    else
        mysql -u "$db_user" -p"$db_pass" "$db_name" < database/schema/dados_seeders.sql
    fi
    
    if [ $? -ne 0 ]; then
        echo -e "${RED}[ERRO] Falha ao importar dados${NC}"
        exit 1
    fi
    echo -e "${GREEN}[OK] Dados importados${NC}"
    
    # Atualizar .env
    sed -i "s/DB_DATABASE=.*/DB_DATABASE=$db_name/" .env
    sed -i "s/DB_USERNAME=.*/DB_USERNAME=$db_user/" .env
    sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=$db_pass/" .env
    
elif [ "$install_method" == "2" ]; then
    echo ""
    echo "=== INSTALAÇÃO VIA MIGRATIONS ==="
    echo ""
    echo -e "${YELLOW}IMPORTANTE: Configure o arquivo .env com suas credenciais do MySQL${NC}"
    echo -e "${YELLOW}antes de continuar!${NC}"
    echo ""
    echo "Exemplo:"
    echo "DB_DATABASE=jym"
    echo "DB_USERNAME=root"
    echo "DB_PASSWORD=sua_senha"
    echo ""
    read -p "Pressione ENTER para continuar..."
    
    echo ""
    echo "Executando migrations..."
    php artisan migrate --force
    
    if [ $? -ne 0 ]; then
        echo -e "${RED}[ERRO] Falha ao executar migrations${NC}"
        echo -e "${YELLOW}Verifique o arquivo .env e tente novamente${NC}"
        exit 1
    fi
    echo -e "${GREEN}[OK] Migrations executadas${NC}"
    
    echo ""
    echo "Executando seeders (isso pode demorar alguns minutos)..."
    php artisan db:seed --class=SimulationSeeder --force
    
    if [ $? -ne 0 ]; then
        echo -e "${RED}[ERRO] Falha ao executar seeders${NC}"
        exit 1
    fi
    echo -e "${GREEN}[OK] Seeders executados${NC}"
else
    echo -e "${RED}[ERRO] Opção inválida${NC}"
    exit 1
fi

echo ""
echo -e "${BLUE}[10/10] Compilando assets...${NC}"
npm run build
echo -e "${GREEN}[OK] Assets compilados${NC}"
echo ""

# Limpar caches
echo "Limpando caches..."
php artisan config:clear > /dev/null 2>&1
php artisan cache:clear > /dev/null 2>&1
php artisan view:clear > /dev/null 2>&1
echo -e "${GREEN}[OK] Caches limpos${NC}"
echo ""

# Ajustar permissões (Linux)
if [[ "$OSTYPE" == "linux-gnu"* ]]; then
    echo "Ajustando permissões..."
    chmod -R 775 storage bootstrap/cache
    echo -e "${GREEN}[OK] Permissões ajustadas${NC}"
    echo ""
fi

# Resumo
echo ""
echo -e "${GREEN}╔════════════════════════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║                   INSTALAÇÃO CONCLUÍDA!                       ║${NC}"
echo -e "${GREEN}╚════════════════════════════════════════════════════════════════╝${NC}"
echo ""
echo "Credenciais de Acesso:"
echo ""
echo -e "${BLUE}┌─────────────────────────────────────────────────────────────┐${NC}"
echo -e "${BLUE}│ ADMINISTRADOR                                               │${NC}"
echo -e "${BLUE}│ Usuário: admin                                              │${NC}"
echo -e "${BLUE}│ Senha:   admin123                                           │${NC}"
echo -e "${BLUE}└─────────────────────────────────────────────────────────────┘${NC}"
echo ""
echo -e "${BLUE}┌─────────────────────────────────────────────────────────────┐${NC}"
echo -e "${BLUE}│ FUNCIONÁRIO - Iron Fitness Academia                         │${NC}"
echo -e "${BLUE}│ Usuário: maria.souza                                        │${NC}"
echo -e "${BLUE}│ Senha:   func123                                            │${NC}"
echo -e "${BLUE}└─────────────────────────────────────────────────────────────┘${NC}"
echo ""
echo -e "${BLUE}┌─────────────────────────────────────────────────────────────┐${NC}"
echo -e "${BLUE}│ FUNCIONÁRIO - Power House Gym                               │${NC}"
echo -e "${BLUE}│ Usuário: pedro.lima                                         │${NC}"
echo -e "${BLUE}│ Senha:   func123                                            │${NC}"
echo -e "${BLUE}└─────────────────────────────────────────────────────────────┘${NC}"
echo ""
echo "Para iniciar o sistema:"
echo -e "${YELLOW}  1. Execute: composer dev${NC}"
echo -e "${YELLOW}  2. Acesse: http://localhost:8000${NC}"
echo ""
echo "Ou execute cada servidor separadamente:"
echo "  - php artisan serve"
echo "  - php artisan reverb:start"
echo "  - php artisan queue:listen"
echo "  - npm run dev"
echo ""
echo "Documentação completa: INSTALACAO.md"
echo "Guia rápido: GUIA-RAPIDO.md"
echo ""
