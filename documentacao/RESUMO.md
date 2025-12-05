# 📋 Resumo Executivo - Sistema JyM

## 🎯 Visão Geral

O **Sistema JyM** é uma solução completa para gestão de academias desenvolvida como TCC, oferecendo:

- ✅ **Multi-tenancy** - Gerenciamento de múltiplas academias em uma única instalação
- ✅ **Reconhecimento Facial** - Controle de acesso moderno e seguro
- ✅ **Gestão Completa** - Clientes, vendas, financeiro, estoque e equipamentos
- ✅ **Tempo Real** - Atualizações instantâneas via WebSockets

---

## 🚀 Início Rápido (5 minutos)

### Instalação Automatizada

**Windows:**
```bash
git clone https://github.com/MatheusMatiasUniver/Sistema-JyM.git
cd Sistema-JyM
instalar.bat
```

**Linux/Mac:**
```bash
git clone https://github.com/MatheusMatiasUniver/Sistema-JyM.git
cd Sistema-JyM
chmod +x instalar.sh
./instalar.sh
```

### Acesso Rápido

**URL:** http://localhost:8000

**Login Administrador:**
- Usuário: `admin`
- Senha: `admin123`

---

## 📚 Documentação Principal

| Documento | Para Quem | Tempo de Leitura |
|-----------|-----------|------------------|
| **[INSTALACAO.md](INSTALACAO.md)** | Novos usuários | 15 min |
| **[GUIA-RAPIDO.md](GUIA-RAPIDO.md)** | Desenvolvedores | 5 min |
| **[DADOS-TESTE.md](DADOS-TESTE.md)** | Testadores | 10 min |
| **[../docs/Documentação - Sistema JyM.md](docs/Documentação%20-%20Sistema%20JyM.md)** | Arquitetos/Orientadores | 30 min |

**📖 Navegação Completa:** [INDICE-DOCUMENTACAO.md](INDICE-DOCUMENTACAO.md)

---

## 💻 Stack Tecnológica

| Camada | Tecnologia | Versão |
|--------|-----------|--------|
| **Backend** | Laravel | 12.x |
| **Linguagem** | PHP | 8.2+ |
| **Banco de Dados** | MySQL | 8.0+ |
| **Frontend** | Blade + Tailwind CSS | 3.4 |
| **JavaScript** | Alpine.js | 3.x |
| **Build Tool** | Vite | 5.x |
| **WebSockets** | Laravel Reverb | 1.6+ |
| **PDFs** | DomPDF | 3.1+ |
| **Reconhecimento Facial** | Face-API.js | Latest |

---

## 🎓 Informações Acadêmicas

**Projeto:** Trabalho de Conclusão de Curso (TCC)  
**Curso:** Sistemas de Informação  
**Instituição:** UNIPAR - Campus Umuarama

**Autores:**
- João Guilherme Chagas Piaia
- Matheus Maiante Marques de Almeida

**Orientadores:**
- Prof. Elyssandro Piffer (Orientador)
- Prof. Carlos Eduardo Simoes Pelegrin
- Prof. Leandro Clementino de Lima
- Prof. Jose Roberto Pelissari Junior

---

## 🔑 Credenciais Padrão

### Administrador (Acesso Total)
```
Usuário: admin
Senha: admin123
Permissões: Todas academias
```

### Funcionário - Iron Fitness Academia
```
Usuário: maria.souza
Senha: func123
Academia: Iron Fitness (ID: 1)
```

### Funcionário - Power House Gym
```
Usuário: pedro.lima
Senha: func123
Academia: Power House Gym (ID: 2)
```

---

## 📊 Dados de Demonstração

Após instalação completa (com seeders):

- **2 Academias** completas
- **3 Usuários** (1 admin + 2 funcionários)
- **56 Clientes** (32 + 24)
- **10 Planos** de assinatura
- **28 Produtos** variados
- **20 Equipamentos** profissionais
- **120 dias** de operação simulada

**Total de registros:** ~4.000+ (entradas, vendas, mensalidades, etc.)

---

## 🛠️ Comandos Essenciais

### Desenvolvimento
```bash
composer dev              # Inicia tudo (recomendado)
php artisan serve        # Servidor web
php artisan reverb:start # WebSocket
npm run dev              # Assets
```

### Banco de Dados
```bash
# Via SQL (Rápido)
mysql -u root -p jym < database/schema/estrutura_banco.sql
mysql -u root -p jym < database/schema/dados_seeders.sql

# Via Laravel (Flexível)
php artisan migrate
php artisan db:seed --class=SimulationSeeder
```

### Manutenção
```bash
php artisan optimize:clear  # Limpar caches
composer test              # Executar testes
```

---

## 🎯 Funcionalidades Principais

### 🏢 Multi-Academia
- Gestão centralizada de múltiplas unidades
- Contexto automático por usuário
- Isolamento de dados por academia

### 👤 Reconhecimento Facial
- Cadastro facial via webcam
- Reconhecimento em tempo real
- Fallback para CPF/senha

### 💰 Gestão Financeira
- Mensalidades automáticas
- Vendas de produtos
- Contas a pagar/receber
- Relatórios em PDF

### 📦 Estoque e Compras
- Controle de produtos
- Gestão de fornecedores
- Movimentações rastreadas
- Alertas de estoque mínimo

### 🏋️ Equipamentos
- Cadastro de equipamentos
- Manutenções programadas
- Histórico completo

### 📊 Relatórios
- Faturamento mensal
- Vendas por período
- Listagem de clientes
- Exportação PDF

---

## 📁 Estrutura do Projeto

```
Sistema-JyM/
├── app/                    # Código da aplicação
│   ├── Http/Controllers/   # Controllers MVC
│   ├── Models/             # Eloquent Models
│   ├── Services/           # Lógica de negócio
│   └── Events/             # Broadcasting
├── database/
│   ├── migrations/         # Estrutura do banco
│   ├── seeders/            # Dados de teste
│   └── schema/             # Scripts SQL
├── resources/
│   ├── views/              # Templates Blade
│   └── js/                 # JavaScript/Alpine
├── docs/                   # Documentação técnica
├── INSTALACAO.md          # ⭐ Guia instalação
├── GUIA-RAPIDO.md         # ⚡ Referência rápida
├── DADOS-TESTE.md         # 📊 Credenciais
└── instalar.bat/sh        # 🔧 Instaladores
```

---

## ⚡ Casos de Uso Principais

### 1. Entrada de Cliente (Reconhecimento Facial)
```
Cliente chega → Kiosk ativa webcam → Face-API reconhece → 
Sistema registra entrada → Dashboard atualiza em tempo real
```

### 2. Venda de Produto
```
Funcionário seleciona produtos → Adiciona ao carrinho → 
Escolhe forma de pagamento → Finaliza venda → 
Estoque atualiza automaticamente
```

### 3. Gestão de Mensalidade
```
Sistema gera mensalidade no vencimento → 
Job diário verifica inadimplência → 
Atualiza status do cliente → 
Bloqueia acesso se necessário
```

---

## 🔒 Segurança

- ✅ Autenticação Laravel (sessions)
- ✅ Middleware de autorização
- ✅ Rate limiting em endpoints críticos
- ✅ Validação de inputs (Form Requests)
- ✅ Senhas com bcrypt
- ✅ CSRF protection
- ✅ SQL injection protection (Eloquent)

---

## 📈 Performance

- ✅ Eager loading (N+1 prevention)
- ✅ Broadcasting assíncrono (queues)
- ✅ Cache de configurações
- ✅ Assets otimizados (Vite)
- ✅ Índices no banco de dados

---

## 🐛 Troubleshooting Rápido

| Problema | Solução |
|----------|---------|
| Assets não carregam | `npm run build && php artisan optimize:clear` |
| Erro conexão DB | Verificar `.env` e `php artisan config:clear` |
| WebSocket não conecta | Executar `php artisan reverb:start` |
| Fila não processa | Executar `php artisan queue:listen` |

**Mais soluções:** [INSTALACAO.md - Problemas Comuns](INSTALACAO.md#problemas-comuns)

---

## 📞 Suporte

- **Documentação Completa:** [INDICE-DOCUMENTACAO.md](INDICE-DOCUMENTACAO.md)
- **Issues:** https://github.com/MatheusMatiasUniver/Sistema-JyM/issues
- **Repositório:** https://github.com/MatheusMatiasUniver/Sistema-JyM

---

## ✅ Checklist de Instalação

- [ ] PHP 8.2+ instalado
- [ ] MySQL 8.0+ rodando
- [ ] Composer instalado
- [ ] Node.js 18+ instalado
- [ ] Repositório clonado
- [ ] Dependências instaladas (`composer install` + `npm install`)
- [ ] `.env` configurado
- [ ] Banco criado e populado
- [ ] Servidores iniciados (`composer dev`)
- [ ] Login funcionando em http://localhost:8000

---

## 🎉 Próximos Passos

Após instalação bem-sucedida:

1. ✅ Faça login com `admin` / `admin123`
2. ✅ Explore as 2 academias pré-configuradas
3. ✅ Teste o reconhecimento facial em `/kiosk`
4. ✅ Consulte [GUIA-RAPIDO.md](GUIA-RAPIDO.md) para comandos
5. ✅ Leia [../docs/Documentação - Sistema JyM.md](docs/Documentação%20-%20Sistema%20JyM.md) para arquitetura

---

**📅 Versão:** 2.0.0 | **Data:** Dezembro 2025  
**🎓 Desenvolvido como TCC - UNIPAR Umuarama**

