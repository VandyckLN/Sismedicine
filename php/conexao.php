<?php
// Ativar exibição de erros (remova em produção)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Definição das variáveis de conexão
$servidor = "localhost"; // ou 127.0.0.1
$usuario_bd = "root";  // usuário padrão do XAMPP
$senha_bd = "";       // senha padrão do XAMPP é vazia
$banco = "farmacia_hospitalar"; // nome do banco de dados importado do arquivo SQL

try {
    // Cria a conexão
    $conn = new mysqli($servidor, $usuario_bd, $senha_bd, $banco);

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
