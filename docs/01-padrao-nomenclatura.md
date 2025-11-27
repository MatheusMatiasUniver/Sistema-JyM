# Padrão de Nomenclatura

Este documento define as convenções de nomenclatura que devem ser seguidas no desenvolvimento do Sistema JyM.

---

## Variáveis e Funções

Utilize **camelCase** para variáveis e funções.

### Exemplos

```php
// Variáveis
$userName = 'João';
$totalPrice = 150.00;
$isActive = true;

// Funções
function validaEntrada($dados) { }
function calcularTotal($itens) { }
function buscarCliente($id) { }
```

---

## Classes e Interfaces

Utilize **PascalCase** para classes e interfaces.

### Exemplos

```php
// Classes
class UserController { }
class ClienteService { }
class ProdutoRepository { }

// Interfaces
interface IUserRepository { }
interface IClienteService { }
```

---

## Constantes

Utilize **UPPER_SNAKE_CASE** para constantes.

### Exemplos

```php
const MAX_REQUESTS_PER_MINUTE = 60;
const DEFAULT_PAGE_SIZE = 15;
const API_VERSION = '1.0';
```

---

## Boas Práticas

- **Evite abreviações** em nomes de variáveis, funções, classes e interfaces.
  - ❌ `$usr`, `$prod`, `$cat`
  - ✅ `$user`, `$product`, `$category`

- **Nunca exponha** detalhes internos do servidor em mensagens de erro.
  - ❌ `throw new Exception("Erro SQL: SELECT * FROM users WHERE...")`
  - ✅ `throw new Exception("Erro ao processar a requisição.")`

- Use nomes **descritivos** que indiquem claramente a finalidade da variável ou função.

- Mantenha **consistência** em todo o código do projeto.

---

## Referências

- [PSR-1: Basic Coding Standard](https://www.php-fig.org/psr/psr-1/)
- [PSR-12: Extended Coding Style Guide](https://www.php-fig.org/psr/psr-12/)
