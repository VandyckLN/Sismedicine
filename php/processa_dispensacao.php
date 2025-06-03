<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}

include("conexao.php");



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        empty($_POST['medicamento_id']) ||
        empty($_POST['quantidade']) ||
        empty($_POST['data_disp']) ||
        empty($_POST['paciente'])
    ) {
        die("Por favor, preencha todos os campos obrigatórios.");
    }

    $medicamento_id = intval($_POST['medicamento_id']);
    $quantidade = intval($_POST['quantidade']);
    $data_disp = $_POST['data_disp'];
    $observacao = trim($_POST['observacao']);
    $paciente = trim($_POST['paciente']);

    // Buscar validade do medicamento
    $med_stmt = $conn->prepare("SELECT validade FROM medicamentos WHERE id = ?");
    $med_stmt->bind_param("i", $medicamento_id);
    $med_stmt->execute();
    $med_stmt->bind_result($validade);
    $med_stmt->fetch();
    $med_stmt->close();

    if (strtotime($validade) < strtotime(date('Y-m-d'))) {
        echo json_encode(["sucesso" => false, "erro" => "Não é possível dispensar medicamento vencido!"]);
        exit;
    }

    $sql = "INSERT INTO dispensacoes (medicamento_id, quantidade, data_disp, paciente, observacao) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Erro ao preparar statement: " . $conn->error);
    }
    $stmt->bind_param("iisss", $medicamento_id, $quantidade, $data_disp, $paciente, $observacao);

    if ($stmt->execute()) {
        // Buscar nome do medicamento
        $med_stmt = $conn->prepare("SELECT nome FROM medicamentos WHERE id = ?");
        $med_stmt->bind_param("i", $medicamento_id);
        $med_stmt->execute();
        $med_stmt->bind_result($nome_medicamento);
        $med_stmt->fetch();
        $med_stmt->close();

        echo json_encode([
            "sucesso" => true,
            "nova" => [
                "id" => $stmt->insert_id,
                "medicamento" => $nome_medicamento,
                "quantidade" => $quantidade,
                "paciente" => $paciente,
                "data_disp" => date('d/m/Y', strtotime($data_disp)),
                "observacao" => $observacao
            ]
        ]);
        exit;
    } else {
        echo json_encode(["sucesso" => false, "erro" => "Erro ao registrar dispensação."]);
        exit;
    }
}
