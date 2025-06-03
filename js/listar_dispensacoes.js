/**
 * Script para gerenciar funcionalidades da página de listagem de dispensações
 */

document.addEventListener("DOMContentLoaded", () => {
    // Verificar mensagens de feedback na URL
    const urlParams = new URLSearchParams(window.location.search);
    const msgBox = document.getElementById("mensagem");

    if (urlParams.has("sucesso")) {
        msgBox.textContent = "Dispensação registrada com sucesso!";
        msgBox.className = "mensagem sucesso";
        msgBox.style.display = "block";
    } else if (urlParams.has("erro")) {
        msgBox.textContent = urlParams.get("erro") || "Erro ao registrar dispensação.";
        msgBox.className = "mensagem erro";
        msgBox.style.display = "block";
    }

    // Configurar o formulário de dispensação se existir
    const form = document.getElementById("form-dispensacao");
    if (form) {
        form.addEventListener("submit", function (e) {
            e.preventDefault();
            const dados = new FormData(this);

            fetch('processa_dispensacao.php', {
                method: 'POST',
                body: dados
            })
                .then(response => response.json())
                .then(data => {
                    if (data.sucesso) {
                        form.reset();
                        // Adiciona nova linha na tabela
                        const tabela = document.querySelector('.table-container tbody');
                        const novaLinha = document.createElement('tr');
                        novaLinha.innerHTML = `
                        <td>${data.nova.id}</td>
                        <td>${data.nova.medicamento}</td>
                        <td>${data.nova.quantidade}</td>
                        <td>${data.nova.data_disp}</td>
                        <td>${data.nova.observacao}</td>
                    `;
                        tabela.prepend(novaLinha);

                        // Exibe mensagem de sucesso
                        msgBox.textContent = "Dispensação registrada com sucesso!";
                        msgBox.className = "mensagem sucesso";
                        msgBox.style.display = "block";
                    } else {
                        msgBox.textContent = data.erro || "Erro ao registrar dispensação.";
                        msgBox.className = "mensagem erro";
                        msgBox.style.display = "block";
                    }
                })
                .catch(error => {
                    console.error("Erro:", error);
                    msgBox.textContent = "Erro ao se comunicar com o servidor.";
                    msgBox.className = "mensagem erro";
                    msgBox.style.display = "block";
                });
        });
    }
});

/**
 * Exporta as dispensações para CSV com os filtros atuais
 */
function exportarCSV() {
    const queryString = window.location.search;
    window.location.href = "exportar_dispensacoes_csv.php" + queryString;
}