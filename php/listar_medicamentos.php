<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit;
}

include("conexao.php");

// Garante que a conexão use UTF-8
$conn->set_charset("utf8");

$sql = "SELECT * FROM medicamentos ORDER BY validade ASC";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Listar Medicamentos | Farmácia Hospitalar</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/listar_medicamentos.css">
</head>
<body>

<div class="top-bar">
    <h2>Lista de Medicamentos</h2>
    <a href="dashboard.php">🏠 Dashboard</a>
</div>
<?php if (isset($_GET['editado']) && $_GET['editado'] == 1): ?>
    <div class="alert">✅ Medicamento atualizado com sucesso!</div>
<?php endif; ?>
<?php if (isset($_GET['erro']) && $_GET['erro'] == 'nao_excluir_dispensado'): ?>
<script>
    alert("Não é possível excluir: este medicamento já foi utilizado em uma dispensação.");
</script>
<?php endif; ?>
<?php if (isset($_GET['msg']) && $_GET['msg'] == 'excluido'): ?>
<script>
    alert("Medicamento excluído com sucesso!");
</script>
<?php endif; ?>
<input type="text" id="busca" onkeyup="filtrarMedicamentos()" placeholder="Buscar medicamento pelo nome...">

<table id="tabelaMedicamentos">
    <tr>
        <th>ID</th>
        <th>Nome</th>
        <th>Descrição</th>
        <th>Validade</th>
        <th>Ações</th>
    </tr>

    <?php
    if ($result->num_rows > 0) {
        while ($med = $result->fetch_assoc()) {
            $validade = new DateTime($med['validade']);
            $hoje = new DateTime();
            $intervalo = $hoje->diff($validade);
            $diasRestantes = $intervalo->days;
            $classe = "";

            if ($validade < $hoje) {
                $classe = "expirado";
            } elseif ($diasRestantes <= 30) {
                $classe = "vencendo";
            }

            echo "<tr class='$classe'>";
            echo "<td>{$med['id']}</td>";
            echo "<td>{$med['nome']}</td>";
            echo "<td>{$med['descricao']}</td>";
            echo "<td>".date('d/m/Y', strtotime($med['validade']))."</td>";
            echo "<td>
                    <a class='btn btn-editar' href='editar_medicamento.php?id={$med['id']}'>Editar</a>
                    <a class='btn btn-excluir' href='excluir_medicamento.php?id={$med['id']}' onclick='return confirm(\"Tem certeza?\")'>Excluir</a>
                  </td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='5'>Nenhum medicamento cadastrado.</td></tr>";
    }
    ?>
</table>

<script src="../js/listar_medicamentos.js"></script>
</body>
</html>
