/**
 * Validador de senha forte
 * Regras:
 * - Mínimo de 8 caracteres
 * - Pelo menos 1 letra maiúscula
 * - Pelo menos 1 letra minúscula
 * - Pelo menos 1 número
 * - Pelo menos 2 caracteres especiais
 */


function validarSenhaForte(senha) {
    // Verificar o comprimento mínimo de 8 caracteres
    if (senha.length < 8) {
        return {
            valido: false,
            mensagem: "A senha deve ter pelo menos 8 caracteres"
        };
    }

    // Verificar se há pelo menos uma letra maiúscula
    if (!/[A-Z]/.test(senha)) {
        return {
            valido: false,
            mensagem: "A senha deve conter pelo menos uma letra maiúscula"
        };
    }

    // Verificar se há pelo menos uma letra minúscula
    if (!/[a-z]/.test(senha)) {
        return {
            valido: false,
            mensagem: "A senha deve conter pelo menos uma letra minúscula"
        };
    }

    // Verificar se há pelo menos um número
    if (!/[0-9]/.test(senha)) {
        return {
            valido: false,
            mensagem: "A senha deve conter pelo menos um número"
        };
    }

    // Verificar se há pelo menos dois caracteres especiais
    const caracteresEspeciais = senha.match(/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/g);
    if (!caracteresEspeciais || caracteresEspeciais.length < 2) {
        return {
            valido: false,
            mensagem: "A senha deve conter pelo menos dois caracteres especiais"
        };
    }

    // Se passou por todas as verificações, a senha é válida
    return {
        valido: true,
        mensagem: "Senha forte"
    };
}

/**
 * Calcula e retorna a força da senha como um percentual (0-100)
 * @param {string} senha - A senha a ser avaliada
 * @return {number} Percentual de força da senha
 */
function calcularForcaSenha(senha) {
    let pontuacao = 0;

    // Comprimento base
    pontuacao += Math.min(senha.length * 4, 25);

    // Letras maiúsculas
    const maiusculas = senha.match(/[A-Z]/g);
    if (maiusculas) {
        pontuacao += Math.min(maiusculas.length * 5, 15);
    }

    // Letras minúsculas
    const minusculas = senha.match(/[a-z]/g);
    if (minusculas) {
        pontuacao += Math.min(minusculas.length * 5, 15);
    }

    // Números
    const numeros = senha.match(/[0-9]/g);
    if (numeros) {
        pontuacao += Math.min(numeros.length * 5, 15);
    }

    // Caracteres especiais
    const especiais = senha.match(/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/g);
    if (especiais) {
        pontuacao += Math.min(especiais.length * 8, 30);
    }

    return Math.min(pontuacao, 100);
}

/**
 * Retorna uma classe CSS baseada na força da senha
 * @param {number} forca - Percentual de força da senha (0-100)
 * @return {string} Nome da classe CSS
 */
function getClasseForcaSenha(forca) {
    if (forca < 30) return "muito-fraca";
    if (forca < 50) return "fraca";
    if (forca < 75) return "media";
    if (forca < 90) return "forte";
    return "muito-forte";
}

// Exportar funções para uso em outros arquivos
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        validarSenhaForte,
        calcularForcaSenha,
        getClasseForcaSenha
    };
}