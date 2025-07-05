<?php
session_start();
include 'conexao.php';

$data = json_decode(file_get_contents("php://input"), true);
$email = $data['email'];
$senha = $data['senha'];

$stmt = $pdo->prepare("SELECT * FROM Usuario WHERE email = ? AND senha = ?");
$stmt->execute([$email, $senha]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if ($usuario) {
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