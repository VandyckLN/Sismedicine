<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit;
}

include("conexao.php");

// Ativa mensagens de erro MySQLi para ajudar no debug
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$nome = isset($_POST['nome']) ? $conn->real_escape_string(trim($_POST['nome'])) : '';
$descricao = isset($_POST['descricao']) ? $conn->real_escape_string(trim($_POST['descricao'])) : '';
$validade = isset($_POST['validade']) ? $_POST['validade'] : '';

// Validação simples
if ($nome == '' || $validade == '') {
    header("Location: cadastro_medicamento.php?erro=1");
    exit;
}

// Verifica se validade está no formato correto (YYYY-MM-DD)
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $validade)) {
    header("Location: cadastro_medicamento.php?erro=2"); // erro formato validade
    exit;
}

// Prepara a query (sem quantidade)
$sql = "INSERT INTO medicamentos (nome, descricao, validade) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Erro no prepare: " . $conn->error);
}

$stmt->bind_param("sss", $nome, $descricao, $validade);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    header("Location: cadastro_medicamento.php?sucesso=1");
    exit;
} else {
    $stmt->close();
    $conn->close();
    header("Location: cadastro_medicamento.php?erro=3"); // erro execução
    exit;
}
