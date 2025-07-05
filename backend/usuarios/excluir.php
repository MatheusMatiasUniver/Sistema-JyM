<?php
session_start();
include '../conexao.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: ../../index.html');
    exit();
}

$id = $_GET['id'];

// Verificar permissão
if ($_SESSION['usuario']['nivel'] != 'Administrador' && $_SESSION['usuario']['id'] != $id) {
    echo "<script>alert('Permissão negada. Apenas administradores podem excluir outros usuários.'); window.history.back();</script>";
    exit();
}

// Excluir usuário
$stmt = $pdo->prepare("DELETE FROM Usuario WHERE idUsuario = ?");
$stmt->execute([$id]);

// Se o usuário excluiu a si mesmo, faz logout
if ($_SESSION['usuario']['id'] == $id) {
    session_destroy();
    header('Location: ../../index.html');
    exit();
}

header('Location: ../../usuario.php');
?>