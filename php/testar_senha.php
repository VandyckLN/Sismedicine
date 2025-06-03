<?php
// Ativar exibição de erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("conexao.php");

// Buscar o usuário admin
$sql = "SELECT id, usuario, senha FROM usuarios WHERE usuario = 'admin'";
$result = $conn->query($sql);

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    
    echo "<h2>Teste de Verificação de Senha</h2>";
    echo "<p>Usuário: " . htmlspecialchars($row['usuario']) . " (ID: " . $row['id'] . ")</p>";
    echo "<p>Hash armazenado: " . htmlspecialchars($row['senha']) . "</p>";
    
    // Testar a senha "123456"
    $senha_teste = "123456";
    $senha_correta = password_verify($senha_teste, $row['senha']);
    
    echo "<p>Testando a senha \"123456\": " . ($senha_correta ? "✓ Correta" : "✗ Incorreta") . "</p>";
    
    if (!$senha_correta) {
        // Gerar um novo hash e atualizar o banco
        $novo_hash = password_hash($senha_teste, PASSWORD_DEFAULT);
        echo "<p>Novo hash gerado: " . htmlspecialchars($novo_hash) . "</p>";
        
        $sql_update = "UPDATE usuarios SET senha = ? WHERE id = ?";
        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param("si", $novo_hash, $row['id']);
        
        if ($stmt->execute()) {
            echo "<p style='color:green'>✓ Senha atualizada com sucesso. Tente fazer login novamente.</p>";
        } else {
            echo "<p style='color:red'>✗ Erro ao atualizar a senha: " . $stmt->error . "</p>";
        }
    }
} else {
    echo "<p style='color:red'>✗ Usuário admin não encontrado!</p>";
}

echo "<p><a href='login.php'>Voltar para a página de login</a></p>";
?>