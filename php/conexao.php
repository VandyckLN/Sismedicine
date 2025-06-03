<?php
// Ativar exibição de erros (remova em produção)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host = "localhost";
$usuario = "root"; 
$senha = "";
$banco = "farmacia_hospitalar";

try {
    // Cria a conexão
    $conn = new mysqli($host, $usuario, $senha, $banco);

    // Verifica a conexão
    if ($conn->connect_error) {
        throw new Exception("Falha na conexão: " . $conn->connect_error);
    }

    // Define o conjunto de caracteres
    $conn->set_charset("utf8");
} catch (Exception $e) {
    die("Erro de conexão: " . $e->getMessage());
}
?>
