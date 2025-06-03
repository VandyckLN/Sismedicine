<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}

include("conexao.php");

if (!isset($_GET['id'])) {
    header("Location: cadastro_usuario.php");
    exit;
}

$id = intval($_GET['id']);
$sql = "SELECT usuario FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($usuario);
if (!$stmt->fetch()) {
    $stmt->close();
    $conn->close();
    header("Location: cadastro_usuario.php?erro=UsuarioNaoEncontrado");
    exit;
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuário | Farmácia Hospitalar</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/cadastro_usuario.css">
</head>
<body>
<div class="top-bar">
    <h2>Editar Usuário</h2>
    <a href="cadastro_usuario.php" class="btn btn-voltar">← Voltar</a>
</div>

<div class="container">
    <form action="processa_editar_usuario.php" method="POST">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <label for="usuario">Usuário:</label>
        <input type="text" id="usuario" name="usuario" value="<?php echo htmlspecialchars($usuario); ?>" required>

        <label for="senha">Nova Senha:</label>
        <input type="password" id="senha" name="senha" placeholder="Deixe em branco para não alterar">

        <button type="submit">Salvar Alterações</button>
    </form>
</div>
</body>
</html>