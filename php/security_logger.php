<?php
function security_log($event_type, $details, $user = 'unknown') {
    $log_file = "../logs/security.log";
    $timestamp = date("[Y-m-d H:i:s]");
    $ip = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    
    $log_entry = "$timestamp | $ip | $user | $event_type | $details | $user_agent\n";
    file_put_contents($log_file, $log_entry, FILE_APPEND);
}

// Exemplo de uso:
// security_log('LOGIN_SUCCESS', 'Login bem-sucedido', $usuario);
// security_log('LOGIN_FAILED', 'Senha incorreta', $usuario);
// security_log('ACCESS_DENIED', 'Tentativa de acesso não autorizado', 'anônimo');
?>