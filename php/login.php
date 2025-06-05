<?php
session_start();
// Redireciona se já estiver logado
if (isset($_SESSION['usuario'])) {
    header("Location: dashboard.php");
    exit;
}

// Gera token CSRF
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Inicializa variável para mensagens de erro
$mensagem_erro = "";

// Processa o formulário quando enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifica o token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $mensagem_erro = "Erro de validação do formulário. Por favor, tente novamente.";
    } else {
        // Validação básica
        $usuario = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
        $senha = isset($_POST['senha']) ? $_POST['senha'] : '';
        
        if (empty($usuario) || empty($senha)) {
            $mensagem_erro = "Por favor, preencha todos os campos.";
        } else {
            // Incluir arquivo de conexão com o banco de dados
            try {
                require_once "conexao.php";
                
                // Consulta preparada para prevenir injeção SQL
                $sql = "SELECT id, nome, usuario, senha FROM usuarios WHERE usuario = ?";
                
                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param("s", $usuario);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows === 1) {
                        $row = $result->fetch_assoc();
                        
                        // Verificar senha (assumindo que está usando password_hash)
                        if (password_verify($senha, $row['senha'])) {
                            // Senha correta, inicia a sessão
                            $_SESSION['usuario_id'] = $row['id'];
                            $_SESSION['usuario'] = $row['usuario'];
                            $_SESSION['nome'] = $row['nome'];
                            $_SESSION['ultima_atividade'] = time();
                            
                            // Regenerar ID da sessão para prevenir fixação de sessão
                            session_regenerate_id(true);
                            
                            // Redirecionar para o dashboard
                            header("Location: dashboard.php");
                            exit;
                        } else {
                            $mensagem_erro = "Senha incorreta. Tente novamente.";
                        }
                    } else {
                        $mensagem_erro = "Usuário não encontrado.";
                    }
                    
                    $stmt->close();
                } else {
                    $mensagem_erro = "Erro na preparação da consulta: " . $conn->error;
                }
                
                $conn->close();
            } catch (Exception $e) {
                $mensagem_erro = "Erro no sistema: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIS Medicine - Login</title>
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
    <div class="background-image"></div>
    <div class="background-overlay"></div>
    
    <!-- Container para mensagens animadas -->
    <div id="mensagem-container">
        <?php if (!empty($mensagem_erro)): ?>
        <div class="mensagem-erro">
            <?php echo htmlspecialchars($mensagem_erro); ?>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h2>SIS Medicine</h2>
                <p>Sistema de Gestão de Medicamentos</p>
            </div>
            
            <form class="login-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <div class="form-group">
                    <label for="usuario">Usuário</label>
                    <input type="text" id="usuario" name="usuario" required autofocus>
                </div>
                
                <div class="form-group">
                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" required>
                </div>
                
                <button type="submit" class="btn-login">Entrar</button>
            </form>
        </div>
        
        <div class="login-footer">
            &copy; <?php echo date('Y'); ?> Grupo-04
        </div>
    </div>
    
    <!-- Carrega o script JavaScript -->
    <script src="../js/login.js"></script>
</body>
</html>
