<?php
<?php
session_start();

// Verifica se o usuário tem sessão e está pendente de verificação
if (!isset($_SESSION['usuario']) || !isset($_SESSION['verificacao_pendente']) || $_SESSION['verificacao_pendente'] !== true) {
    // Se não estiver aguardando verificação, redireciona para o login
    header("Location: login.php");
    exit;
}

// Arquivo de log para debugging
$log_file = "../logs/login_debug.log";

// Função para registrar no log
function log_message($message) {
    global $log_file;
    $timestamp = date("[Y-m-d H:i:s]");
    file_put_contents($log_file, "$timestamp $message\n", FILE_APPEND);
}

// Verifica se recebemos o código de verificação
$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['codigo']) && $_POST['codigo'] === $_SESSION['codigo_verificacao']) {
        // Código correto - Marca como verificado e redireciona para dashboard
        $_SESSION['verificacao_pendente'] = false;
        
        // Registra o sucesso no log
        log_message("Verificação em dois fatores bem-sucedida para o usuário: " . $_SESSION['usuario']);
        
        // Redireciona para o dashboard
        header("Location: dashboard.php");
        exit;
    } else {
        // Código inválido
        $error = "Código de verificação inválido. Por favor, tente novamente.";
        log_message("Tentativa de verificação falha para o usuário: " . $_SESSION['usuario']);
    }
}

// No início do arquivo após session_start()
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIS Medicine - Verificação de Segurança</title>
    <link rel="stylesheet" href="../css/verificacao.css">
</head>
<body>
    <div class="overlay"></div>
    
    <div class="verificacao-container">
        <div class="verificacao-logo">SIS Medicine</div>
        <div class="verificacao-title">Verificação de Segurança</div>
        
        <div class="verificacao-content">
            <div class="mensagem-info">
                <p>Olá, <strong><?php echo htmlspecialchars($_SESSION['usuario']); ?></strong>!</p>
                <p>Como administrador do sistema, é necessária uma verificação adicional.</p>
                <p>Seu código de verificação é:</p>
                
                <!-- Exibe o código de verificação de forma destacada -->
                <div class="codigo-display"><?php echo chunk_split($_SESSION['codigo_verificacao'], 3, ' '); ?></div>
                
                <div class="aviso">
                    <strong>Nota:</strong> Em um ambiente de produção, este código seria enviado para seu e-mail ou telefone celular cadastrado.
                </div>
            </div>
            
            <form class="verificacao-form" method="post" action="">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <?php if ($error): ?>
                    <div class="error-message"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <label for="codigo">Digite o código de verificação:</label>
                <input type="text" id="codigo" name="codigo" maxlength="6" pattern="[0-9]{6}" 
                       placeholder="Digite o código de 6 dígitos" required autofocus>
                
                <div class="form-actions">
                    <button type="submit">Verificar</button>
                    <a href="logout.php" class="cancel-link">Cancelar</a>
                </div>
            </form>
        </div>
        
        <div class="verificacao-footer">
            &copy; <?php echo date('Y'); ?> Grupo-04 - SIS Medicine
        </div>
    </div>
    
    <script src="../js/verificacao.js"></script>
</body>
</html>