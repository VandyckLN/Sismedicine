<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit;
}

include("conexao.php");

// Defina o número de registros por página
$por_pagina = 10;

// Descubra a página atual
$pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;

// Calcule o offset
$offset = ($pagina - 1) * $por_pagina;

// Conte o total de registros
$sql_total = "SELECT COUNT(*) as total FROM dispensacoes";
$result_total = $conn->query($sql_total);
$total_registros = $result_total->fetch_assoc()['total'];
$total_paginas = ceil($total_registros / $por_pagina);

// Consulta paginada
$sql_disp = "SELECT d.id, m.nome AS medicamento, d.quantidade, d.paciente, d.data_disp, d.observacao
             FROM dispensacoes d
             JOIN medicamentos m ON d.medicamento_id = m.id
             ORDER BY d.data_disp DESC
             LIMIT $por_pagina OFFSET $offset";
$result_disp = $conn->query($sql_disp);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $medicamento_id = $_POST['medicamento_id'];
    $quantidade = $_POST['quantidade'];
    $paciente = $_POST['paciente'];
    $data_disp = $_POST['data_disp'];
    $observacao = $_POST['observacao'];

    $sql = "INSERT INTO dispensacoes (medicamento_id, quantidade, paciente, data_disp, observacao)
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("iisss", $medicamento_id, $quantidade, $paciente, $data_disp, $observacao);
        if ($stmt->execute()) {
            header("Location: listar_dispensacoes.php?msg=Dispensação registrada com sucesso");
            exit;
        } else {
            echo "Erro ao executar: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Erro na preparação: " . $conn->error;
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Registrar Dispensação</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/dispensacao.css">
</head>
<body>

<div class="top-bar">
    <h2>Registrar Dispensação</h2>
    <a href="dashboard.php">🏠 Dashboard</a>
</div>

<div class="form-container">
    <form id="form-dispensacao" method="POST" action="processa_dispensacao.php">
        <label for="medicamento_id">Medicamento:</label>
        <select id="medicamento_id" name="medicamento_id" required>
            <option value="">Selecione</option>
            <?php
            $sql = "SELECT id, nome FROM medicamentos ORDER BY nome";
            $result = $conn->query($sql);
            while ($row = $result->fetch_assoc()) {
                echo "<option value='".$row['id']."' data-validade='".$row['validade']."'>".$row['nome']."</option>";
            }
            ?>
        </select>

        <label for="quantidade">Quantidade:</label>
        <input type="number" name="quantidade" id="quantidade" required>

        <label for="paciente">Paciente:</label>
        <input type="text" name="paciente" id="paciente" class="campo-paciente" required>

        <label for="data_disp">Data de Dispensação:</label>
        <input type="date" name="data_disp" id="data_disp" value="<?php echo date('Y-m-d'); ?>" required>

        <label for="observacao">Observação:</label>
        <textarea name="observacao" id="observacao"></textarea>

        <button type="submit">Registrar</button>
    </form>
</div>

<div id="mensagem" class="mensagem" style="display:none"></div>

<div class="listar-container" style="margin-top:40px;">
    <h3>Dispensações já cadastradas</h3>
    <input type="text" id="busca-dispensacao" placeholder="Buscar por paciente, medicamento ou observação..." style="margin-bottom:16px;width:100%;padding:8px;">
    <table class="tabela-dispensacoes" id="tabela-dispensacoes">
        <thead>
            <tr>
                <th>ID</th>
                <th>Medicamento</th>
                <th>Quantidade</th>
                <th>Paciente</th>
                <th>Data</th>
                <th>Observação</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result_disp && $result_disp->num_rows > 0): ?>
                <?php while ($row = $result_disp->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['medicamento']) ?></td>
                        <td><?= $row['quantidade'] ?></td>
                        <td><?= htmlspecialchars($row['paciente']) ?></td>
                        <td><?= date('d/m/Y', strtotime($row['data_disp'])) ?></td>
                        <td><?= htmlspecialchars($row['observacao']) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6">Nenhuma dispensação cadastrada.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="paginacao" style="margin-top:20px; text-align:center;">
    <?php if ($pagina > 1): ?>
        <a href="?pagina=<?= $pagina - 1 ?>">&laquo; Anterior</a>
    <?php endif; ?>

    <strong><?= $pagina ?></strong>

    <?php if ($pagina < $total_paginas): ?>
        <a href="?pagina=<?= $pagina + 1 ?>">Próxima &raquo;</a>
    <?php endif; ?>
</div>

<script src="../js/dispensacao.js"></script>
</body>
</html>
