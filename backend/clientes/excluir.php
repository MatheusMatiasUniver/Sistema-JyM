<?php
include '../conexao.php';
$cpf = $_GET['cpf'];
$stmt = $pdo->prepare("DELETE FROM Cliente WHERE cpf = ?");
$stmt->execute([$cpf]);
header("Location: ../../clientes.php");
?>