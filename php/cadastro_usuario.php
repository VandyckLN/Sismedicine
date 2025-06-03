<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}

include("conexao.php");

// Busca todos os usuários cadastrados
$sql = "SELECT id, usuario FROM usuarios ORDER BY usuario ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Usuário | Farmácia Hospitalar</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/cadastro_usuario.css">
</head>
<body>
<div class="top-bar">
    <h2>Cadastrar Novo Usuário</h2>
    <a href="dashboard.php">🏠 Dashboard</a>
</div>

<div class="container">
    <form action="processa_cadastro_usuario.php" method="POST">
        <label for="usuario">Usuário:</label>
        <input type="text" id="usuario" name="usuario" required>

        <label for="senha">Senha:</label>
        <input type="password" id="senha" name="senha" required>

        <button type="submit">Cadastrar</button>
    </form>
</div>

<div class="container" style="margin-top:30px;">
    <h3>Usuários cadastrados</h3>
    <table style="width:100%; border-collapse:collapse;">
        <tr>
            <th>ID</th>
            <th>Usuário</th>
            <th>Ações</th>
        </tr>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['usuario']); ?></td>
                    <td>
                        <a class="btn btn-editar" href="editar_usuario.php?id=<?php echo $row['id']; ?>">Editar</a>
                        <a class="btn btn-excluir" href="excluir_usuario.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir este usuário?');">Excluir</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="3">Nenhum usuário cadastrado.</td></tr>
        <?php endif; ?>
    </table>
</div>
</body>
</html>

<?php
$senha_hash = password_hash($senha, PASSWORD_DEFAULT);
?>
