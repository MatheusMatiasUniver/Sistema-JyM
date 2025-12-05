# 📊 Dados de Teste - Sistema JyM

Este documento lista todos os dados criados pelas seeders do sistema para fins de desenvolvimento e testes.

---

## 👥 Usuários do Sistema

### 🔐 Administrador

| Campo | Valor |
|-------|-------|
| **Nome** | Administrador Sistema |
| **Usuário** | `admin` |
| **Senha** | `admin123` |
| **Email** | admin@sistemajym.com.br |
| **Nível de Acesso** | Administrador |
| **Academia** | Todas (multi-academia) |
| **Salário Mensal** | R$ 10.000,00 |

**Permissões:**
- ✅ Acesso a todas as academias
- ✅ Pode trocar contexto entre academias
- ✅ Todas as funcionalidades do sistema

---

### 👤 Funcionários

#### Academia 1: Iron Fitness Academia

| Campo | Valor |
|-------|-------|
| **Nome** | Maria Souza |
| **Usuário** | `maria.souza` |
| **Senha** | `func123` |
| **Email** | maria@ironfitness.com.br |
| **Nível de Acesso** | Funcionário |
| **Academia** | Iron Fitness Academia (ID: 1) |
| **Salário Mensal** | R$ 2.500,00 |

**Permissões:**
- ✅ Acesso apenas à Iron Fitness Academia
- ✅ Gestão de clientes, vendas e entradas
- ❌ Não pode trocar contexto de academia

---

#### Academia 2: Power House Gym

| Campo | Valor |
|-------|-------|
| **Nome** | Pedro Henrique Lima |
| **Usuário** | `pedro.lima` |
| **Senha** | `func123` |
| **Email** | pedro@powerhousegym.com.br |
| **Nível de Acesso** | Funcionário |
| **Academia** | Power House Gym (ID: 2) |
| **Salário Mensal** | R$ 2.500,00 |

**Permissões:**
- ✅ Acesso apenas à Power House Gym
- ✅ Gestão de clientes, vendas e entradas
- ❌ Não pode trocar contexto de academia

---

## 🏢 Academias

### Academia 1: Iron Fitness Academia

| Campo | Valor |
|-------|-------|
| **ID** | 1 |
| **Nome** | Iron Fitness Academia |
| **CNPJ** | 12.345.678/0001-90 |
| **Telefone** | (44) 99999-8888 |
| **Email** | contato@ironfitness.com.br |
| **Endereço** | Av. Brasil, 1500 - Centro, Maringá - PR |
| **Responsável** | Carlos Eduardo Silva |

**Dados Cadastrados:**
- 32 Clientes
- 5 Planos de Assinatura
- 14 Produtos
- 10 Equipamentos
- 4 Materiais de consumo
- 120 dias de operação simulada

---

### Academia 2: Power House Gym

| Campo | Valor |
|-------|-------|
| **ID** | 2 |
| **Nome** | Power House Gym |
| **CNPJ** | 98.765.432/0001-10 |
| **Telefone** | (43) 98888-7777 |
| **Email** | contato@powerhousegym.com.br |
| **Endereço** | Rua Sergipe, 850 - Centro, Londrina - PR |
| **Responsável** | Roberto Almeida Santos |

**Dados Cadastrados:**
- 24 Clientes
- 5 Planos de Assinatura
- 14 Produtos
- 10 Equipamentos
- 4 Materiais de consumo
- 120 dias de operação simulada

---

## 💳 Planos de Assinatura

Os mesmos 5 planos estão disponíveis em ambas as academias:

| Plano | Descrição | Valor | Duração |
|-------|-----------|-------|---------|
| **Mensal Básico** | Acesso à musculação | R$ 89,90 | 30 dias |
| **Mensal Completo** | Musculação + Aulas coletivas | R$ 129,90 | 30 dias |
| **Trimestral** | Acesso completo por 3 meses | R$ 299,90 | 90 dias |
| **Semestral** | Acesso completo por 6 meses | R$ 499,90 | 180 dias |
| **Anual** | Melhor custo-benefício | R$ 799,90 | 365 dias |

---

## 👨‍👩‍👧‍👦 Clientes

### Iron Fitness Academia (32 clientes)

Ana Clara Oliveira, Bruno Santos Silva, Carla Mendes, Daniel Ferreira Costa, Elena Rodrigues, Felipe Almeida, Gabriela Lima, Henrique Souza, Isabela Martins, João Pedro Nascimento, Karina Dias, Lucas Ribeiro, Mariana Costa, Nicolas Pereira, Olivia Santos, Paulo Henrique, Rafaela Gomes, Samuel Alves, Tatiana Moreira, Vinícius Castro, Amanda Barbosa, Ricardo Teixeira, Juliana Cardoso, Thiago Fernandes, Letícia Araújo, Gustavo Rocha, Camila Nunes, André Monteiro, Beatriz Correia, Matheus Pinto, Larissa Duarte, Fernando Lopes

**Características:**
- Status: Maioria "Ativo", alguns "Inadimplente" (dependendo da simulação)
- Códigos de acesso: 001001 a 001032
- CPFs gerados automaticamente
- Idades: 18 a 50 anos
- Planos distribuídos aleatoriamente

---

### Power House Gym (24 clientes)

Fernanda Costa Silva, Rodrigo Pereira, Aline Moreira Santos, Diego Alves, Patrícia Ferreira, Marcelo Souza, Renata Dias Lima, Eduardo Martins, Vanessa Ribeiro, Leonardo Nascimento, Cristiane Gomes, Fábio Teixeira, Débora Cardoso, Guilherme Araújo, Simone Rocha, Anderson Monteiro, Priscila Correia, Vinícius Pinto, Natália Duarte, Caio Lopes, Michele Barbosa, Rafael Santos, Jéssica Oliveira, Leandro Fernandes

**Características:**
- Status: Maioria "Ativo", alguns "Inadimplente" (dependendo da simulação)
- Códigos de acesso: 002001 a 002024
- CPFs gerados automaticamente
- Idades: 18 a 50 anos
- Planos distribuídos aleatoriamente

---

## 🛍️ Produtos (Ambas Academias)

### Suplementos
- Whey Protein 1kg - R$ 129,90
- Creatina 300g - R$ 79,90
- BCAA 120 caps - R$ 59,90
- Pré-Treino 300g - R$ 89,90
- Glutamina 300g - R$ 69,90
- Barra de Proteína - R$ 8,90

### Bebidas
- Água Mineral 500ml - R$ 4,00
- Isotônico 500ml - R$ 7,50
- Energético 250ml - R$ 9,90

### Acessórios
- Luva de Treino - R$ 49,90
- Squeeze 1L - R$ 25,90
- Toalha Esportiva - R$ 29,90

### Roupas
- Camiseta Dry Fit - R$ 59,90
- Shorts Academia - R$ 49,90

---

## 🏋️ Equipamentos (Ambas Academias)

1. **Esteira Elétrica Profissional** - Technogym Skillrun - R$ 45.000,00
2. **Bicicleta Ergométrica Vertical** - Life Fitness Integrity - R$ 25.000,00
3. **Elíptico Profissional** - Precor EFX 885 - R$ 38.000,00
4. **Power Rack** - Hammer Strength HD Elite - R$ 18.000,00
5. **Leg Press 45°** - Technogym Selection Pro - R$ 22.000,00
6. **Supino Reto** - Life Fitness Signature - R$ 12.000,00
7. **Puxador Alto** - Hammer Strength MTS - R$ 15.000,00
8. **Cross Cable** - Technogym Element+ - R$ 35.000,00
9. **Cadeira Extensora** - Life Fitness Optima - R$ 11.000,00
10. **Mesa Flexora** - Hammer Strength Select - R$ 11.500,00

**Total investido em equipamentos:** R$ 232.500,00 por academia

---

## 🧰 Materiais de Consumo (Ambas Academias)

| Material | Estoque Inicial | Estoque Mínimo | Unidade |
|----------|-----------------|----------------|---------|
| Álcool 70% | 20 | 5 | Litros |
| Toalhas de Papel | 50 | 10 | Pacotes |
| Desinfetante | 15 | 5 | Litros |
| Lubrificante Esteira | 8 | 3 | Litros |

---

## 📊 Dados Simulados (120 dias)

Ao executar `php artisan db:seed --class=SimulationSeeder`, são gerados:

### Por Academia

**Entradas de Clientes:**
- Dias úteis: 10 a 20 entradas/dia
- Fins de semana: 5 a 12 entradas/dia
- Total aproximado: 1.800 entradas

**Vendas de Produtos:**
- Dias úteis: 3 a 8 vendas/dia
- Fins de semana: 1 a 4 vendas/dia
- Total aproximado: 600 vendas

**Mensalidades:**
- Geradas automaticamente no vencimento
- Atualizadas diariamente pelo job `VerificarMensalidadesVencidas`
- Aproximadamente 120 mensalidades por academia

**Compras de Produtos:**
- 40% de chance em dias úteis
- Total aproximado: 50 compras

**Manutenções de Equipamentos:**
- 15% de chance em dias úteis
- Total aproximado: 20 manutenções

**Contas Fixas Mensais:**
- Aluguel, Energia, Água, Internet, etc.
- Geradas no dia 1 de cada mês
- Salários gerados automaticamente

---

## 🔢 Estatísticas Totais (Ambas Academias)

| Tipo de Dado | Quantidade Total |
|--------------|------------------|
| **Academias** | 2 |
| **Usuários** | 3 (1 admin + 2 funcionários) |
| **Clientes** | 56 (32 + 24) |
| **Planos** | 10 (5 por academia) |
| **Produtos** | 28 (14 por academia) |
| **Equipamentos** | 20 (10 por academia) |
| **Entradas** | ~3.600 (120 dias) |
| **Vendas** | ~1.200 (120 dias) |
| **Mensalidades** | ~240 (120 dias) |
| **Compras** | ~100 (120 dias) |

---

## 🔄 Regenerar Dados

### Resetar banco completo
```bash
php artisan migrate:fresh --seed
```

### Apenas dados de simulação (mantém estrutura)
```bash
php artisan db:seed --class=SimulationSeeder
```

### Limpar e criar dados novos
```bash
php artisan migrate:fresh
php artisan db:seed --class=SimulationSeeder
```

---

## 📝 Notas Importantes

1. **Senhas:** Todas as senhas são hasheadas com bcrypt
2. **CPFs:** Gerados automaticamente (não são CPFs reais válidos)
3. **Emails:** Fictícios, seguem padrão nome.sobrenome@email.com
4. **Códigos de Acesso:** Formato 00XXXX (XX = ID academia, XXXX = sequencial)
5. **Dados Simulados:** Variam a cada execução do seeder
6. **Status Clientes:** Atualizado automaticamente pelo job diário

---

## 🆘 Consultas Úteis

### Verificar dados via Tinker
```bash
php artisan tinker
```

```php
// Contar registros
Academia::count();              // 2
User::count();                  // 3
Cliente::count();               // 56
PlanoAssinatura::count();       // 10

// Ver usuários
User::all(['usuario', 'email', 'nivelAcesso']);

// Ver clientes por academia
Cliente::where('idAcademia', 1)->count();  // 32
Cliente::where('idAcademia', 2)->count();  // 24

// Ver clientes ativos
Cliente::where('status', 'Ativo')->count();
```

---

**📅 Última atualização:** Dezembro 2025  
**👨‍💻 Gerado por:** SimulationSeeder.php
