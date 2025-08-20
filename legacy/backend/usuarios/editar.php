<?php

session_start();
include '../conexao.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: ../../index.html');
    exit();
}

$id = $_POST['id'] ?? '';
$nome = $_POST['nome'] ?? '';
$email = $_POST['email'] ?? '';
$nivel = $_POST['nivel'] ?? '';
$senha = $_POST['senha'] ?? ''; 

$sql = "UPDATE Usuario SET nome=?, email=?, nivelAcesso=? WHERE idUsuario=?";
$params = [$nome, $email, $nivel, $id];

if (!empty($senha)) {
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT); 
    $sql = "UPDATE Usuario SET nome=?, email=?, senha=?, nivelAcesso=? WHERE idUsuario=?";
    $params = [$nome, $email, $senhaHash, $nivel, $id]; 
}

if ($_SESSION['usuario']['nivel'] != 'Administrador' && $_SESSION['usuario']['id'] != $id) {
    echo "<script>alert('Permissão negada. Apenas administradores podem editar outros usuários.'); window.history.back();</script>";
    exit();
}


$stmt = $pdo->prepare($sql);
$stmt->execute($params);

header('Location: ../../usuario.php');
exit(); 
?>