<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}

include("conexao.php");

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Verifica se existe dispensação para este medicamento
    $check = $conn->prepare("SELECT COUNT(*) FROM dispensacoes WHERE medicamento_id = ?");
    $check->bind_param("i", $id);
    $check->execute();
    $check->bind_result($total);
    $check->fetch();
    $check->close();

    if ($total > 0) {
        // Não permite excluir
        $conn->close();
        header("Location: listar_medicamentos.php?erro=nao_excluir_dispensado");
        exit;
    }

    // Se não houver dispensação, pode excluir
    $stmt = $conn->prepare("DELETE FROM medicamentos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

$conn->close();
header("Location: listar_medicamentos.php?msg=excluido");
exit;
?>
