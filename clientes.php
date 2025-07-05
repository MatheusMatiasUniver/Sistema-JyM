<?php
include 'backend/conexao.php';
$stmt = $pdo->query("SELECT * FROM Cliente");
$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Clientes - Sistema JyM</title>
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
  <h1>Lista de Clientes</h1>
  <a href="cadastrar_cliente.php"><button>Adicionar Novo Cliente</button></a>
  <table>
    <tr>
      <th>CPF</th>
      <th>Nome</th>
      <th>Email</th>
      <th>Telefone</th>
      <th>Data de Nascimento</th>
      <th>Plano</th>
      <th>Ações</th>
    </tr>
    <?php foreach ($clientes as $cliente): ?>
    <tr>
      <td><?php echo $cliente['cpf']; ?></td>
      <td><?php echo $cliente['nome']; ?></td>
      <td><?php echo $cliente['email']; ?></td>
      <td><?php echo $cliente['telefone']; ?></td>
      <td><?php echo $cliente['dataNascimento']; ?></td>
      <td><?php echo $cliente['plano']; ?></td>
      <td>
        <a href="editar_cliente.php?cpf=<?php echo $cliente['cpf']; ?>">Editar</a> |
        <a href="backend/clientes/excluir.php?cpf=<?php echo $cliente['cpf']; ?>">Excluir</a>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
</div>
</body>
</html>