<?php
session_start();
include 'backend/conexao.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: index.html');
    exit();
}

$id = $_GET['id'];

// Verificar se o usuário tem permissão
if ($_SESSION['usuario']['nivel'] != 'Administrador' && $_SESSION['usuario']['id'] != $id) {
    echo "<script>alert('Permissão negada. Apenas administradores podem editar outros usuários.'); window.history.back();</script>";
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM Usuario WHERE idUsuario = ?");
$stmt->execute([$id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Editar Usuário - Sistema JyM</title>
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
  <h1>Editar Usuário</h1>
  <form action="backend/usuarios/editar.php" method="POST">
    <input type="hidden" name="id" value="<?php echo $usuario['idUsuario']; ?>">
    <input type="text" name="nome" value="<?php echo $usuario['nome']; ?>" required>
    <input type="email" name="email" value="<?php echo $usuario['email']; ?>" required>
    <input type="password" name="senha" placeholder="Nova Senha (deixe em branco para não alterar)">
    <select name="nivel">
      <option value="Funcionario" <?php if($usuario['nivelAcesso']=='Funcionario') echo 'selected'; ?>>Funcionário</option>
      <option value="Administrador" <?php if($usuario['nivelAcesso']=='Administrador') echo 'selected'; ?>>Administrador</option>
    </select>
    <button type="submit">Salvar Alterações</button>
  </form>
</div>
</body>
</html>