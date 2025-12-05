# 🔑 Credenciais de Acesso - Sistema JyM

## 👤 Usuários do Sistema

### 🔐 Administrador (Acesso Total)

```
Usuário: admin
Senha: admin123
Email: admin@sistemajym.com.br
```

**Permissões:**
- ✅ Acesso a todas as academias
- ✅ Pode trocar contexto entre academias
- ✅ Acesso completo a todas as funcionalidades

---

### 👨‍💼 Funcionários

#### Academia 1: Iron Fitness Academia

```
Usuário: maria.souza
Senha: func123
Email: maria@ironfitness.com.br
```

**Informações:**
- Nome: Maria Souza
- Academia: Iron Fitness Academia (ID: 1)
- Permissões: Acesso apenas à Iron Fitness Academia

---

#### Academia 2: Power House Gym

```
Usuário: pedro.lima
Senha: func123
Email: pedro@powerhousegym.com.br
```

**Informações:**
- Nome: Pedro Henrique Lima
- Academia: Power House Gym (ID: 2)
- Permissões: Acesso apenas à Power House Gym

---

## 📋 Resumo de Credenciais

| Tipo | Usuário | Senha | Academia |
|------|---------|-------|----------|
| **Administrador** | `admin` | `admin123` | Todas |
| **Funcionário** | `maria.souza` | `func123` | Iron Fitness (1) |
| **Funcionário** | `pedro.lima` | `func123` | Power House (2) |

---

## 🌐 Acesso ao Sistema

**URL:** http://localhost:8000

**Telas:**
- `/` → Login
- `/dashboard` → Dashboard (após login)
- `/kiosk` → Interface reconhecimento facial

---

## ⚠️ Observações Importantes

1. **Senhas Padrão:** Todas as senhas são de desenvolvimento/teste
2. **Produção:** Altere todas as senhas antes de implantar em produção
3. **Multi-Academia:** O admin pode trocar entre academias, funcionários não
4. **Primeiro Acesso:** Use a conta admin para configurações iniciais

---

## 🔒 Segurança

- ✅ Senhas armazenadas com bcrypt
- ✅ Sessões protegidas
- ✅ CSRF protection ativo
- ⚠️ **Troque as senhas em produção!**

---

**📅 Credenciais válidas para:** Ambiente de desenvolvimento  
**🎓 Sistema JyM - TCC UNIPAR Umuarama**
