/**
 * Script para a página de verificação em dois fatores
 */
document.addEventListener('DOMContentLoaded', function () {
    // Formatar automaticamente o campo de código enquanto digita
    const codigoInput = document.getElementById('codigo');
    if (codigoInput) {
        codigoInput.addEventListener('input', function (e) {
            // Remove tudo que não for número
            this.value = this.value.replace(/[^0-9]/g, '');

            // Limita a 6 dígitos
            if (this.value.length > 6) {
                this.value = this.value.slice(0, 6);
            }
        });

        // Seleciona todo o texto ao focar no campo
        codigoInput.addEventListener('focus', function () {
            this.select();
        });
    }

    // Adiciona um timer para mostrar há quanto tempo o código foi gerado
    const codigoDisplay = document.querySelector('.codigo-display');
    if (codigoDisplay) {
        const timerElement = document.createElement('div');
        timerElement.className = 'codigo-timer';
        timerElement.textContent = 'Código gerado há 0 segundos';
        codigoDisplay.insertAdjacentElement('afterend', timerElement);

        let segundos = 0;
        setInterval(() => {
            segundos++;

            let texto = '';
            if (segundos < 60) {
                texto = `Código gerado há ${segundos} segundo${segundos === 1 ? '' : 's'}`;
            } else {
                const minutos = Math.floor(segundos / 60);
                const segundosRestantes = segundos % 60;
                texto = `Código gerado há ${minutos} minuto${minutos === 1 ? '' : 's'} e ${segundosRestantes} segundo${segundosRestantes === 1 ? '' : 's'}`;
            }

            timerElement.textContent = texto;
        }, 1000);
    }
});