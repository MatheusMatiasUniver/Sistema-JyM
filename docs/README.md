# Documentação do Sistema JyM

Bem-vindo à documentação do Sistema JyM - Sistema de Gestão para Academias.

---

## Índice

Esta documentação está organizada em seções para facilitar a navegação:

### Desenvolvimento

1. **[Padrão de Nomenclatura](01-padrao-nomenclatura.md)**
   - Convenções de nomes para variáveis, funções, classes e constantes
   - Boas práticas de nomenclatura

2. **[Validação de Entrada](02-validacao-entrada.md)**
   - Práticas de validação de dados
   - Uso de Form Requests no Laravel
   - Tratamento de erros de validação

3. **[Práticas de Segurança](03-praticas-seguranca.md)**
   - Hash de senhas
   - Rate limiting
   - Prevenção de SQL Injection
   - CORS, HTTPS e autenticação

4. **[Logs de Modificações](04-logs-modificacoes.md)**
   - Estrutura de logs
   - Registro de alterações
   - Uso do ActivityLog

### Operações

5. **[Inicialização do Projeto](05-inicializacao-projeto.md)**
   - Pré-requisitos
   - Instalação e configuração
   - Comandos para iniciar os servidores

6. **[Domínio de Acesso](06-dominio-acesso.md)**
   - URLs de acesso
   - Configuração de ambiente
   - Portas utilizadas

---

## Visão Geral do Sistema

O Sistema JyM é um sistema de gestão completo para academias, desenvolvido com Laravel. Ele oferece funcionalidades para:

- **Gestão de Clientes** - Cadastro, mensalidades, controle de acesso
- **Gestão de Produtos** - Estoque, vendas, categorias
- **Gestão Financeira** - Contas a pagar, contas a receber
- **Gestão de Equipamentos** - Inventário, manutenção
- **Controle de Acesso** - Reconhecimento facial, registro de entradas

---

## Tecnologias Utilizadas

| Tecnologia | Versão | Descrição |
|------------|--------|-----------|
| PHP | 8.2+ | Linguagem backend |
| Laravel | 12.x | Framework PHP |
| MySQL | 8.0+ | Banco de dados |
| Node.js | 20+ (LTS) | Runtime JavaScript |
| Vite | 7.x | Build tool |
| Tailwind CSS | 3.x | Framework CSS |
| Laravel Reverb | - | WebSocket server |

---

## Estrutura do Projeto

```
Sistema-JyM/
├── app/
│   ├── Http/
│   │   ├── Controllers/    # Controladores
│   │   ├── Middleware/     # Middlewares
│   │   └── Requests/       # Form Requests
│   ├── Models/             # Modelos Eloquent
│   ├── Services/           # Serviços de negócio
│   └── Providers/          # Service Providers
├── config/                 # Configurações
├── database/
│   ├── migrations/         # Migrations
│   └── seeders/            # Seeders
├── docs/                   # Documentação
├── public/                 # Arquivos públicos
├── resources/
│   ├── css/               # Estilos
│   ├── js/                # JavaScript
│   └── views/             # Views Blade
├── routes/                # Rotas
├── storage/               # Armazenamento
└── tests/                 # Testes
```

---

## Início Rápido

```bash
# 1. Clonar repositório
git clone https://github.com/MatheusMatiasUniver/Sistema-JyM.git
cd Sistema-JyM

# 2. Instalar dependências
composer install
npm install

# 3. Configurar ambiente
cp .env.example .env
php artisan key:generate
php artisan migrate

# 4. Iniciar servidores (em terminais separados)
php artisan serve
php artisan reverb:start
npm run dev

# 5. Acessar o sistema
# http://localhost:8000
```

---

## Contribuindo

Antes de contribuir, certifique-se de:

1. Ler toda a documentação de desenvolvimento
2. Seguir os padrões de nomenclatura
3. Implementar validações adequadas
4. Aplicar as práticas de segurança
5. Registrar logs de modificações

---

## Suporte

Para suporte ou dúvidas, entre em contato com a equipe de desenvolvimento.

---

## Licença

Este projeto é proprietário e de uso interno.
