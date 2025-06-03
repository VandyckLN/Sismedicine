<?php
session_start();
// Redireciona se já estiver logado
if (isset($_SESSION['usuario'])) {
    header("Location: dashboard.php");
    exit;
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
    <title>SIS Medicine - Login</title>
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
    <div class="overlay"></div>
    
    <!-- Container para mensagens animadas -->
    <div id="mensagem-container"></div>
    
    <div class="login-container">
        <div class="login-title">SIS Medicine</div>
        <form class="login-form" method="post" action="verificar_login.php">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <label for="usuario">Usuário</label>
            <input type="text" id="usuario" name="usuario" required autofocus>
            <label for="senha">Senha</label>
            <input type="password" id="senha" name="senha" required>
            <button type="submit">Entrar</button>
        </form>
        <div class="login-footer">
            &copy; <?php echo date('Y'); ?> Grupo-04
        </div>
    </div>
    
    <!-- Carrega o script JavaScript -->
    <script src="../js/login.js"></script>
</body>
</html>
