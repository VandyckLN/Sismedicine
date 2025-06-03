<?php
<?php
// Ativar exibição de erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Tentar conectar ao banco de dados
$host = "localhost";
$usuario = "root"; 
$senha = "";
$banco = "farmacia_hospitalar";

echo "<h2>Teste de Conexão com o Banco de Dados</h2>";

try {
    $conn = new mysqli($host, $usuario, $senha, $banco);
    
    if ($conn->connect_error) {
        throw new Exception("Falha na conexão: " . $conn->connect_error);
    }
    
    echo "<p style='color:green'>✓ Conexão bem-sucedida com o banco de dados.</p>";
    
    // Verificar a tabela 'usuarios'
    $result = $conn->query("SHOW TABLES LIKE 'usuarios'");
    if ($result->num_rows > 0) {
        echo "<p style='color:green'>✓ Tabela 'usuarios' encontrada.</p>";
        
        // Verificar se o usuário 'admin' existe
        $result = $conn->query("SELECT id, usuario, senha FROM usuarios WHERE usuario = 'admin'");
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo "<p style='color:green'>✓ Usuário 'admin' encontrado (ID: {$row['id']}).</p>";
            echo "<p>Hash da senha: " . htmlspecialchars(substr($row['senha'], 0, 15) . "...") . "</p>";
        } else {
            echo "<p style='color:red'>✗ Usuário 'admin' não encontrado.</p>";
        }
    } else {
        echo "<p style='color:red'>✗ Tabela 'usuarios' não encontrada.</p>";
    }
    
    $conn->close();
    
} catch (Exception $e) {
    echo "<p style='color:red'>✗ " . $e->getMessage() . "</p>";
}

// Verificar caminhos
echo "<h3>Verificação de Arquivos</h3>";
$files_to_check = [
    'd:/XAMPP/htdocs/sismedicine/php/login.php',
    'd:/XAMPP/htdocs/sismedicine/php/verificar_login.php',
    'd:/XAMPP/htdocs/sismedicine/php/conexao.php',
    'd:/XAMPP/htdocs/sismedicine/php/dashboard.php'
];

foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        echo "<p style='color:green'>✓ Arquivo encontrado: " . htmlspecialchars($file) . "</p>";
    } else {
        echo "<p style='color:red'>✗ Arquivo não encontrado: " . htmlspecialchars($file) . "</p>";
    }
}

// Resetar a senha do usuário admin
echo "<h3>Resetar Senha do Admin</h3>";
echo "<p>Clique <a href='resetar_senha.php'>aqui</a> para resetar a senha do usuário admin para '123456'.</p>";
?>