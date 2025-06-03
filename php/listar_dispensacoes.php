<?php
// Iniciar a sessão
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit;
}

// Conectar ao banco de dados
require_once 'conexao.php';

// Filtro por data
if (isset($_GET['data_inicio']) && !empty($_GET['data_inicio']) && isset($_GET['data_fim']) && !empty($_GET['data_fim'])) {
    $data_inicio = $_GET['data_inicio'];
    $data_fim = $_GET['data_fim'];
    $where .= " AND d.data_disp BETWEEN '$data_inicio' AND '$data_fim'";
}

// Consulta medicamentos para filtro
$sql_med = "SELECT id, nome FROM medicamentos ORDER BY nome";
$medicamentos = $conn->query($sql_med);

// Consulta dispensações
$sql = "SELECT d.id, m.nome, d.quantidade, d.data_disp, d.observacao 
        FROM dispensacoes d
        JOIN medicamentos m ON d.medicamento_id = m.id
        WHERE $where
        ORDER BY d.data_disp DESC";
$dispensacoes = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listar Dispensações</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/listar_dispensacoes.css">
</head>
<body>
    <div class="container">
        <h1>Listar Dispensações</h1>
        <div id="mensagem" style="display: none;"></div>
        <a href="../dashboard.php">Voltar</a>
        
        <form id="form-filtro" method="GET">
            <label>Medicamento:</label>
            <select name="medicamento_id">
                <option value="">Todos</option>
                <?php while($med = $medicamentos->fetch_assoc()) { ?>
                <option value="<?= $med['id'] ?>" <?= (isset($medicamento_id) && $medicamento_id == $med['id']) ? 'selected' : '' ?>><?= $med['nome'] ?></option>
                <?php } ?>
            </select>
            
            <label>De:</label>
        <input type="date" name="data_inicio" value="<?= $_GET['data_inicio'] ?? '' ?>">

        <label>Até:</label>
        <input type="date" name="data_fim" value="<?= $_GET['data_fim'] ?? '' ?>">

        <button type="submit">Filtrar</button>
        <button type="button" onclick="exportarCSV()">Exportar CSV</button>
    </form>
</div>
        fetch('processa_dispensacao.php', {os";
            method: 'POST',y($sql_med);
            body: dados
        })a dispensações
        .then(response => response.json())
        .then(data => {
            if (data.sucesso) {
                form.reset();d.medicamento_id = m.id
                // Adiciona nova linha na tabela
                const tabela = document.querySelector('.listar-container tbody');
                const novaLinha = document.createElement('tr');
                novaLinha.innerHTML = `
                    <td>${data.nova.id}</td>
                    <td>${data.nova.medicamento}</td>
                    <td>${data.nova.quantidade}</td>
                    <td>${data.nova.paciente}</td>
                    <td>${data.nova.data_disp}</td>
                    <td>${data.nova.observacao}</td>
                `;Dispensações</title>
                tabela.prepend(novaLinha);yle.css">
no    <link rel="stylesheet" href="../css/listar_dispensacoes.css">
                // Exibe mensagem de sucesso em JS
                alert("Dispensação registrada com sucesso!");
            } else {
                msgBox.textContent = data.erro || "Erro ao registrar dispensação.";
                msgBox.className = "mensagem erro";hboard.php">Voltar</a>
                msgBox.style.display = "block";
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const urlParams = new URLSearchParams(window.location.search);
            const msgBox = document.getElementById("mensagem");
            
            if (urlParams.has("sucesso")) {
                msgBox.textContent = "Dispensação registrada com sucesso!";
                msgBox.className = "mensagem sucesso";
                msgBox.style.display = "block";
            } else if (urlParams.has("erro")) {
                msgBox.textContent = "Erro ao registrar dispensação.";
                msgBox.className = "mensagem erro";
                msgBox.style.display = "block";
            }
        });
        
        function exportarCSV() {
            window.location.href = "exportar_dispensacoes_csv.php<?= $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '' ?>";
        }
    </script>
</body>
</html>
    });['id'] ?>" <?= (isset($medicamento_id) && $medicamento_id == $med['id']) ? 'selected' : '' ?>><?= $med['nome'] ?></option>
});     <?php } ?>
            <input type="date" name="data_inicio" value="<?= $_GET['data_inicio'] ?? '' ?>">

            <label>Até:</label>
            <input type="date" name="data_fim" value="<?= $_GET['data_fim'] ?? '' ?>">

            <button type="submit">Filtrar</button>
            <button type="button" onclick="exportarCSV()">Exportar CSV</button>
        </form>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Medicamento</th>
                    <th>Quantidade</th>
                    <th>Data</th>
                    <th>Observação</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $dispensacoes->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['nome'] ?></td>
                    <td><?= $row['quantidade'] ?></td>
                    <td><?= date("d/m/Y", strtotime($row['data_disp'])) ?></td>
                    <td><?= $row['observacao'] ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <script>
        function exportarCSV() {
            window.location.href = "exportar_dispensacoes_csv.php<?= $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '' ?>";
        }
    </script>
</body>
</html>
