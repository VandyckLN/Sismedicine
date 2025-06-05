<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit;
}

include("conexao.php");

// Validação de senha no lado do servidor
function validarSenhaForte($senha) {
    // Mínimo de 8 caracteres
    if (strlen($senha) < 8) {
        return false;
    }
    
    // Pelo menos uma letra maiúscula
    if (!preg_match('/[A-Z]/', $senha)) {
        return false;
    }
    
    // Pelo menos uma letra minúscula
    if (!preg_match('/[a-z]/', $senha)) {
        return false;
    }
    
    // Pelo menos um número
    if (!preg_match('/[0-9]/', $senha)) {
        return false;
    }
    
    // Pelo menos dois caracteres especiais
    if (preg_match_all('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $senha) < 2) {
        return false;
    }
    
    return true;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'] ?? '';
    $senha = $_POST['senha'] ?? '';
    
    // Validar dados
    if (empty($usuario) || empty($senha)) {
        header("Location: cadastro_usuario.php?erro=CamposVazios");
        exit;
    }
    
    // Validar força da senha
    if (!validarSenhaForte($senha)) {
        header("Location: cadastro_usuario.php?erro=senha_fraca");
        exit;
    }
    
    // Verificar se o usuário já existe
    $sql = "SELECT id FROM usuarios WHERE usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $stmt->close();
        header("Location: cadastro_usuario.php?erro=UsuarioJaExiste");
        exit;
    }
    $stmt->close();
    
    // Hash da senha
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
    
    // Inserir no banco
    $sql = "INSERT INTO usuarios (usuario, senha) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $usuario, $senha_hash);
    
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: cadastro_usuario.php?sucesso=true");
    } else {
        $stmt->close();
        $conn->close();
        header("Location: cadastro_usuario.php?erro=" . urlencode($conn->error));
    }
} else {
    header("Location: cadastro_usuario.php");
}
exit;
?>