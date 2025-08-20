<?php

function custom_error_handler($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) {
        return false;
    }

    $error_message = "[" . date("Y-m-d H:i:s") . "] ";
    $error_message .= "ERROR " . $errno . ": " . $errstr . " in " . $errfile . " on line " . $errline . "\n";

    error_log($error_message); 

    if ($errno == E_USER_ERROR || $errno == E_RECOVERABLE_ERROR) {
        if (!headers_sent()) { 
            set_flash_message('danger', 'Ocorreu um erro inesperado. Por favor, tente novamente mais tarde.');
            header('Location: /'); 
            exit();
        } else {
            echo "Um erro inesperado ocorreu. Por favor, tente novamente mais tarde.";
        }
    }
    return true; 
}

set_error_handler("custom_error_handler");

$host = 'localhost';
$db = 'jym';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    $errorMsg = "Erro de conexão DB - " . date('Y-m-d H:i:s') . " - " . $e->getMessage();
    error_log($errorMsg);
    
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
        die("Sistema temporariamente indisponível. Tente novamente em alguns minutos.");
    }
}
?>