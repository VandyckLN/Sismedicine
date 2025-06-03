<?php
// Mostrar todos os erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$mensagem = "";
$tipo_mensagem = "";
$hash_gerado = "";
$erro_usuario = false;
$erro_senha = false;

// Processo de atualização de senha quando o formulário é enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Conexão com o banco de dados
    $conn = new mysqli("localhost", "root", "", "farmacia_hospitalar");

    // Verifica a conexão
    if ($conn->connect_error) {
        $mensagem = "Falha na conexão: " . $conn->connect_error;
        $tipo_mensagem = "erro";
    } else {
        // Dados para atualização
        $usuario = $_POST['usuario'];
        $nova_senha = $_POST['senha'];
        
        // Verificar se o usuário existe
        $check_sql = "SELECT id FROM usuarios WHERE usuario = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $usuario);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows === 0) {
            $mensagem = "Usuário não encontrado: $usuario";
            $tipo_mensagem = "erro";
            $erro_usuario = true;
        } else {
            // Validar senha (pelo menos 6 caracteres)
            if (strlen($nova_senha) < 6) {
                $mensagem = "A senha deve ter pelo menos 6 caracteres.";
                $tipo_mensagem = "erro";
                $erro_senha = true;
            } else {
                $senha_hash = password_hash($nova_senha, PASSWORD_BCRYPT);
                
                // Guarda o hash para mostrar
                $hash_gerado = $senha_hash;

                // Prepara e executa a atualização
                $sql = "UPDATE usuarios SET senha = ? WHERE usuario = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ss", $senha_hash, $usuario);

                if ($stmt->execute()) {
                    $mensagem = "Senha atualizada com sucesso para o usuário $usuario!";
                    $tipo_mensagem = "sucesso";
                } else {
                    $mensagem = "Erro ao atualizar a senha: " . $stmt->error;
                    $tipo_mensagem = "erro";
                }
                $stmt->close();
            }
        }
        $check_stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resetar Senha - SisMedicine</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/resetar_senha.css">
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h6><i class="fas fa-key"></i> Resetar Senha</h6>
            </div>
            <div class="card-body">
                <?php if (!empty($mensagem)): ?>
                    <div class="alert <?php echo $tipo_mensagem === 'sucesso' ? 'alert-success' : 'alert-danger'; ?>">
                        <?php echo $mensagem; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" id="resetForm">
                    <div class="form-group">
                        <label for="usuario">Nome de Usuário</label>
                        <input type="text" class="form-control <?php if ($erro_usuario) echo 'is-invalid'; ?>" 
                               id="usuario" name="usuario" required>
                        <div class="invalid-feedback">
                            Este usuário não existe no sistema.
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="senha">Nova Senha</label>
                        <input type="password" class="form-control <?php if ($erro_senha) echo 'is-invalid'; ?>" 
                               id="senha" name="senha" required>
                        <div class="invalid-feedback">
                            A senha deve ter pelo menos 6 caracteres.
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Atualizar Senha</button>
                </form>
                
                <?php if (!empty($hash_gerado)): ?>
                    <div class="code-display">
                        <strong>Hash Gerado:</strong> <?php echo $hash_gerado; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h6><i class="fas fa-info-circle"></i> Informações</h6>
            </div>
            <div class="card-body">
                <p>Esta ferramenta permite redefinir a senha de um usuário existente no sistema SisMedicine.</p>
                <p>O sistema utiliza criptografia bcrypt para armazenar as senhas de forma segura.</p>
                <p><a href="index.php"><i class="fas fa-arrow-left"></i> Voltar para o Dashboard</a></p>
            </div>
        </div>
    </div>

    <!-- Carrega o arquivo JavaScript externo -->
    <script src="js/resetar_senha.js"></script>
</body>
</html>