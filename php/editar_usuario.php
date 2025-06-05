<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}

include("conexao.php");

// Validação de senha no lado do servidor
function validarSenhaForte($senha) {
    // Pular validação se a senha estiver vazia (nenhuma alteração)
    if (empty($senha)) {
        return true;
    }
    
    // Mínimo de 8 caracteres
    if (strlen($senha) < 8) {
        return false;
    }
    
    // Pelo menos uma letra maiúscula
    if (!preg_match('/[A-Z]/', $senha)) {
        return false;
    }
    
    // Pelo menos uma letra minúscula
    if (!preg_match('/[a-z]/', $senha)) {
        return false;
    }
    
    // Pelo menos um número
    if (!preg_match('/[0-9]/', $senha)) {
        return false;
    }
    
    // Pelo menos dois caracteres especiais
    if (preg_match_all('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $senha) < 2) {
        return false;
    }
    
    return true;
}

if (!isset($_GET['id'])) {
    header("Location: cadastro_usuario.php");
    exit;
}

// Verificar se há mensagem de erro ou sucesso para exibir
$mensagem = '';
$tipo_mensagem = '';

if (isset($_GET['erro'])) {
    $mensagem = $_GET['erro'] === 'senha_fraca' ? 
        'A senha não atende aos critérios de segurança. Tente novamente com uma senha mais forte.' :
        'Erro ao editar usuário: ' . $_GET['erro'];
    $tipo_mensagem = 'erro';
} elseif (isset($_GET['sucesso'])) {
    $mensagem = 'Usuário atualizado com sucesso!';
    $tipo_mensagem = 'sucesso';
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuário | SisMedicine</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/validacao_senha.css">
    <link rel="stylesheet" href="../css/cadastro_usuario.css">
</head>
<body>
<div class="top-bar">
    <h2>Editar Usuário</h2>
    <a href="cadastro_usuario.php" class="btn btn-voltar">← Voltar</a>
</div>

<?php if (!empty($mensagem)): ?>
<div class="mensagem-container">
    <div class="mensagem mensagem-<?php echo $tipo_mensagem; ?>">
        <?php echo $mensagem; ?>
    </div>
</div>
<?php endif; ?>

<div class="container">
    <h2 class="form-title">Cadastro de Usuário</h2>
    
    <?php if (!empty($mensagem)): ?>
    <div class="mensagem-container">
        <div class="mensagem <?php echo $tipo_mensagem; ?>">
            <?php echo $mensagem; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="dashboard-container">
        <!-- Formulário de cadastro -->
        <div class="form-container">
            <form action="processa_cadastro_usuario.php" method="POST" id="form-usuario">
                <div class="form-group">
                    <label for="usuario">Nome de Usuário</label>
                    <input type="text" id="usuario" name="usuario" placeholder="Digite o nome de usuário" required>
                </div>

                <div class="form-group">
                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" placeholder="Digite uma senha forte" required>
                    <!-- O medidor de senha será inserido aqui pelo JavaScript -->
                </div>

                <button type="submit">Cadastrar Usuário</button>
            </form>
        </div>
        
        <!-- Tabela de usuários -->
        <div class="table-container">
            <h3>Usuários Cadastrados</h3>
            
            <?php if ($result && $result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuário</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['usuario']); ?></td>
                        <td>
                            <a href="editar_usuario.php?id=<?php echo $row['id']; ?>" class="btn btn-editar">Editar</a>
                            <a href="excluir_usuario.php?id=<?php echo $row['id']; ?>" class="btn btn-excluir" onclick="return confirm('Tem certeza que deseja excluir este usuário?');">Excluir</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="no-users">Nenhum usuário cadastrado.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Importando scripts de validação -->
<script src="../js/validacao_senha.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const senhaInput = document.getElementById('senha');
    const formulario = document.getElementById('form-usuario');
    
    if (!senhaInput || !formulario) return;
    
    // Adicionar medidor de força da senha
    const medidorContainer = document.createElement('div');
    medidorContainer.className = 'medidor-senha-container';
    medidorContainer.innerHTML = `
        <div class="medidor-senha">
            <div class="medidor-barra"></div>
        </div>
        <div class="medidor-texto"></div>
    `;
    
    senhaInput.parentNode.insertBefore(medidorContainer, senhaInput.nextSibling);
    
    // Elementos do medidor
    const medidorBarra = medidorContainer.querySelector('.medidor-barra');
    const medidorTexto = medidorContainer.querySelector('.medidor-texto');
    
    // Adicionar evento de input para verificar a senha enquanto digita
    senhaInput.addEventListener('input', function() {
        const senha = this.value;
        
        if (senha.length === 0) {
            medidorContainer.style.display = 'none';
            return;
        }
        
        medidorContainer.style.display = 'block';
        
        // Calcular força da senha
        const forca = calcularForcaSenha(senha);
        const classe = getClasseForcaSenha(forca);
        
        // Atualizar medidor
        medidorBarra.style.width = forca + '%';
        medidorBarra.className = 'medidor-barra ' + classe;
        
        // Texto do medidor
        if (forca < 30) {
            medidorTexto.textContent = 'Senha muito fraca';
        } else if (forca < 50) {
            medidorTexto.textContent = 'Senha fraca';
        } else if (forca < 75) {
            medidorTexto.textContent = 'Senha média';
        } else if (forca < 90) {
            medidorTexto.textContent = 'Senha forte';
        } else {
            medidorTexto.textContent = 'Senha muito forte';
        }
        
        // Verificar requisitos
        const resultado = validarSenhaForte(senha);
        medidorTexto.title = resultado.mensagem;
    });
    
    // Validar senha no envio do formulário
    formulario.addEventListener('submit', function(e) {
        const senha = senhaInput.value;
        
        // Apenas validar se o campo não estiver vazio
        if (senha.length > 0) {
            const resultado = validarSenhaForte(senha);
            
            if (!resultado.valido) {
                e.preventDefault(); // Impedir envio do formulário
                alert(resultado.mensagem);
            }
        }
    });
});
</script>
</body>
</html>