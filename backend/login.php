<?php

session_start();
include 'conexao.php';
include '../helpers.php'; 

$data = json_decode(file_get_contents("php://input"), true);

$usuarioInput = filter_var($data['usuario'] ?? ''); 
$senhaInput = $data['senha'] ?? '';

$stmt = $pdo->prepare("SELECT idUsuario, nome, usuario, senha, nivelAcesso FROM Usuario WHERE usuario = ?");
$stmt->execute([$usuarioInput]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if ($usuario && password_verify($senhaInput, $usuario['senha'])) {
    $_SESSION['usuario'] = [
        'id' => $usuario['idUsuario'],
        'nome' => $usuario['nome'],
        'nivel' => $usuario['nivelAcesso']
    ];
    set_flash_message('success', 'Login realizado com sucesso!');
    echo json_encode(["status" => "sucesso", "redirect" => "dashboard.php"]);
} else {
    set_flash_message('danger', 'Credenciais inválidas.');
    echo json_encode(["status" => "erro", "mensagem" => "Credenciais inválidas"]);
}
?>