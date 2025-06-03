<?php
// Incluir em todas as páginas que exigem autenticação
session_start();

// Verifica se usuário está logado
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

// Verifica tempo de inatividade (30 minutos)
$tempo_maximo_inatividade = 1800; // 30 minutos em segundos
if (isset($_SESSION['ultima_atividade']) && 
    (time() - $_SESSION['ultima_atividade'] > $tempo_maximo_inatividade)) {
    // Destruir sessão
    session_unset();
    session_destroy();
    header("Location: login.php?erro=sessao_expirada");
    exit;
}

// Atualiza timestamp de última atividade
$_SESSION['ultima_atividade'] = time();
?>