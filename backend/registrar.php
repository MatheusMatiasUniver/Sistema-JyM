<?php

include 'conexao.php'; 

$data = json_decode(file_get_contents("php://input"), true);

$nome = $data['nome'] ?? ''; 
$email = $data['email'] ?? '';
$senha = $data['senha'] ?? '';
$nivel = $data['nivel'] ?? '';

$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("SELECT COUNT(*) FROM Usuario WHERE email = ?");
$stmt->execute([$email]);
$emailExiste = $stmt->fetchColumn();

if ($emailExiste > 0) {
    echo json_encode(["status" => "erro", "mensagem" => "Este e-mail já está cadastrado."]);
    exit();
}

$stmt = $pdo->prepare("INSERT INTO Usuario (nome, email, senha, nivelAcesso) VALUES (?, ?, ?, ?)");
if($stmt->execute([$nome, $email, $senhaHash, $nivel])) {
    echo json_encode(["status" => "sucesso"]);
} else {
    echo json_encode(["status" => "erro", "mensagem" => "Falha ao cadastrar usuário."]);
}
?>