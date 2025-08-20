<?php
include 'backend/conexao.php';
$cpf = $_GET['cpf'];
$stmt = $pdo->prepare("SELECT * FROM Cliente WHERE cpf = ?");
$stmt->execute([$cpf]);
$cliente = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Editar Cliente - Sistema JyM</title>
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
  <h1>Editar Cliente</h1>
  <form action="backend/clientes/editar.php" method="POST">
    <input type="hidden" name="cpf" value="<?php echo $cliente['cpf']; ?>">
    <input type="text" name="nome" value="<?php echo $cliente['nome']; ?>" required>
    <input type="email" name="email" value="<?php echo $cliente['email']; ?>" required>
    <input type="text" name="telefone" value="<?php echo $cliente['telefone']; ?>">
    <input type="date" name="dataNascimento" value="<?php echo $cliente['dataNascimento']; ?>">
    <select name="plano" required>
      <option value="Assinante" <?php if($cliente['plano']=='Assinante') echo 'selected'; ?>>Assinante</option>
      <option value="Não Assinante" <?php if($cliente['plano']=='Não Assinante') echo 'selected'; ?>>Não Assinante</option>
    </select>
    <button type="submit">Salvar Alterações</button>
  </form>
</div>
</body>
</html>