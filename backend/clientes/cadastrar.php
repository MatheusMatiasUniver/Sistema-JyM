<?php
include '../conexao.php';

$cpf = $_POST['cpf'];
$nome = $_POST['nome'];
$email = $_POST['email'];
$telefone = $_POST['telefone'];
$data = $_POST['dataNascimento'];
$plano = $_POST['plano'];

// Verificar se o CPF já existe
$stmt = $pdo->prepare("SELECT COUNT(*) FROM Cliente WHERE cpf = ?");
$stmt->execute([$cpf]);
$existe = $stmt->fetchColumn();

if ($existe > 0) {
    echo "<script>alert('Erro: Este CPF já está cadastrado.'); window.history.back();</script>";
    exit();
}

// Prosseguir com o cadastro
$stmt = $pdo->prepare("INSERT INTO Cliente (cpf, nome, email, telefone, dataNascimento, plano) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->execute([$cpf, $nome, $email, $telefone, $data, $plano]);
header("Location: ../../clientes.php");
?>