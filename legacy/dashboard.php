<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: index.html");
  exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - Sistema JyM</title>
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
    <h1>Bem‑vindo ao Sistema JyM</h1>
    <p>Selecione uma opção no menu lateral para começar.</p>
  </div>
</body>
</html>
