# 🤝 Guia de Contribuição - Sistema JyM

Obrigado por considerar contribuir com o Sistema JyM! Este documento fornece diretrizes para contribuir com o projeto.

---

## 📋 Antes de Começar

1. Leia a [documentação completa](INDICE-DOCUMENTACAO.md)
2. Familiarize-se com as [convenções do projeto](.github/copilot-instructions.md)
3. Configure o [ambiente de desenvolvimento](INSTALACAO.md)

---

## 🔧 Configuração do Ambiente

### 1. Fork e Clone
```bash
# Fork o repositório no GitHub
# Clone seu fork
git clone https://github.com/SEU-USUARIO/Sistema-JyM.git
cd Sistema-JyM

# Adicione o repositório original como upstream
git remote add upstream https://github.com/MatheusMatiasUniver/Sistema-JyM.git
```

### 2. Instale as Dependências
```bash
composer install
npm install
```

### 3. Configure o Ambiente
```bash
copy .env.example .env
php artisan key:generate
# Configure seu banco de dados no .env
php artisan migrate
php artisan db:seed --class=SimulationSeeder
```

---

## 📝 Padrões de Código

### Convenções Gerais

1. **Nomenclatura:**
   - Classes e Interfaces: `PascalCase`
   - Métodos e variáveis: `camelCase`
   - Constantes: `UPPER_SNAKE_CASE`
   - Sem abreviações desnecessárias

2. **Primary Keys:**
   ```php
   // Use o padrão do projeto
   protected $primaryKey = 'idCliente';  // ✅ Correto
   protected $primaryKey = 'id';         // ❌ Errado
   ```

3. **Multi-academia:**
   ```php
   // SEMPRE filtre por academia
   $academiaId = config('app.academia_atual');
   $clientes = Cliente::where('idAcademia', $academiaId)->get();
   ```

4. **Sem Comentários no Código:**
   - Código deve ser autoexplicativo
   - Use nomes descritivos
   - Documente apenas casos complexos

### Laravel Específico

```php
// ✅ Correto: Use Form Requests
public function store(StoreClienteRequest $request)
{
    // Validação já feita pelo Request
}

// ✅ Correto: Use Services para lógica complexa
$this->entradaService->registrar($cliente);

// ✅ Correto: Use Eloquent relationships
$cliente->mensalidades()->where('status', 'Pendente')->get();

// ❌ Evite: Queries diretas complexas no controller
DB::table('clientes')->join(...)->where(...)->get();
```

### Validação

```php
// CPF sem formatação (11 dígitos)
'cpf' => ['required', 'size:11', 'regex:/^[0-9]{11}$/'],

// SEMPRE valide inputs
// NUNCA confie em dados do usuário
```

---

## 🌿 Workflow Git

### 1. Crie uma Branch

Use nomes descritivos:
```bash
git checkout -b feature/nome-da-funcionalidade
git checkout -b fix/correcao-do-bug
git checkout -b docs/atualizacao-documentacao
```

**Prefixos:**
- `feature/` - Nova funcionalidade
- `fix/` - Correção de bug
- `docs/` - Documentação
- `refactor/` - Refatoração
- `test/` - Testes
- `style/` - Formatação/estilo

### 2. Faça Commits Semânticos

```bash
# Formato
git commit -m "tipo(escopo): descrição curta"

# Exemplos
git commit -m "feat(clientes): adiciona validação de CPF"
git commit -m "fix(vendas): corrige cálculo de desconto"
git commit -m "docs(readme): atualiza instruções de instalação"
git commit -m "refactor(services): melhora EntradaService"
```

**Tipos:**
- `feat` - Nova funcionalidade
- `fix` - Correção de bug
- `docs` - Documentação
- `style` - Formatação (não afeta código)
- `refactor` - Refatoração
- `test` - Adição/correção de testes
- `chore` - Tarefas de manutenção

### 3. Mantenha Atualizado

```bash
# Atualize sua branch com upstream
git fetch upstream
git rebase upstream/main
```

### 4. Push e Pull Request

```bash
git push origin feature/nome-da-funcionalidade
```

No GitHub:
1. Abra um Pull Request
2. Descreva as mudanças claramente
3. Referencie issues relacionadas
4. Aguarde revisão

---

## ✅ Checklist de Pull Request

Antes de abrir um PR, verifique:

### Código
- [ ] Segue as convenções do projeto
- [ ] Não adiciona comentários desnecessários
- [ ] Filtra por `idAcademia` onde necessário
- [ ] Usa Form Requests para validação
- [ ] Lógica complexa está em Services
- [ ] Primary keys seguem padrão (`idTabela`)

### Testes
- [ ] Testes existentes passam (`composer test`)
- [ ] Novos testes adicionados (se aplicável)
- [ ] Testado manualmente

### Documentação
- [ ] README.md atualizado (se necessário)
- [ ] CHANGELOG.md atualizado
- [ ] Comentários de código (apenas se muito necessário)
- [ ] GUIA-RAPIDO.md atualizado (funcionalidades novas)

### Banco de Dados
- [ ] Migration criada (se alterou banco)
- [ ] Seeder atualizado (se necessário)
- [ ] Scripts SQL regenerados (se estrutura mudou)

### Segurança
- [ ] Inputs validados
- [ ] Sem SQL injection
- [ ] Sem exposição de dados sensíveis
- [ ] Rate limiting em endpoints críticos

---

## 🧪 Testes

### Executar Testes

```bash
composer test              # Todos os testes
php artisan test          # Com output Laravel
php artisan test --filter NomeDoTeste  # Teste específico
```

### Criar Testes

```php
// tests/Feature/ClienteTest.php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Cliente;

class ClienteTest extends TestCase
{
    public function test_pode_criar_cliente(): void
    {
        $response = $this->post('/clientes', [
            'nome' => 'Teste Cliente',
            'cpf' => '12345678901',
            // ... outros campos
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('clientes', [
            'nome' => 'Teste Cliente',
        ]);
    }
}
```

---

## 📦 Adicionando Dependências

### Composer (PHP)
```bash
# Adicionar dependência
composer require vendor/package

# Adicionar dependência de desenvolvimento
composer require --dev vendor/package

# Atualizar composer.json no PR
```

### NPM (JavaScript)
```bash
# Adicionar dependência
npm install package-name

# Adicionar dependência de desenvolvimento
npm install --save-dev package-name

# Atualizar package.json e package-lock.json no PR
```

**⚠️ Importante:**
- Justifique a necessidade da nova dependência
- Verifique licença compatível
- Atualize documentação

---

## 🗄️ Migrations e Seeders

### Criar Migration

```bash
# Criar tabela
php artisan make:migration create_tabela_table --create=tabela

# Alterar tabela
php artisan make:migration add_campo_to_tabela_table --table=tabela
```

**Convenções:**
```php
// Use o padrão do projeto
Schema::create('nome_tabela', function (Blueprint $table) {
    $table->id('idTabela');  // Primary key customizada
    $table->unsignedInteger('idAcademia');  // FK academia
    
    // Foreign keys
    $table->foreign('idAcademia')
          ->references('idAcademia')
          ->on('academias')
          ->onDelete('cascade');
});
```

### Criar Seeder

```bash
php artisan make:seeder NomeTabelaSeeder
```

---

## 📚 Documentação

### Atualizando Documentação

Ao adicionar funcionalidades, atualize:

1. **CHANGELOG.md** - Adicione na seção `[Unreleased]`
2. **GUIA-RAPIDO.md** - Adicione comandos/conceitos novos
3. **docs/Documentação - Sistema JyM.md** - Detalhes técnicos
4. **README.md** - Se afeta instalação/requisitos

### Formato da Documentação

- Use Markdown
- Seja claro e conciso
- Adicione exemplos de código
- Use emojis para categorização (📝 📊 ⚡ etc.)

---

## 🐛 Reportando Bugs

### Como Reportar

1. Verifique se já não foi reportado
2. Use template de issue (se houver)
3. Inclua:
   - Descrição clara do problema
   - Passos para reproduzir
   - Comportamento esperado vs atual
   - Versão do sistema
   - Ambiente (SO, PHP, MySQL)
   - Screenshots (se aplicável)

### Exemplo

```markdown
**Descrição:**
Erro ao cadastrar cliente com CPF já existente.

**Passos para Reproduzir:**
1. Acessar Clientes > Novo
2. Preencher com CPF já cadastrado
3. Clicar em Salvar

**Comportamento Esperado:**
Mensagem de validação "CPF já cadastrado"

**Comportamento Atual:**
Erro 500 - Internal Server Error

**Ambiente:**
- SO: Windows 11
- PHP: 8.2.12
- MySQL: 8.0.30
- Versão Sistema: 2.0.0
```

---

## 💡 Sugerindo Melhorias

1. Abra uma issue com label `enhancement`
2. Descreva o problema atual
3. Proponha a solução
4. Explique benefícios
5. Considere impacto em funcionalidades existentes

---

## 🔍 Revisão de Código

### Como Revisor

- ✅ Verifique se segue convenções
- ✅ Teste as mudanças localmente
- ✅ Sugira melhorias construtivamente
- ✅ Aprove se está tudo OK
- ✅ Solicite mudanças se necessário

### Como Autor

- ✅ Responda feedback prontamente
- ✅ Faça alterações solicitadas
- ✅ Agradeça revisores
- ✅ Aprenda com sugestões

---

## 📞 Comunicação

- **Issues:** Para bugs e features
- **Pull Requests:** Para contribuições de código
- **Discussões:** Para dúvidas e ideias gerais

---

## 📄 Licença

Ao contribuir, você concorda que suas contribuições serão licenciadas sob a mesma licença do projeto.

---

## 🙏 Agradecimentos

Obrigado por contribuir com o Sistema JyM! Suas contribuições ajudam a melhorar o sistema para todos.

---

**Dúvidas?** Consulte:
- [GUIA-RAPIDO.md](GUIA-RAPIDO.md) - Referência rápida
- [INSTALACAO.md](INSTALACAO.md) - Instalação e configuração
- [INDICE-DOCUMENTACAO.md](INDICE-DOCUMENTACAO.md) - Toda documentação

---

**📅 Última atualização:** Dezembro 2025  
**👨‍💻 Mantenedores:** João Piaia & Matheus Almeida

