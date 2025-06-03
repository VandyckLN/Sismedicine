/**
 * Script para validação do formulário de reset de senha
 */
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('resetForm');
    const usuarioInput = document.getElementById('usuario');
    const senhaInput = document.getElementById('senha');

    // Validação ao enviar formulário
    form.addEventListener('submit', function (event) {
        let formIsValid = true;

        // Validar nome de usuário
        if (usuarioInput.value.trim() === '') {
            showError(usuarioInput, 'Por favor, informe o nome de usuário.');
            formIsValid = false;
        }

        // Validar senha
        if (senhaInput.value.length < 6) {
            showError(senhaInput, 'A senha deve ter pelo menos 6 caracteres.');
            formIsValid = false;
        }

        if (!formIsValid) {
            event.preventDefault();
        }
    });

    // Remover indicadores de erro quando o usuário corrige
    usuarioInput.addEventListener('input', function () {
        this.classList.remove('is-invalid');
    });

    senhaInput.addEventListener('input', function () {
        this.classList.remove('is-invalid');
    });

    // Função para mostrar erro
    function showError(input, message) {
        input.classList.add('is-invalid');
        const feedback = input.nextElementSibling;
        if (feedback && feedback.classList.contains('invalid-feedback')) {
            feedback.textContent = message;
        }
    }
});