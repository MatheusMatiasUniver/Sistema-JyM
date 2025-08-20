<?php
session_start();
include 'backend/conexao.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: index.html');
    exit();
}

$stmt = $pdo->query("SELECT * FROM Usuario");
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Usuários - Sistema JyM</title>
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
  <h1>Informações do Usuário Logado</h1>
  <p><strong>Nome:</strong> <?php echo $_SESSION['usuario']['nome']; ?></p>
  <p><strong>Nível de Acesso:</strong> <?php echo $_SESSION['usuario']['nivel']; ?></p>

  <h2>Lista de Usuários Cadastrados</h2>
  <table>
    <tr>
      <th>Nome</th>
      <th>Email</th>
      <th>Nível</th>
      <th>Ações</th>
    </tr>
    <?php foreach ($usuarios as $usuario): ?>
    <tr>
      <td><?php echo $usuario['nome']; ?></td>
      <td><?php echo $usuario['email']; ?></td>
      <td><?php echo $usuario['nivelAcesso']; ?></td>
      <td>
        <?php if (isset($_SESSION['usuario']) && $_SESSION['usuario']['nivel'] === 'Administrador'): ?>
          <a href="editar_usuario.php?id=<?php echo $usuario['idUsuario']; ?>">Editar</a> |
          <a href="backend/usuarios/excluir.php?id=<?php echo $usuario['idUsuario']; ?>" onclick="return confirm('Tem certeza que deseja excluir este usuário?');">Excluir</a>
        <?php endif; ?>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
</div>
</body>
</html>