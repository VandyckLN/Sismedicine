<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}

include("conexao.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $usuario = trim($_POST['usuario']);
    $senha = $_POST['senha'];

    if (empty($usuario)) {
        header("Location: editar_usuario.php?id=$id&erro=1");
        exit;
    }

    // Atualiza usuário e senha (se informada)
    if (!empty($senha)) {
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE usuarios SET usuario = ?, senha = ? WHERE id = ?");
        $stmt->bind_param("ssi", $usuario, $senha_hash, $id);
    } else {
        $stmt = $conn->prepare("UPDATE usuarios SET usuario = ? WHERE id = ?");
        $stmt->bind_param("si", $usuario, $id);
    }

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: cadastro_usuario.php?sucesso=1");
        exit;
    } else {
        $stmt->close();
        $conn->close();
        header("Location: editar_usuario.php?id=$id&erro=2");
        exit;
    }
} else {
    header("Location: cadastro_usuario.php");
    exit;
}
?>

<div class="container">
    <?php if (isset($_GET['sucesso']) && $_GET['sucesso'] == 1): ?>
        <div class="sucesso">Usuário editado com sucesso!</div>
    <?php endif; ?>
    <form action="processa_cadastro_usuario.php" method="POST">
        <!-- ... -->
    </form>
</div>