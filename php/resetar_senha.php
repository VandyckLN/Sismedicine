<?php
// Ativar exibição de erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Conexão com o banco de dados
$host = "localhost";
$usuario = "root"; 
$senha = "";
$banco = "farmacia_hospitalar";

try {
    $conn = new mysqli($host, $usuario, $senha, $banco);
    
    if ($conn->connect_error) {
        throw new Exception("Falha na conexão: " . $conn->connect_error);
    }
    
    $usuario_admin = "admin";
    $nova_senha = "123456";
    $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
    
    echo "<h2>Redefinição de Senha</h2>";
    echo "<p>Usuário: " . htmlspecialchars($usuario_admin) . "</p>";
    echo "<p>Nova Senha: " . htmlspecialchars($nova_senha) . "</p>";
    echo "<p>Hash Gerado: " . htmlspecialchars($senha_hash) . "</p>";
    
    $sql = "UPDATE usuarios SET senha = ? WHERE usuario = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception("Erro ao preparar a consulta: " . $conn->error);
    }
    
    $stmt->bind_param("ss", $senha_hash, $usuario_admin);
    
    if ($stmt->execute()) {
        echo "<p style='color:green'>✓ Senha do usuário admin redefinida com sucesso!</p>";
    } else {
        throw new Exception("Erro ao executar a consulta: " . $stmt->error);
    }
    
    $stmt->close();
    $conn->close();
    
    echo "<p><a href='login.php'>Voltar para a página de login</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color:red'>✗ " . $e->getMessage() . "</p>";
}
?>