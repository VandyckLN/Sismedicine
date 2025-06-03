<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit;
}

include("conexao.php");

$id = intval($_POST['id']);
$nome = isset($_POST['nome']) ? $conn->real_escape_string(trim($_POST['nome'])) : '';
$descricao = isset($_POST['descricao']) ? $conn->real_escape_string(trim($_POST['descricao'])) : '';
$validade = isset($_POST['validade']) ? $_POST['validade'] : '';

if ($nome == '' || $validade == '') {
    header("Location: editar_medicamento.php?id=$id&erro=1");
    exit;
}

// Atualiza no banco
$sql = "UPDATE medicamentos SET nome = ?, descricao = ?, validade = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssi", $nome, $descricao, $validade, $id);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    header("Location: listar_medicamentos.php?editado=1");
    exit;
} else {
    $stmt->close();
    $conn->close();
    header("Location: editar_medicamento.php?id=$id&erro=1");
    exit;
}
?>
