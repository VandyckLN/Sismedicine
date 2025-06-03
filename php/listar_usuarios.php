<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit;
}

// Verificar se o usuário é administrador
if (!isset($_SESSION['nivel']) || $_SESSION['nivel'] != 'admin') {
    header("Location: dashboard.php");
    exit;
}

include("conexao.php");

// Consulta todos os usuários
$sql = "SELECT id, nome, usuario, nivel FROM usuarios ORDER BY nome";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Usuários | SisMedicine</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/listar_usuarios.css">
</head>
<body>

<div class="top-bar">
    <h2>Usuários Cadastrados</h2>
    <a class="btn-voltar" href="dashboard.php">&larr; Voltar ao Dashboard</a>
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Usuário</th>
            <th>Nível</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row["id"]; ?></td>
                    <td><?php echo htmlspecialchars($row["nome"]); ?></td>
                    <td><?php echo htmlspecialchars($row["usuario"]); ?></td>
                    <td><?php echo $row["nivel"]; ?></td>
                    <td>
                        <a class="btn-excluir" href="excluir_usuario.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir este usuário?');">Excluir</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="5" class="no-users">Nenhum usuário cadastrado.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
<?php
$conn->close();
?>
