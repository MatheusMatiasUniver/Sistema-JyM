<?php
session_start();
include '../conexao.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: ../../index.html');
    exit();
}

$id = $_POST['id'];

// Verificar permissão
if ($_SESSION['usuario']['nivel'] != 'Administrador' && $_SESSION['usuario']['id'] != $id) {
    echo "<script>alert('Permissão negada. Apenas administradores podem editar outros usuários.'); window.history.back();</script>";
    exit();
}

$nome = $_POST['nome'];
$email = $_POST['email'];
$nivel = $_POST['nivel'];
$senha = $_POST['senha'];

// Atualizar dados
if (!empty($senha)) {
    $stmt = $pdo->prepare("UPDATE Usuario SET nome=?, email=?, senha=?, nivelAcesso=? WHERE idUsuario=?");
    $stmt->execute([$nome, $email, $senha, $nivel, $id]);
} else {
    $stmt = $pdo->prepare("UPDATE Usuario SET nome=?, email=?, nivelAcesso=? WHERE idUsuario=?");
    $stmt->execute([$nome, $email, $nivel, $id]);
}

header('Location: ../../usuario.php');
?>