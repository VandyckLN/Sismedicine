<?php
// Ativar exibição de erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("conexao.php");

echo "<h2>Verificação da Estrutura da Tabela</h2>";

// Verificar se a tabela 'usuarios' existe
$result = $conn->query("SHOW TABLES LIKE 'usuarios'");

if ($result->num_rows === 0) {
    echo "<p style='color:red'>✗ A tabela 'usuarios' não existe!</p>";
    
    // Criar a tabela
    echo "<p>Tentando criar a tabela 'usuarios'...</p>";
    
    $sql_criar = "CREATE TABLE usuarios (
        id INT(11) NOT NULL AUTO_INCREMENT,
        nome VARCHAR(100) NOT NULL,
        usuario VARCHAR(50) NOT NULL UNIQUE,
        senha VARCHAR(255) NOT NULL,
        nivel_acesso ENUM('Administrador','Farmaceutico') NOT NULL,
        PRIMARY KEY (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    
    if ($conn->query($sql_criar) === TRUE) {
        echo "<p style='color:green'>✓ Tabela 'usuarios' criada com sucesso!</p>";
        
        // Inserir o usuário admin
        $nome = "Administrador";
        $usuario = "admin";
        $senha = password_hash("123456", PASSWORD_DEFAULT);
        $nivel = "Administrador";
        
        $sql_inserir = "INSERT INTO usuarios (nome, usuario, senha, nivel_acesso) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql_inserir);
        $stmt->bind_param("ssss", $nome, $usuario, $senha, $nivel);
        
        if ($stmt->execute()) {
            echo "<p style='color:green'>✓ Usuário admin criado com sucesso!</p>";
        } else {
            echo "<p style='color:red'>✗ Erro ao criar o usuário admin: " . $stmt->error . "</p>";
        }
    } else {
        echo "<p style='color:red'>✗ Erro ao criar a tabela: " . $conn->error . "</p>";
    }
} else {
    echo "<p style='color:green'>✓ A tabela 'usuarios' existe.</p>";
    
    // Verificar a estrutura da tabela
    $result = $conn->query("DESCRIBE usuarios");
    echo "<h3>Estrutura da tabela 'usuarios':</h3>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Chave</th><th>Padrão</th><th>Extra</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Default']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Extra']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Listar os usuários
    $result = $conn->query("SELECT id, nome, usuario, senha, nivel_acesso FROM usuarios");
    echo "<h3>Usuários cadastrados:</h3>";
    
    if ($result->num_rows > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Nome</th><th>Usuário</th><th>Hash da Senha</th><th>Nível de Acesso</th></tr>";
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['nome']) . "</td>";
            echo "<td>" . htmlspecialchars($row['usuario']) . "</td>";
            echo "<td>" . htmlspecialchars(substr($row['senha'], 0, 15) . "...") . "</td>";
            echo "<td>" . htmlspecialchars($row['nivel_acesso']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color:red'>✗ Nenhum usuário encontrado!</p>";
    }
}

echo "<p><a href='login.php'>Voltar para a página de login</a></p>";
?>