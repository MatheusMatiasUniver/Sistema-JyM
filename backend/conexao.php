<?php
// Configurações do banco
$host = 'localhost';
$db = 'jym';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

// String de conexão
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// Opções de configuração do PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // Log detalhado do erro
    $errorMsg = "Erro de conexão DB - " . date('Y-m-d H:i:s') . " - " . $e->getMessage();
    error_log($errorMsg);
    
    // Verifica se é uma requisição AJAX
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
              strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    
    if ($isAjax) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(500);
        echo json_encode([
            "status" => "erro", 
            "mensagem" => "Serviço temporariamente indisponível. Tente novamente em alguns minutos.",
            "codigo" => "DB_CONNECTION_ERROR"
        ]);
        exit();
    } else {
        // Para páginas web normais
        die("Sistema temporariamente indisponível. Tente novamente em alguns minutos.");
    }
}
?>