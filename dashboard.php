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
      <a href="usuario.php">Usuário</a>
      <a href="backend/logout.php">Logout</a>
    </nav>
  </aside>
  <div class="main">
    <h1>Bem‑vindo ao Sistema JyM</h1>
    <p>Selecione uma opção no menu lateral para começar.</p>
  </div>
</body>
</html>
