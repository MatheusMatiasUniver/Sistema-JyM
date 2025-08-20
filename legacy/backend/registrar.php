<?php

include 'conexao.php';
session_start();
include '../helpers.php';

$data = json_decode(file_get_contents("php://input"), true);

$nome = $data['nome'] ?? '';
$usuario = $data['usuario'] ?? ''; 
$email = filter_var($data['email'] ?? '', FILTER_SANITIZE_EMAIL); 
$senha = $data['senha'] ?? '';
$nivel = $data['nivel'] ?? '';

$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

$erros = [];

if (empty($nome) || strlen($nome) < 3) {
    $erros[] = 'Nome completo inválido! Deve conter pelo menos 3 caracteres.';
}
if (empty($usuario) || strlen($usuario) < 3) {
    $erros[] = 'Nome de usuário inválido! Deve conter pelo menos 3 caracteres.';
}

if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erros[] = 'Email fornecido é inválido!';
} else if (empty($email)) {
    $email = null; 
}
if (empty($senha) || strlen($senha) < 6) { 
    $erros[] = 'Senha inválida! Deve conter pelo menos 6 caracteres.';
}
$niveisPermitidos = ['Funcionario', 'Administrador'];
if (empty($nivel) || !in_array($nivel, $niveisPermitidos)) {
    $erros[] = 'Nível de acesso inválido!';
}

if (!empty($erros)) {
    set_flash_message('danger', implode('<br>', $erros));
    echo json_encode(["status" => "erro", "mensagem" => implode("\n", $erros)]);
    exit();
}

$stmt = $pdo->prepare("SELECT COUNT(*) FROM Usuario WHERE usuario = ?");
$stmt->execute([$usuario]);
$usuarioExiste = $stmt->fetchColumn();

if ($usuarioExiste > 0) {
    set_flash_message('danger', 'Este nome de usuário já está em uso.');
    echo json_encode(["status" => "erro", "mensagem" => "Este nome de usuário já está em uso."]);
    exit();
}

$stmt = $pdo->prepare("INSERT INTO Usuario (nome, usuario, email, senha, nivelAcesso) VALUES (?, ?, ?, ?, ?)");
if($stmt->execute([$nome, $usuario, $email, $senhaHash, $nivel])) {
    set_flash_message('success', 'Usuário cadastrado com sucesso!');
    echo json_encode(["status" => "sucesso"]);
} else {
    set_flash_message('danger', 'Falha ao cadastrar usuário. Por favor, tente novamente.');
    echo json_encode(["status" => "erro", "mensagem" => "Falha ao cadastrar usuário."]);
}
?>