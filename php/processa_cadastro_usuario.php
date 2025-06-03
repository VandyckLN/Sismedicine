<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}

include("conexao.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario']);
    $senha = $_POST['senha'];

    // Validação simples
    if (empty($usuario) || empty($senha)) {
        header("Location: cadastro_usuario.php?erro=1");
        exit;
    }

    // Verifica se o usuário já existe
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE usuario = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->close();
        $conn->close();
        header("Location: cadastro_usuario.php?erro=2"); // Usuário já existe
        exit;
    }
    $stmt->close();

    // Criptografa a senha
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    // Insere o novo usuário
    $stmt = $conn->prepare("INSERT INTO usuarios (usuario, senha) VALUES (?, ?)");
    $stmt->bind_param("ss", $usuario, $senha_hash);
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: cadastro_usuario.php?sucesso=1");
        exit;
    } else {
        $stmt->close();
        $conn->close();
        header("Location: cadastro_usuario.php?erro=3");
        exit;
    }
} else {
    header("Location: cadastro_usuario.php");
    exit;
}