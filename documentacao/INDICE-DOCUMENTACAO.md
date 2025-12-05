# 📚 Índice da Documentação - Sistema JyM

Bem-vindo à documentação completa do Sistema JyM! Este índice organiza toda a documentação disponível para facilitar a navegação.

---

## 🚀 Começando

### Para Novos Usuários

1. **[INSTALACAO.md](INSTALACAO.md)** ⭐ **COMECE AQUI**
   - Tutorial completo de instalação
   - Requisitos do sistema
   - Configuração passo a passo
   - Duas formas de instalação do banco
   - Solução de problemas comuns

2. **Scripts de Instalação Automatizada**
   - `instalar.bat` - Para Windows
   - `instalar.sh` - Para Linux/Mac
   - Instalação guiada e verificação automática de requisitos

3. **[DADOS-TESTE.md](DADOS-TESTE.md)**
   - Credenciais de acesso (admin e funcionários)
   - Lista completa de dados de teste
   - Informações sobre as academias
   - Clientes, produtos, equipamentos

---

## 👨‍💻 Para Desenvolvedores

### Referência Rápida

4. **[GUIA-RAPIDO.md](GUIA-RAPIDO.md)** ⚡ **REFERÊNCIA DIÁRIA**
   - Comandos essenciais
   - Conceitos-chave do sistema
   - Tarefas comuns
   - Troubleshooting rápido
   - Atalhos e convenções

### Arquitetura e Código

5. **[../docs/Documentação - Sistema JyM.md](../docs/Documentação%20-%20Sistema%20JyM.md)**
   - Documentação técnica completa
   - Arquitetura do sistema
   - Módulos e funcionalidades
   - Diagramas e fluxos

6. **Instruções de Desenvolvimento**
   - `../.github/copilot-instructions.md` - Convenções do projeto
   - `../.github/instructions/intru.instructions.md` - Padrões de código

---

## 💾 Banco de Dados

### Scripts SQL

7. **[../database/schema/README.md](../database/schema/README.md)**
   - Documentação dos scripts SQL
   - Como usar os scripts de estrutura e dados
   - Regeneração de scripts
   - Estrutura de tabelas

8. **Scripts Disponíveis**
   - `../database/schema/estrutura_banco.sql` - Estrutura completa (DDL)
   - `../database/schema/dados_seeders.sql` - Dados iniciais
   - `../database_schema.sql` - Schema legacy

### Migrations e Seeders

9. **Migrations** (`../../database/migrations/`)
   - Estrutura versionada do banco
   - Histórico de alterações
   - Rollback disponível

10. **Seeders** (`../../database/seeders/`)
    - `SimulationSeeder.php` - 120 dias de dados simulados
    - `DatabaseSeeder.php` - Seeder principal

---

## 📖 Documentação Geral

### Arquivos do Projeto

11. **[../README.md](../README.md)**
    - Visão geral do projeto
    - Autores e orientadores
    - Stack tecnológica
    - Links para documentação

12. **[../CHANGELOG.md](../CHANGELOG.md)**
    - Histórico de versões
    - Notas de release
    - Mudanças e melhorias

13. **[../LICENSE](../LICENSE)** (se existir)
    - Termos de licença
    - Direitos de uso

---

## 🎯 Guias por Tarefa

### Instalação e Configuração

| Tarefa | Documento | Seção |
|--------|-----------|-------|
| Instalar pela primeira vez | INSTALACAO.md | Início |
| Instalar via SQL scripts | INSTALACAO.md | Passo 4 - Forma 1 |
| Instalar via migrations | INSTALACAO.md | Passo 4 - Forma 2 |
| Configurar .env | INSTALACAO.md | Passo 3 |
| Obter credenciais de teste | DADOS-TESTE.md | Usuários |
| Resolver problemas | INSTALACAO.md | Problemas Comuns |

### Desenvolvimento

| Tarefa | Documento | Seção |
|--------|-----------|-------|
| Comandos rápidos | GUIA-RAPIDO.md | Comandos Essenciais |
| Conceitos multi-academia | GUIA-RAPIDO.md | Conceitos-Chave |
| Padrões de código | .github/copilot-instructions.md | Todo |
| Criar migrations | GUIA-RAPIDO.md | Tarefas Comuns |
| Gerar PDFs | GUIA-RAPIDO.md | Tarefas Comuns |
| Debug rápido | GUIA-RAPIDO.md | Debug Rápido |

### Banco de Dados

| Tarefa | Documento | Seção |
|--------|-----------|-------|
| Entender estrutura | database/schema/README.md | Estrutura de Tabelas |
| Importar SQL | database/schema/README.md | Instalação |
| Regenerar scripts | database/schema/README.md | Regenerar Scripts |
| Executar seeders | GUIA-RAPIDO.md | Banco de Dados |
| Ver dados de teste | DADOS-TESTE.md | Todo |

### Funcionalidades

| Tarefa | Documento | Seção |
|--------|-----------|-------|
| Arquitetura geral | docs/Documentação - Sistema JyM.md | Arquitetura |
| Reconhecimento facial | docs/Documentação - Sistema JyM.md | Módulos |
| Multi-tenancy | GUIA-RAPIDO.md | Conceitos-Chave |
| Broadcasting | GUIA-RAPIDO.md | Conceitos-Chave |
| Jobs automáticos | GUIA-RAPIDO.md | Conceitos-Chave |

---

## 🎓 Fluxo de Aprendizado Sugerido

### Para Novos Desenvolvedores

```
1. README.md
   ↓
2. INSTALACAO.md (seguir passo a passo)
   ↓
3. DADOS-TESTE.md (obter credenciais)
   ↓
4. Fazer login no sistema
   ↓
5. GUIA-RAPIDO.md (conceitos-chave)
   ↓
6. docs/Documentação - Sistema JyM.md (arquitetura)
   ↓
7. .github/copilot-instructions.md (padrões)
   ↓
8. Explorar código-fonte
```

### Para Configuração de Servidor

```
1. INSTALACAO.md (requisitos)
   ↓
2. database/schema/README.md (scripts SQL)
   ↓
3. Importar estrutura_banco.sql
   ↓
4. Importar dados_seeders.sql
   ↓
5. Configurar .env (produção)
   ↓
6. php artisan config:cache
   ↓
7. npm run build
```

### Para Manutenção

```
1. CHANGELOG.md (versão atual)
   ↓
2. GUIA-RAPIDO.md (comandos úteis)
   ↓
3. INSTALACAO.md (troubleshooting)
   ↓
4. database/schema/README.md (backup)
```

---

## 📁 Estrutura de Arquivos de Documentação

```
Sistema-JyM/
├── README.md                          # Visão geral
├── INSTALACAO.md                      # ⭐ Guia de instalação
├── GUIA-RAPIDO.md                     # ⚡ Referência rápida
├── DADOS-TESTE.md                     # 📊 Dados e credenciais
├── CHANGELOG.md                       # 📝 Histórico de versões
├── INDICE-DOCUMENTACAO.md            # 📚 Este arquivo
├── instalar.bat                       # 🔧 Instalador Windows
├── instalar.sh                        # 🔧 Instalador Linux/Mac
├── .github/
│   ├── copilot-instructions.md       # Convenções do projeto
│   └── instructions/
│       └── intru.instructions.md     # Padrões de código
├── database/
│   ├── schema/
│   │   ├── README.md                 # 💾 Doc scripts SQL
│   │   ├── estrutura_banco.sql       # Estrutura DDL
│   │   └── dados_seeders.sql         # Dados iniciais
│   ├── migrations/                   # Migrations Laravel
│   └── seeders/                      # Seeders Laravel
└── docs/
    ├── Documentação - Sistema JyM.md # 📄 Doc técnica completa
    └── diagramas/                    # Diagramas do sistema
```

---

## 🔍 Busca Rápida

### Preciso saber como...

- **Instalar o sistema** → INSTALACAO.md
- **Fazer login** → DADOS-TESTE.md (credenciais)
- **Rodar comandos** → GUIA-RAPIDO.md
- **Entender multi-academia** → GUIA-RAPIDO.md > Conceitos-Chave
- **Importar banco via SQL** → database/schema/README.md
- **Resolver erros** → INSTALACAO.md > Problemas Comuns
- **Ver dados de teste** → DADOS-TESTE.md
- **Entender arquitetura** → docs/Documentação - Sistema JyM.md
- **Seguir padrões** → .github/copilot-instructions.md
- **Ver histórico** → CHANGELOG.md

---

## 📞 Suporte e Contato

- **Issues:** https://github.com/MatheusMatiasUniver/Sistema-JyM/issues
- **Repositório:** https://github.com/MatheusMatiasUniver/Sistema-JyM
- **Email:** (conforme definido pelos autores)

---

## ✅ Checklist de Documentação

Para contribuidores: ao adicionar novas funcionalidades, certifique-se de:

- [ ] Atualizar CHANGELOG.md
- [ ] Adicionar seção em GUIA-RAPIDO.md (se relevante)
- [ ] Documentar em docs/Documentação - Sistema JyM.md
- [ ] Atualizar este índice (INDICE-DOCUMENTACAO.md)
- [ ] Adicionar exemplos de código quando necessário
- [ ] Atualizar database/schema/README.md (se alterar banco)
- [ ] Documentar credenciais em DADOS-TESTE.md (se adicionar)

---

**📅 Última atualização:** Dezembro 2025  
**👨‍💻 Desenvolvido por:** João Guilherme Piaia & Matheus Maiante Almeida  
**🎓 Instituição:** UNIPAR - Campus Umuarama

