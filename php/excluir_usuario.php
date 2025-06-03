<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}

include("conexao.php");

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Opcional: impedir que o usuário exclua a si mesmo
    if ($_SESSION['usuario_id'] == $id) {
        header("Location: cadastro_usuario.php?erro=nao_pode_excluir_proprio");
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: cadastro_usuario.php?sucesso=2");
        exit;
    } else {
        $stmt->close();
        $conn->close();
        header("Location: cadastro_usuario.php?erro=3");
        exit;
    }
} else {
    header("Location: cadastro_usuario.php?erro=4");
    exit;
}
