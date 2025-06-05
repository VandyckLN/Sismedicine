/**
 * Script para sugestão de senha forte
 */

// Função para gerar senha forte que atende aos requisitos de segurança
function gerarSenhaForte() {
    // Definindo conjuntos de caracteres
    const maiusculas = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    const minusculas = "abcdefghijklmnopqrstuvwxyz";
    const numeros = "0123456789";
    const especiais = "!@#$%^&*()_+-=[]{}|;:,.<>?";
    
    // Garantir pelo menos um de cada tipo de caractere
    let senha = "";
    senha += maiusculas.charAt(Math.floor(Math.random() * maiusculas.length));
    senha += maiusculas.charAt(Math.floor(Math.random() * maiusculas.length));
    senha += minusculas.charAt(Math.floor(Math.random() * minusculas.length));
    senha += minusculas.charAt(Math.floor(Math.random() * minusculas.length));
    senha += numeros.charAt(Math.floor(Math.random() * numeros.length));
    senha += numeros.charAt(Math.floor(Math.random() * numeros.length));
    senha += especiais.charAt(Math.floor(Math.random() * especiais.length));
    senha += especiais.charAt(Math.floor(Math.random() * especiais.length));
    
    // Completar o resto da senha até 14 caracteres
    const todosCaracteres = maiusculas + minusculas + numeros + especiais;
    for (let i = senha.length; i < 14; i++) {
        senha += todosCaracteres.charAt(Math.floor(Math.random() * todosCaracteres.length));
    }
    
    // Embaralhar a senha (Fisher-Yates shuffle)
    return senha.split('').sort(() => 0.5 - Math.random()).join('');
}

// Inicializar quando o documento estiver pronto
document.addEventListener('DOMContentLoaded', function() {
    const tooltipArea = document.getElementById("tooltipArea");
    const senhaInput = document.getElementById("senha");
    const sugestaoDiv = document.getElementById("sugestaoSenha");

    if (!tooltipArea || !senhaInput || !sugestaoDiv) return;
    
    let senhaAtual = "";

    // Mostra a sugestão de senha ao passar o mouse
    tooltipArea.addEventListener("mouseenter", () => {
        senhaAtual = gerarSenhaForte();
        sugestaoDiv.textContent = "Clique para usar: " + senhaAtual;
        sugestaoDiv.style.display = "block";
    });

    // Esconde a sugestão ao tirar o mouse
    tooltipArea.addEventListener("mouseleave", () => {
        setTimeout(() => {
            if (!tooltipArea.matches(':hover') && !sugestaoDiv.matches(':hover')) {
                sugestaoDiv.style.display = "none";
            }
        }, 200);
    });

    // Preenche o campo com a senha sugerida ao clicar
    sugestaoDiv.addEventListener("click", () => {
        senhaInput.value = senhaAtual;
        senhaInput.type = "text"; // Mostrar a senha temporariamente
        
        // Disparar evento para atualizar o medidor de força
        senhaInput.dispatchEvent(new Event('input'));
        
        // Após um breve momento, esconder a senha novamente
        setTimeout(() => {
            senhaInput.type = "password";
        }, 1500);
        
        sugestaoDiv.style.display = "none";
    });
});