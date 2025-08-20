<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Cadastrar Cliente - Sistema JyM</title>
  <link rel="stylesheet" href="assets/css/style-dashboard.css">
</head>
<body>
<aside class="sidebar">
    <img src="assets/img/logo.png" alt="Logo JyM" class="logo">
    <nav>
        <a href="dashboard.php">Visão Geral</a>
        <a href="clientes.php">Clientes</a>
        <a href="mensalidades.php">Mensalidades</a>
        <a href="entrada.php">Autorizar Entrada</a>
        <a href="produtos.php">Produtos</a>
        <a href="vendas.php">Vendas</a>
        <!-- Links abaixo visíveis apenas para Administradores -->
        <?php if (isset($_SESSION['usuario']) && $_SESSION['usuario']['nivel'] === 'Administrador'): ?>
            <a href="academias.php">Academias</a>
            <a href="planos.php">Planos de Assinatura</a>
            <a href="usuario.php">Usuários</a> 
            <a href="cadastro.php">Cadastrar Novo Usuário</a>
        <?php endif; ?>
        <a href="backend/logout.php">Logout</a>
    </nav>
</aside>

<div class="main">
  <?php display_flash_messages();?>
  <h1>Cadastrar Novo Cliente</h1>
  <form action="backend/clientes/cadastrar.php" method="POST">
    <input type="text" name="cpf" placeholder="CPF" maxlength="11" required>
    <input type="text" name="nome" placeholder="Nome" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="text" name="telefone" placeholder="Telefone" maxlength="11">
    <input type="date" name="dataNascimento" placeholder="Data de Nascimento">
    <select name="plano" required>
      <option value="Assinante">Assinante</option>
      <option value="Não Assinante">Não Assinante</option>
    </select>
    <button type="submit">Cadastrar</button>
  </form>
</div>

<script>
// Bloquear letras enquanto digita
document.querySelector('input[name="cpf"]').addEventListener('input', function(e) {
  this.value = this.value.replace(/[^0-9]/g, '');
});
document.querySelector('input[name="telefone"]').addEventListener('input', function(e) {
  this.value = this.value.replace(/[^0-9]/g, '');
});

// Validação final antes de enviar
document.querySelector("form").addEventListener("submit", function(e) {
  const cpf = document.querySelector('input[name="cpf"]').value.trim();
  const telefone = document.querySelector('input[name="telefone"]').value.trim();

  if (cpf.length !== 11) {
    alert("CPF inválido! Deve conter exatamente 11 dígitos numéricos.");
    e.preventDefault();
    return;
  }

  if (telefone.length < 8 || telefone.length > 11) {
    alert("Telefone inválido! Deve conter entre 8 e 11 dígitos numéricos.");
    e.preventDefault();
    return;
  }
});
</script>
</body>
</html>