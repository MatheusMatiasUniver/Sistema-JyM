<?php

session_start(); 
include 'conexao.php';

$data = json_decode(file_get_contents("php://input"), true);

$email = filter_var($data['email'] ?? '', FILTER_SANITIZE_EMAIL); 
$senhaInput = $data['senha'] ?? ''; 

$stmt = $pdo->prepare("SELECT idUsuario, nome, senha, nivelAcesso FROM Usuario WHERE email = ?");
$stmt->execute([$email]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if ($usuario && password_verify($senhaInput, $usuario['senha'])) {    
    $_SESSION['usuario'] = [
        'id' => $usuario['idUsuario'],
        'nome' => $usuario['nome'],
        'nivel' => $usuario['nivelAcesso']
    ];
    echo json_encode(["status" => "sucesso", "redirect" => "dashboard.php"]);
} else {    
    echo json_encode(["status" => "erro", "mensagem" => "Credenciais inválidas"]);
}

?>