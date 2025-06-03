document.addEventListener("DOMContentLoaded", () => {
    const urlParams = new URLSearchParams(window.location.search);
    const msgBox = document.getElementById("mensagem");

    if (urlParams.has("sucesso")) {
        msgBox.textContent = "Dispensação registrada com sucesso!";
        msgBox.className = "mensagem sucesso";
        msgBox.style.display = "block";
    } else if (urlParams.has("erro")) {
        msgBox.textContent = "Erro ao registrar dispensação.";
        msgBox.className = "mensagem erro";
        msgBox.style.display = "block";
    }

    // AJAX para envio do formulário
    const form = document.getElementById('form-dispensacao');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const select = document.getElementById('medicamento_id');
        const selectedOption = select.options[select.selectedIndex];
        const validade = selectedOption.getAttribute('data-validade');
        if (validade && new Date(validade) < new Date()) {
            alert("Não é possível dispensar medicamento vencido!");
            return;
        }

        const dados = new FormData(form);

        fetch('processa_dispensacao.php', {
            method: 'POST',
            body: dados
        })
        .then(response => response.json())
        .then(data => {
            if (data.sucesso) {
                form.reset();
                // Adiciona nova linha na tabela
                const tabela = document.querySelector('.listar-container tbody');
                const novaLinha = document.createElement('tr');
                novaLinha.innerHTML = `
                    <td>${data.nova.id}</td>
                    <td>${data.nova.medicamento}</td>
                    <td>${data.nova.quantidade}</td>
                    <td>${data.nova.paciente}</td>
                    <td>${data.nova.data_disp}</td>
                    <td>${data.nova.observacao}</td>
                `;
                tabela.prepend(novaLinha);

                // Exibe toast de sucesso
                const toast = document.createElement('div');
                toast.textContent = "Dispensação registrada com sucesso!";
                toast.style.position = "fixed";
                toast.style.top = "30px";
                toast.style.left = "50%";
                toast.style.transform = "translateX(-50%)";
                toast.style.background = "#28a745";
                toast.style.color = "#fff";
                toast.style.padding = "14px 28px";
                toast.style.borderRadius = "8px";
                toast.style.zIndex = "9999";
                toast.style.fontWeight = "bold";
                toast.style.boxShadow = "0 2px 8px rgba(0,0,0,0.15)";
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 2500);
            } else {
                msgBox.textContent = data.erro || "Erro ao registrar dispensação.";
                msgBox.className = "mensagem erro";
                msgBox.style.display = "block";
            }
        })
        .catch(() => {
            msgBox.textContent = "Erro ao registrar dispensação.";
            msgBox.className = "mensagem erro";
            msgBox.style.display = "block";
        });
    });

    // Busca interativa na tabela de dispensações
    const inputBusca = document.getElementById('busca-dispensacao');
    const tabela = document.getElementById('tabela-dispensacoes');
    if (inputBusca && tabela) {
        inputBusca.addEventListener('keyup', function() {
            const termo = this.value.toLowerCase();
            const linhas = tabela.querySelectorAll('tbody tr');
            linhas.forEach(linha => {
                const texto = linha.textContent.toLowerCase();
                linha.style.display = texto.includes(termo) ? '' : 'none';
            });
        });
    }
});

function exportarCSV() {
    const tabela = document.getElementById('tabela-dispensacoes');
    let csv = '';
    const linhas = tabela.querySelectorAll('tr');
    linhas.forEach(linha => {
        let cols = linha.querySelectorAll('th,td');
        let linhaCSV = [];
        cols.forEach(col => linhaCSV.push('"' + col.innerText.replace(/"/g, '""') + '"'));
        csv += linhaCSV.join(';') + '\n';
    });
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'dispensacoes.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}
