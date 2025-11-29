---
applyTo: '**'
---

# Instruções de Desenvolvimento Compatíveis com GitHub Copilot

## 1. Padrão de Nomenclatura

- Utilize **camelCase** para variáveis e funções.
  - Exemplo: `userName`, `validaEntrada()`
- Utilize **PascalCase** para classes e interfaces.
  - Exemplo: `UserController`, `IUserRepository`
- Utilize **UPPER_SNAKE_CASE** para constantes.
  - Exemplo: `MAX_REQUESTS_PER_MINUTE`
- Evite **abreviações** em nomes de variáveis, funções, classes e interfaces.
- **Nunca** exponha detalhes internos do servidor em mensagens de erro.

---

## 2. Validação de Entrada

- **Valide todos os inputs** antes de processar qualquer dado.
- Retorne **erros de validação com status 400**.
- **Documente** todos os campos obrigatórios e seus tipos nas funções, endpoints e métodos.
- Não permita que dados inválidos passem despercebidos para a lógica de negócio.

---

## 3. Práticas de Segurança

- **Nunca armazene senhas em texto plano.**
- Utilize **bcrypt** ou **argon2** para fazer o hash de senhas.
- Implemente **rate limiting** em endpoints públicos, evitando ataques de força bruta.
- Sempre **valide e sanitize todos os inputs** para prevenir SQL injection.
- Utilize **CORS** adequadamente para proteger contra ataques cross-origin.
- Implemente **autenticação** e **autorização** robustas. 
- **Utilize HTTPS** em todas as comunicações.
- **Monitore** e **registre** atividades suspeitas.

---

## 4. Logs de Modificações

- **Sim**, todos os logs de modificações devem ser gerados.
- **Pasta de Logs:** `.logs_modificacoes`
  - Armazene registros de alterações de código, especialmente os que impactam segurança, entrada de dados ou nomenclatura.
  - Utilize arquivos separados por data ou funcionalidade, conforme necessário.

---

## 5. Inicialização do Projeto

Para o projeto funcionar corretamente, **inicie os seguintes servidores**:

```sh
php artisan serve
php artisan reveb:start
npm run dev
```

---

## 6. Domínio de Acesso

O acesso ao sistema deve ser feito via:

```
http://localhost:8000
```

---

## Observações Finais

- Siga e confira todas as normas acima a cada commit.
- Mantenha este arquivo atualizado conforme as práticas evoluírem.

Provide project context and coding guidelines that AI should follow when generating code, answering questions, or reviewing changes.