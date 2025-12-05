@echo off
chcp 65001 > nul
color 0A
title Sistema JyM - Instalação Automatizada

echo.
echo ╔════════════════════════════════════════════════════════════════╗
echo ║         SISTEMA JYM - INSTALAÇÃO AUTOMATIZADA                 ║
echo ║         Gestão de Academias com Reconhecimento Facial         ║
echo ╚════════════════════════════════════════════════════════════════╝
echo.

REM Verificar se PHP está instalado
echo [1/10] Verificando PHP...
php -v >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERRO] PHP não encontrado! Instale PHP 8.2+ antes de continuar.
    echo Recomendamos usar Laragon: https://laragon.org/download/
    pause
    exit /b 1
)
php -r "if (version_compare(PHP_VERSION, '8.2.0') < 0) { echo '[ERRO] PHP 8.2+ necessário. Versão atual: ' . PHP_VERSION; exit(1); }"
if %errorlevel% neq 0 (
    pause
    exit /b 1
)
echo [OK] PHP instalado e compatível
echo.

REM Verificar Composer
echo [2/10] Verificando Composer...
composer -v >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERRO] Composer não encontrado!
    echo Baixe em: https://getcomposer.org/download/
    pause
    exit /b 1
)
echo [OK] Composer instalado
echo.

REM Verificar Node.js
echo [3/10] Verificando Node.js...
node -v >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERRO] Node.js não encontrado!
    echo Baixe em: https://nodejs.org/
    pause
    exit /b 1
)
echo [OK] Node.js instalado
echo.

REM Instalar dependências PHP
echo [4/10] Instalando dependências PHP (isso pode demorar)...
call composer install --no-interaction --prefer-dist --optimize-autoloader
if %errorlevel% neq 0 (
    echo [ERRO] Falha ao instalar dependências PHP
    pause
    exit /b 1
)
echo [OK] Dependências PHP instaladas
echo.

REM Instalar dependências JavaScript
echo [5/10] Instalando dependências JavaScript (isso pode demorar)...
call npm install
if %errorlevel% neq 0 (
    echo [ERRO] Falha ao instalar dependências JavaScript
    pause
    exit /b 1
)
echo [OK] Dependências JavaScript instaladas
echo.

REM Criar arquivo .env
echo [6/10] Configurando arquivo .env...
if not exist .env (
    copy .env.example .env >nul
    echo [OK] Arquivo .env criado
) else (
    echo [AVISO] Arquivo .env já existe. Pulando...
)
echo.

REM Gerar chave da aplicação
echo [7/10] Gerando chave da aplicação...
php artisan key:generate --force
echo [OK] Chave gerada
echo.

REM Verificar MySQL
echo [8/10] Verificando MySQL...
mysql --version >nul 2>&1
if %errorlevel% neq 0 (
    echo [AVISO] MySQL não encontrado no PATH
    echo Você precisará criar o banco manualmente
    echo.
    set MYSQL_MANUAL=1
) else (
    echo [OK] MySQL instalado
    echo.
)

REM Configurar banco de dados
echo [9/10] Configuração do Banco de Dados
echo.
echo Escolha o método de instalação:
echo [1] Scripts SQL (Recomendado - mais rápido)
echo [2] Migrations + Seeders (Laravel nativo)
echo.
set /p INSTALL_METHOD="Opção [1-2]: "

if "%INSTALL_METHOD%"=="1" (
    echo.
    echo === INSTALAÇÃO VIA SQL ===
    echo.
    set /p DB_USER="Usuário MySQL [root]: "
    if "%DB_USER%"=="" set DB_USER=root
    
    set /p DB_PASS="Senha MySQL (deixe vazio se não tiver): "
    
    set /p DB_NAME="Nome do banco [jym]: "
    if "%DB_NAME%"=="" set DB_NAME=jym
    
    echo.
    echo Criando banco de dados...
    if "%DB_PASS%"=="" (
        mysql -u %DB_USER% -e "CREATE DATABASE IF NOT EXISTS %DB_NAME% CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
    ) else (
        mysql -u %DB_USER% -p%DB_PASS% -e "CREATE DATABASE IF NOT EXISTS %DB_NAME% CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
    )
    
    if %errorlevel% neq 0 (
        echo [ERRO] Falha ao criar banco de dados
        pause
        exit /b 1
    )
    
    echo [OK] Banco criado
    echo.
    echo Importando estrutura...
    if "%DB_PASS%"=="" (
        mysql -u %DB_USER% %DB_NAME% < database\schema\estrutura_banco.sql
    ) else (
        mysql -u %DB_USER% -p%DB_PASS% %DB_NAME% < database\schema\estrutura_banco.sql
    )
    
    if %errorlevel% neq 0 (
        echo [ERRO] Falha ao importar estrutura
        pause
        exit /b 1
    )
    
    echo [OK] Estrutura importada
    echo.
    echo Importando dados iniciais...
    if "%DB_PASS%"=="" (
        mysql -u %DB_USER% %DB_NAME% < database\schema\dados_seeders.sql
    ) else (
        mysql -u %DB_USER% -p%DB_PASS% %DB_NAME% < database\schema\dados_seeders.sql
    )
    
    if %errorlevel% neq 0 (
        echo [ERRO] Falha ao importar dados
        pause
        exit /b 1
    )
    
    echo [OK] Dados importados
    
) else if "%INSTALL_METHOD%"=="2" (
    echo.
    echo === INSTALAÇÃO VIA MIGRATIONS ===
    echo.
    echo IMPORTANTE: Configure o arquivo .env com suas credenciais do MySQL
    echo antes de continuar!
    echo.
    echo Exemplo:
    echo DB_DATABASE=jym
    echo DB_USERNAME=root
    echo DB_PASSWORD=sua_senha
    echo.
    pause
    
    echo.
    echo Executando migrations...
    php artisan migrate --force
    
    if %errorlevel% neq 0 (
        echo [ERRO] Falha ao executar migrations
        echo Verifique o arquivo .env e tente novamente
        pause
        exit /b 1
    )
    
    echo [OK] Migrations executadas
    echo.
    echo Executando seeders (isso pode demorar alguns minutos)...
    php artisan db:seed --class=SimulationSeeder --force
    
    if %errorlevel% neq 0 (
        echo [ERRO] Falha ao executar seeders
        pause
        exit /b 1
    )
    
    echo [OK] Seeders executados
    
) else (
    echo [ERRO] Opção inválida
    pause
    exit /b 1
)

echo.
echo [10/10] Compilando assets...
call npm run build
echo [OK] Assets compilados
echo.

REM Limpar caches
echo Limpando caches...
php artisan config:clear >nul 2>&1
php artisan cache:clear >nul 2>&1
php artisan view:clear >nul 2>&1
echo [OK] Caches limpos
echo.

REM Resumo
echo.
echo ╔════════════════════════════════════════════════════════════════╗
echo ║                   INSTALAÇÃO CONCLUÍDA!                       ║
echo ╚════════════════════════════════════════════════════════════════╝
echo.
echo Credenciais de Acesso:
echo.
echo ┌─────────────────────────────────────────────────────────────┐
echo │ ADMINISTRADOR                                               │
echo │ Usuário: admin                                              │
echo │ Senha:   admin123                                           │
echo └─────────────────────────────────────────────────────────────┘
echo.
echo ┌─────────────────────────────────────────────────────────────┐
echo │ FUNCIONÁRIO - Iron Fitness Academia                         │
echo │ Usuário: maria.souza                                        │
echo │ Senha:   func123                                            │
echo └─────────────────────────────────────────────────────────────┘
echo.
echo ┌─────────────────────────────────────────────────────────────┐
echo │ FUNCIONÁRIO - Power House Gym                               │
echo │ Usuário: pedro.lima                                         │
echo │ Senha:   func123                                            │
echo └─────────────────────────────────────────────────────────────┘
echo.
echo Para iniciar o sistema:
echo   1. Execute: composer dev
echo   2. Acesse: http://localhost:8000
echo.
echo Ou execute cada servidor separadamente:
echo   - php artisan serve
echo   - php artisan reverb:start
echo   - php artisan queue:listen
echo   - npm run dev
echo.
echo Documentação completa: INSTALACAO.md
echo Guia rápido: GUIA-RAPIDO.md
echo.
pause
