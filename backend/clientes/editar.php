<?php
include '../conexao.php';
$cpf = $_POST['cpf'];
$nome = $_POST['nome'];
$email = $_POST['email'];
$telefone = $_POST['telefone'];
$data = $_POST['dataNascimento'];
$plano = $_POST['plano'];

$stmt = $pdo->prepare("UPDATE Cliente SET nome=?, email=?, telefone=?, dataNascimento=?, plano=? WHERE cpf=?");
$stmt->execute([$nome, $email, $telefone, $data, $plano, $cpf]);
header("Location: ../../clientes.php");
?>