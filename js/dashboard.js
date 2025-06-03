/**
 * Script para funcionalidades do dashboard
 */

// Função para atualizar o relógio em tempo real
function atualizarRelogio() {
    const agora = new Date();
    const horas = agora.getHours().toString().padStart(2, '0');
    const minutos = agora.getMinutes().toString().padStart(2, '0');
    const segundos = agora.getSeconds().toString().padStart(2, '0');
    const relogio = document.getElementById('relogio');
    if (relogio) {
        relogio.textContent = `${horas}:${minutos}:${segundos}`;
    }
}

// Inicia o relógio e atualiza a cada segundo
setInterval(atualizarRelogio, 1000);
atualizarRelogio();

// Detecta inatividade para logout automático
let tempoInatividade = 0;
const tempoMaximoInatividade = 30 * 60; // 30 minutos em segundos

function resetarTempoInatividade() {
    tempoInatividade = 0;
}

function verificarInatividade() {
    tempoInatividade++;
    if (tempoInatividade >= tempoMaximoInatividade) {
        window.location.href = 'logout.php?razao=inatividade';
    }
}

// Eventos que resetam o contador de inatividade
document.addEventListener('mousemove', resetarTempoInatividade);
document.addEventListener('keypress', resetarTempoInatividade);
document.addEventListener('click', resetarTempoInatividade);
document.addEventListener('scroll', resetarTempoInatividade);

// Inicia o verificador de inatividade
setInterval(verificarInatividade, 1000);