<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit;
}

include("conexao.php");

// Filtros
$data_fim = isset($_GET['data_fim']) ? $_GET['data_fim'] : date('Y-m-d');
$paciente = isset($_GET['paciente']) ? trim($_GET['paciente']) : '';

// Data inicial para a consulta
$data_inicio = isset($_GET['data_inicio']) ? $_GET['data_inicio'] : '2000-01-01';

// Query principal
$sql = "SELECT d.id, m.nome AS medicamento, d.quantidade, d.paciente, d.data_disp, d.observacao
        FROM dispensacoes d
        JOIN medicamentos m ON d.medicamento_id = m.id
        WHERE d.data_disp BETWEEN ? AND ?";

$params = [$data_inicio, $data_fim];
$types = "ss";

// Filtro opcional por paciente
if (!empty($paciente)) {
    $sql .= " AND d.paciente LIKE ?";
    $params[] = "%$paciente%";
    $types .= "s";
}

$sql .= " ORDER BY d.data_disp DESC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Erro na preparação: " . $conn->error);
}

$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Gráfico
$sqlGrafico = "SELECT 
                m.nome AS medicamento, 
                SUM(d.quantidade) AS total_unidades,
                COUNT(d.id) AS total_dispensacoes
               FROM dispensacoes d
               JOIN medicamentos m ON d.medicamento_id = m.id
               WHERE d.data_disp BETWEEN ? AND ?
               GROUP BY d.medicamento_id
               ORDER BY total_unidades DESC 
               LIMIT 10";

$stmtGrafico = $conn->prepare($sqlGrafico);
$stmtGrafico->bind_param("ss", $data_inicio, $data_fim);
$stmtGrafico->execute();
$resultadoGrafico = $stmtGrafico->get_result();

// Arrays para armazenar os dados do gráfico
$medicamentos = [];
$totaisUnidades = [];
$totaisDispensacoes = [];
$categorias = [];
$cores = [
    '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', 
    '#FF9F40', '#DADADA', '#75D701', '#26C6DA', '#D81B60'
];

$i = 0;
while ($row = $resultadoGrafico->fetch_assoc()) {
    $medicamentos[] = $row['medicamento'];
    $totaisUnidades[] = $row['total_unidades'];
    $totaisDispensacoes[] = $row['total_dispensacoes'];
    $categorias[] = $row['categoria'] ?? 'Não categorizado';
    $i++;
}

// Consulta para dispensações por mês (para gráfico de linha)
$sqlMensal = "SELECT 
                DATE_FORMAT(d.data_disp, '%Y-%m') AS mes,
                DATE_FORMAT(d.data_disp, '%b/%Y') AS mes_nome,
                COUNT(d.id) AS total
              FROM dispensacoes d
              WHERE d.data_disp BETWEEN ? AND ?
              GROUP BY DATE_FORMAT(d.data_disp, '%Y-%m')
              ORDER BY mes";

$stmtMensal = $conn->prepare($sqlMensal);
$stmtMensal->bind_param("ss", $data_inicio, $data_fim);
$stmtMensal->execute();
$resultadoMensal = $stmtMensal->get_result();

$meses = [];
$dispensacoesPorMes = [];

while ($row = $resultadoMensal->fetch_assoc()) {
    $meses[] = $row['mes_nome'];
    $dispensacoesPorMes[] = $row['total'];
}

$stmtMensal->close();
$stmt->close();
$stmtGrafico->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatórios | Farmácia</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/dashboard.css">
    <style>
        body {
            padding: 20px;
            font-family: Arial, sans-serif;
        }
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        h2 {
            margin: 0;
        }
        .filtro-form {
            margin: 20px 0;
        }
        .filtro-form input, .filtro-form button {
            padding: 6px;
            margin-right: 8px;
        }
        .btn-actions {
            margin-bottom: 20px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 30px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background: #007BFF;
            color: white;
        }
        /* Adicionando novos estilos para os gráficos */
        .graficos-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .grafico-card {
            flex: 1 1 400px;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            background-color: white;
        }
        
        .resumo-container {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        #resumo-dados {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 10px;
        }
        
        .dado-resumo {
            background-color: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            flex: 1 1 200px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .dado-valor {
            font-size: 24px;
            font-weight: bold;
            color: #007BFF;
            margin: 10px 0;
        }
        
        .dado-label {
            color: #6c757d;
            font-size: 14px;
            text-align: center;
        }
        
        @media print {
            .graficos-container, .resumo-container {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

<div class="top-bar">
    <h2>Relatório de Dispensações</h2>
    <a href="dashboard.php">🏠 Dashboard</a>
</div>

<form class="filtro-form" method="GET">
    <label>De:</label>
    <input type="date" name="data_inicio" value="<?php echo $data_inicio; ?>" required>
    <label>Até:</label>
    <input type="date" name="data_fim" value="<?php echo $data_fim; ?>" required>
    <label>Paciente:</label>
    <input type="text" name="paciente" value="<?php echo htmlspecialchars($paciente); ?>" placeholder="Nome do paciente">
    <button type="submit">Filtrar</button>
</form>

<div class="btn-actions">
    <button onclick="window.print()">🖨️ Imprimir</button>
    <button onclick="exportarCSV()">📥 Exportar CSV</button>
</div>

<?php if ($result->num_rows > 0): ?>
<table id="tabela-relatorio">
    <thead>
        <tr>
            <th>ID</th>
            <th>Medicamento</th>
            <th>Qtd</th>
            <th>Paciente</th>
            <th>Data</th>
            <th>Observação</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['medicamento']); ?></td>
            <td><?php echo $row['quantidade']; ?></td>
            <td><?php echo htmlspecialchars($row['paciente']); ?></td>
            <td><?php echo date('d/m/Y', strtotime($row['data_disp'])); ?></td>
            <td><?php echo htmlspecialchars($row['observacao']); ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
<?php else: ?>
<p>Nenhum registro encontrado.</p>
<?php endif; ?>

<div class="graficos-container">
    <div class="grafico-card">
        <h3>Medicamentos mais dispensados</h3>
        <canvas id="graficoDispensacoes" width="600" height="300"></canvas>
    </div>
    
    <div class="grafico-card">
        <h3>Dispensações por mês</h3>
        <canvas id="graficoMensal" width="600" height="300"></canvas>
    </div>
    
    <div class="grafico-card">
        <h3>Distribuição por categoria</h3>
        <canvas id="graficoPizza" width="400" height="400"></canvas>
    </div>
</div>

<div class="resumo-container">
    <h3>Resumo do período</h3>
    <div id="resumo-dados"></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function exportarCSV() {
    let csv = [];
    let rows = document.querySelectorAll("#tabela-relatorio tr");
    for (let i = 0; i < rows.length; i++) {
        let cols = rows[i].querySelectorAll("td, th");
        let row = [];
        for (let j = 0; j < cols.length; j++) {
            let text = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, " ").replace(/"/g, '""');
            row.push('"' + text + '"');
        }
        csv.push(row.join(","));
    }
    let blob = new Blob([csv.join("\n")], { type: "text/csv" });
    let link = document.createElement("a");
    link.download = "relatorio_dispensacoes.csv";
    link.href = URL.createObjectURL(blob);
    link.click();
}

const ctx = document.getElementById('graficoDispensacoes').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($medicamentos); ?>,
        datasets: [{
            label: 'Dispensações',
            data: <?php echo json_encode($totaisUnidades); ?>, // Alterado de $totais para $totaisUnidades
            backgroundColor: 'rgba(75, 192, 192, 0.7)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true,
                precision: 0
            }
        },
        plugins: {
            legend: { display: false }
        }
    }
});
</script>

</body>
</html>
