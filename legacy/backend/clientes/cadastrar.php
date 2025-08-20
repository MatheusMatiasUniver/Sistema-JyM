<?php

include '../conexao.php'; 
session_start(); 
include '../helpers.php'; 

$cpf = filter_input(INPUT_POST, 'cpf', FILTER_SANITIZE_NUMBER_INT); 
$nome = $_POST['nome'] ?? ''; 
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL); 
$telefone = filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_NUMBER_INT); 
$dataNascimento = $_POST['dataNascimento'] ?? ''; 
$plano = $_POST['plano'] ?? ''; 

$erros = [];

if (empty($cpf) || strlen($cpf) !== 11 || !ctype_digit($cpf)) {
    $erros[] = 'CPF inválido! Deve conter exatamente 11 dígitos numéricos.';
}

if (empty($nome) || strlen($nome) < 3) {
    $erros[] = 'Nome inválido! Deve conter pelo menos 3 caracteres.';
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erros[] = 'Email inválido!';
}

if (empty($telefone) || strlen($telefone) < 8 || strlen($telefone) > 11 || !ctype_digit($telefone)) {
    $erros[] = 'Telefone inválido! Deve conter entre 8 e 11 dígitos numéricos.';
}

if (empty($dataNascimento) || !preg_match("/^\d{4}-\d{2}-\d{2}$/", $dataNascimento)) {
    $erros[] = 'Data de Nascimento inválida! Formato esperado: YYYY-MM-DD.';
}

$planosPermitidos = ['Assinante', 'Não Assinante'];
if (empty($plano) || !in_array($plano, $planosPermitidos)) {
    $erros[] = 'Plano inválido!';
}

if (!empty($erros)) {
    set_flash_message('danger', implode('<br>', $erros));
    header("Location: ../../cadastrar_cliente.php");
    exit(); 
}

$stmt = $pdo->prepare("SELECT COUNT(*) FROM Cliente WHERE cpf = ?");
$stmt->execute([$cpf]);
$existe = $stmt->fetchColumn();

if ($existe > 0) {
    set_flash_message('danger', 'Erro: Este CPF já está cadastrado.');
    header("Location: ../../cadastrar_cliente.php");
    exit();
}

$stmt = $pdo->prepare("INSERT INTO Cliente (cpf, nome, email, telefone, dataNascimento, plano) VALUES (?, ?, ?, ?, ?, ?)");
if ($stmt->execute([$cpf, $nome, $email, $telefone, $dataNascimento, $plano])) {
    set_flash_message('success', 'Cliente cadastrado com sucesso!');
    header("Location: ../../clientes.php");
    exit(); 
} else {
    set_flash_message('danger', 'Erro desconhecido ao cadastrar cliente. Tente novamente.');
    header("Location: ../../cadastrar_cliente.php");
    exit(); 
}

?>