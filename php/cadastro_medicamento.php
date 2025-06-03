<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Medicamento | Farmácia Hospitalar</title>
    <link rel="stylesheet" href="../css/cadastro_medicamentos.css">
    <link rel="stylesheet" href="../css/style.css">
    </head>
<body>
    <div class="top-bar">
        <h2>Cadastro de Medicamento</h2>
        <a href="dashboard.php">🏠 Dashboard</a>
    </div>

    <div class="container">
        <?php if (isset($_GET['sucesso']) && $_GET['sucesso'] == 1): ?>
            <div id="msg-sucesso">Medicamento cadastrado com sucesso!</div>
        <?php endif; ?>

        <form action="processa_cadastro_medicamento.php" method="POST">
            <label for="nome">Nome do Medicamento:</label>
            <input type="text" id="nome" name="nome" required>

            <label for="descricao">Descrição:</label>
            <textarea id="descricao" name="descricao" rows="3"></textarea>

            <label for="quantidade">Quantidade:</label>
            <input type="number" id="quantidade" name="quantidade" min="0" required>

            <label for="validade">Data de Validade:</label>
            <input type="date" id="validade" name="validade" required>

            <button type="submit">Cadastrar</button>
        </form>
    </div>
    <script src="../js/cadastro_medicamento.js"></script>
</body>
</html>

<?php
// Atualiza o estoque do medicamento
$sql_update = "UPDATE medicamentos SET quantidade = quantidade - ? WHERE id = ? AND quantidade >= ?";
$stmt_update = $conn->prepare($sql_update);
$stmt_update->bind_param("iii", $quantidade, $medicamento_id, $quantidade);
$stmt_update->execute();
?>
