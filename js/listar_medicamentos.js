function filtrarMedicamentos() {
    var input = document.getElementById("busca");
    var filtro = input.value.toUpperCase();
    var tabela = document.getElementById("tabelaMedicamentos");
    var tr = tabela.getElementsByTagName("tr");

    for (var i = 1; i < tr.length; i++) {
        var tdNome = tr[i].getElementsByTagName("td")[1];
        if (tdNome) {
            var txtValor = tdNome.textContent || tdNome.innerText;
            if (txtValor.toUpperCase().indexOf(filtro) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
}

// Função para pegar parâmetros da URL
function getParametroURL(param) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
}

// Mostrar alertas de feedback
window.onload = function() {
    const sucesso = getParametroURL('sucesso');
    const erro = getParametroURL('erro');

    if (sucesso == 1) {
        alert("✅ Medicamento excluído com sucesso!");
    } else if (erro == 1) {
        alert("❌ Ocorreu um erro ao excluir o medicamento.");
    }
}
function confirmarExclusao(id) {
    if (confirm("Tem certeza que deseja excluir este medicamento?")) {
        window.location.href = "excluir_medicamento.php?id=" + id;
    }
}