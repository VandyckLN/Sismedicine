<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit;
}

include("conexao.php");

// Filtro
$where = "WHERE 1=1";
if (!empty($_GET['medicamento_id'])) {
    $medicamento_id = intval($_GET['medicamento_id']);
    $where .= " AND d.medicamento_id = $medicamento_id";
}

if (!empty($_GET['data_inicio']) && !empty($_GET['data_fim'])) {
    $data_inicio = $_GET['data_inicio'];
    $data_fim = $_GET['data_fim'];
    $where .= " AND d.data_disp BETWEEN '$data_inicio' AND '$data_fim'";
}

// Consulta
$sql = "
SELECT d.id, m.nome AS medicamento, d.quantidade, d.data_disp, d.observacao
FROM dispensacoes d
INNER JOIN medicamentos m ON d.medicamento_id = m.id
$where
ORDER BY d.data_disp DESC
";

$result = $conn->query($sql);

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=dispensacoes.csv');

$output = fopen('php://output', 'w');
fputcsv($output, ['ID', 'Medicamento', 'Quantidade', 'Data', 'Observação']);

while ($row = $result->fetch_assoc()) {
    fputcsv($output, [$row['id'], $row['medicamento'], $row['quantidade'], $row['data_disp'], $row['observacao']]);
}

fclose($output);
exit;
