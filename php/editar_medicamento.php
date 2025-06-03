<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit;
}

include("conexao.php");

// Verifica se veio o ID pela URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: listar_medicamentos.php");
    exit;
}

$id = intval($_GET['id']);

// Consulta o medicamento pelo ID
$sql = "SELECT * FROM medicamentos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    $conn->close();
    header("Location: listar_medicamentos.php");
    exit;
}

$medicamento = $result->fetch_assoc();
$stmt->close();
// Don't close the connection here as we need it later
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Medicamento</title>
    <link rel="stylesheet" href="../css/formularios.css">
</head>
<body>

<div class="container">
    <h2>Editar Medicamento</h2>

    <form action="processa_edicao_medicamento.php" method="POST">
        <input type="hidden" name="id" value="<?php echo $medicamento['id']; ?>">

        <div class="form-group">
            <label for="nome">Nome:</label>
            <input type="text" name="nome" id="nome" value="<?php echo htmlspecialchars($medicamento['nome'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label for="descricao">Descrição:</label>
            <textarea name="descricao" id="descricao" rows="3"><?php echo htmlspecialchars($medicamento['descricao'] ?? ''); ?></textarea>
        </div>

        <div class="form-group">
            <label for="fabricante">Fabricante:</label>
            <input type="text" name="fabricante" id="fabricante" value="<?php echo htmlspecialchars($medicamento['fabricante'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="quantidade">Quantidade em estoque:</label>
            <input type="number" name="quantidade" id="quantidade" value="<?php echo $medicamento['quantidade'] ?? 0; ?>" required>
        </div>

        <div class="form-group">
            <label for="validade">Validade:</label>
            <input type="date" name="validade" id="validade" value="<?php echo $medicamento['validade'] ?? date('Y-m-d'); ?>" required>
        </div>

        <button type="submit">Salvar Alterações</button>
    </form>

    <a href="listar_medicamentos.php" class="btn-voltar">← Voltar para lista</a>
</div>

</body>
</html>
<?php
// Close the connection at the end of the file
$conn->close();
?>
