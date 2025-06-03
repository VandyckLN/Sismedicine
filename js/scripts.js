// Exemplo de script: mensagem no console
document.addEventListener("DOMContentLoaded", () => {
    console.log("Página carregada!");
});
// Relógio em tempo real
function atualizarRelogio() {
    const agora = new Date();
    const horas = agora.getHours().toString().padStart(2, '0');
    const minutos = agora.getMinutes().toString().padStart(2, '0');
    const segundos = agora.getSeconds().toString().padStart(2, '0');
    document.getElementById('relogio').textContent = `${horas}:${minutos}:${segundos}`;
}
setInterval(atualizarRelogio, 1000);
window.onload = atualizarRelogio;

// Animação contagem dos cards
function animarNumero(id, valorFinal) {
    let valorAtual = 0;
    const incremento = Math.ceil(valorFinal / 50);
    const elemento = document.getElementById(id);

    const intervalo = setInterval(() => {
        valorAtual += incremento;
        if (valorAtual >= valorFinal) {
            elemento.textContent = valorFinal;
            clearInterval(intervalo);
        } else {
            elemento.textContent = valorAtual;
        }
    }, 30);
}

document.addEventListener("DOMContentLoaded", function () {
    // Anima os números
    animarNumero("medicamentos", parseInt(document.getElementById("medicamentos").textContent));
    animarNumero("dispensacoes", parseInt(document.getElementById("dispensacoes").textContent));

    // Gráfico Chart.js
    const ctx = document.getElementById('graficoResumo').getContext('2d');
    const graficoResumo = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Medicamentos', 'Dispensações'],
            datasets: [{
                label: 'Resumo',
                data: [
                    parseInt(document.getElementById("medicamentos").textContent),
                    parseInt(document.getElementById("dispensacoes").textContent)
                ],
                backgroundColor: [
                    '#0077b6',
                    '#90e0ef'
                ],
                borderColor: [
                    '#023e8a',
                    '#0077b6'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                title: {
                    display: true,
                    text: 'Medicamentos vs. Dispensações'
                }
            }
        }
    });
});
