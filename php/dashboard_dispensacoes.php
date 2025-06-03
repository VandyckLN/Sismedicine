<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit;
}

include("conexao.php");

// Consulta quantidade por medicamento
$sql = "
SELECT m.nome, SUM(d.quantidade) as total_disp
FROM dispensacoes d
INNER JOIN medicamentos m ON d.medicamento_id = m.id
GROUP BY m.nome
ORDER BY total_disp DESC
";

$result = $conn->query($sql);

$nomes = [];
$quantidades = [];

while ($row = $result->fetch_assoc()) {
    $nomes[] = $row['nome'];
    $quantidades[] = $row['total_disp'];
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Dispensações</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="top-bar">
        <h2>Dashboard de Dispensações</h2>
        <a href="dashboard.php">Voltar</a>
    </div>

    <div class="chart-container" style="width:80%; margin:30px auto;">
        <canvas id="graficoDispensacoes"></canvas>
        <button onclick="exportarPDF()" style="margin-top:20px;">Exportar PDF</button>
    </div>

    <script>
        // Passando os dados do PHP para variáveis JavaScript globais
        const medicamentosNomes = <?= json_encode($nomes) ?>;
        const medicamentosQuantidades = <?= json_encode($quantidades) ?>;
    </script>
    <script src="../js/dashboard_dispensacoes.js"></script>
</body>
</html>
