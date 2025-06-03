// Função para pegar parâmetro da URL
function getParametroURL(nome) {
    const params = new URLSearchParams(window.location.search);
    return params.get(nome);
}

window.onload = function() {
    const sucesso = getParametroURL('sucesso');
    if (sucesso === '1') {
        const msg = document.getElementById('msg-sucesso');
        if (msg) {
            msg.style.display = 'block';
        }
        // Remove o parâmetro da URL para evitar repetição ao atualizar
        const url = new URL(window.location);
        url.searchParams.delete('sucesso');
        window.history.replaceState({}, document.title, url.toString());
    }
};

<script src="../js/cadastro_medicamento.js"></script>