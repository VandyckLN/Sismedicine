<?php
// Ativar exibição de erros (remova em produção)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Arquivo de log para debugging
$log_file = "../logs/login_debug.log";

// Criar diretório de logs se não existir
if (!is_dir("../logs")) {
    mkdir("../logs", 0755, true);
}

// Função para registrar no log
function log_message($message) {
    global $log_file;
    $timestamp = date("[Y-m-d H:i:s]");
    file_put_contents($log_file, "$timestamp $message\n", FILE_APPEND);
}

// Verifica se foram enviados os dados
if (!isset($_POST['usuario']) || !isset($_POST['senha'])) {
    header("Location: login.php");
    exit;
}

// Sanitização e validação
$usuario = filter_input(INPUT_POST, 'usuario', FILTER_SANITIZE_STRING);
if (empty($usuario) || strlen($usuario) > 50) {
    header("Location: login.php?erro=usuario");
    exit;
}

$senha = $_POST['senha'];

log_message("Tentativa de login: usuário=$usuario");

// Conexão com o banco de dados
$conn = new mysqli("localhost", "root", "", "farmacia_hospitalar");

// Verifica a conexão
if ($conn->connect_error) {
    log_message("Erro de conexão: " . $conn->connect_error);
    die("Falha na conexão: " . $conn->connect_error);
}

// Verificação de CSRF (deve ser feito antes de qualquer processamento)
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    // Log da tentativa
    log_message("Tentativa de CSRF detectada");
    // Redirecionar para login com erro
    header("Location: login.php?erro=seguranca");
    exit;
}

// Após session_start()
// Inicialização das tentativas
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['last_attempt_time'] = time();
} else {
    // Verifica se o tempo de bloqueio já passou
    if (isset($_SESSION['bloqueado_ate']) && time() < $_SESSION['bloqueado_ate']) {
        // Ainda está bloqueado
        $tempo_restante = $_SESSION['bloqueado_ate'] - time();
        $minutos = ceil($tempo_restante / 60);
        $segundos = $tempo_restante % 60;
        log_message("Usuário ainda bloqueado por $minutos min e $segundos seg");
        header("Location: login.php?erro=bloqueado&tempo=$tempo_restante");
        exit;
    } else if (isset($_SESSION['bloqueado_ate']) && time() >= $_SESSION['bloqueado_ate']) {
        // Bloqueio expirou, reinicia contagem
        $_SESSION['login_attempts'] = 0;
        unset($_SESSION['bloqueado_ate']);
        log_message("Bloqueio expirado, tentativas reiniciadas para $usuario");
    }
}

// Consulta SQL (usando prepared statements para segurança)
$sql = "SELECT id, usuario, senha, nivel_acesso FROM usuarios WHERE usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Usuário encontrado
    $row = $result->fetch_assoc();
    log_message("Usuário encontrado: ID=" . $row['id'] . ", Hash=" . substr($row['senha'], 0, 10) . "...");
    
    // Verifica a senha
    if (password_verify($senha, $row['senha'])) {
        // Senha correta - Reseta contador de tentativas
        $_SESSION['login_attempts'] = 0;
        log_message("Senha correta para o usuário: $usuario");
        
        // Cria sessão
        $_SESSION['id'] = $row['id'];
        $_SESSION['usuario'] = $row['usuario'];
        $_SESSION['nivel_acesso'] = $row['nivel_acesso'];
        $_SESSION['ultima_atividade'] = time();
        
        log_message("Sessão iniciada. Redirecionando para dashboard.");
        
        // Todos os usuários são redirecionados diretamente para o dashboard
        header("Location: dashboard.php");
        exit;
    } else {
        // Senha incorreta - Incrementa contador
        $_SESSION['login_attempts']++;
        $_SESSION['last_attempt_time'] = time();
        
        // Definimos o tempo de bloqueio baseado no número de tentativas
        $tentativas = $_SESSION['login_attempts'];
        log_message("Senha incorreta para o usuário: $usuario - Tentativa $tentativas de 3");
        
        if ($tentativas >= 3) {
            // Bloqueia por 5 minutos (300 segundos)
            $tempo_bloqueio = 300;
            $_SESSION['bloqueado_ate'] = time() + $tempo_bloqueio;
            log_message("Conta bloqueada por 5 minutos após 3 tentativas falhas: $usuario");
            header("Location: login.php?erro=bloqueado&tempo=$tempo_bloqueio");
            exit;
        } else {
            // Mensagem de erro baseada no número de tentativas
            header("Location: login.php?erro=senha&tentativas=$tentativas");
            exit;
        }
    }
} else {
    // Usuário não encontrado - Também incrementa contador
    $_SESSION['login_attempts']++;
    $_SESSION['last_attempt_time'] = time();
    
    $tentativas = $_SESSION['login_attempts'];
    log_message("Usuário não encontrado: $usuario - Tentativa $tentativas de 3");
    
    if ($tentativas >= 3) {
        // Bloqueia por 5 minutos (300 segundos)
        $tempo_bloqueio = 300;
        $_SESSION['bloqueado_ate'] = time() + $tempo_bloqueio;
        log_message("Conta bloqueada por 5 minutos após 3 tentativas falhas");
        header("Location: login.php?erro=bloqueado&tempo=$tempo_bloqueio");
        exit;
    } else {
        header("Location: login.php?erro=usuario&tentativas=$tentativas");
        exit;
    }
}

$stmt->close();
$conn->close();
?>
