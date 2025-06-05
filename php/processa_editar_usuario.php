<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit;
}

include("conexao.php");

// Validação de senha no lado do servidor
function validarSenhaForte($senha) {
    // Pular validação se a senha estiver vazia (nenhuma alteração)
    if (empty($senha)) {
        return true;
    }
    
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
    $id = $_POST['id'] ?? 0;
    $usuario = $_POST['usuario'] ?? '';
    $senha = $_POST['senha'] ?? '';
    
    // Validar ID e usuário
    if (empty($id) || empty($usuario)) {
        header("Location: cadastro_usuario.php?erro=CamposVazios");
        exit;
    }
    
    // Validar força da senha se foi informada
    if (!empty($senha) && !validarSenhaForte($senha)) {
        header("Location: editar_usuario.php?id=$id&erro=senha_fraca");
        exit;
    }
    
    // Verificar se o nome de usuário já existe (exceto para o próprio usuário)
    $sql = "SELECT id FROM usuarios WHERE usuario = ? AND id != ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $usuario, $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $stmt->close();
        header("Location: editar_usuario.php?id=$id&erro=UsuarioJaExiste");
        exit;
    }
    $stmt->close();
    
    // Preparar a atualização do usuário
    if (!empty($senha)) {
        // Atualizar usuário e senha
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET usuario = ?, senha = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $usuario, $senha_hash, $id);
    } else {
        // Atualizar apenas o nome de usuário
        $sql = "UPDATE usuarios SET usuario = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $usuario, $id);
    }
    
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: editar_usuario.php?id=$id&sucesso=true");
    } else {
        $stmt->close();
        $conn->close();
        header("Location: editar_usuario.php?id=$id&erro=" . urlencode($conn->error));
    }
} else {
    header("Location: cadastro_usuario.php");
}
exit;
?>

<div class="container">
    <?php if (isset($_GET['sucesso']) && $_GET['sucesso'] == 1): ?>
        <div class="sucesso">Usuário editado com sucesso!</div>
    <?php endif; ?>
    <form action="processa_cadastro_usuario.php" method="POST">
        <!-- ... -->
    </form>
</div>