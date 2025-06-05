/**
 * Script para animações e validações do login
 */
document.addEventListener('DOMContentLoaded', function () {
    console.log("Script de login carregado"); // Debug

    // Verificar parâmetros da URL
    const urlParams = new URLSearchParams(window.location.search);
    const erro = urlParams.get('erro');
    const sucesso = urlParams.get('sucesso');
    const tentativas = urlParams.get('tentativas');
    const tempo = urlParams.get('tempo');

    console.log("Parâmetros da URL:", { erro, sucesso, tentativas, tempo }); // Debug

    const mensagemContainer = document.getElementById('mensagem-container');


    if (erro === 'usuario') {
        let mensagem = 'Usuário não encontrado! Verifique suas credenciais.';
        if (tentativas) {
            const restantes = 3 - parseInt(tentativas);
            mensagem += ` Você tem mais ${restantes} tentativa(s) antes do bloqueio.`;
        }
        mostrarMensagem('erro', mensagem);
    } else if (erro === 'senha') {
        let mensagem = 'Senha incorreta! Tente novamente.';
        if (tentativas) {
            const restantes = 3 - parseInt(tentativas);
            mensagem += ` Você tem mais ${restantes} tentativa(s) antes do bloqueio.`;
        }
        mostrarMensagem('erro', mensagem);
    } else if (erro === 'bloqueado') {
        let mensagem = 'Acesso bloqueado temporariamente devido a múltiplas tentativas falhas.';
        if (tempo) {
            const minutos = Math.floor(tempo / 60);
            const segundos = tempo % 60;
            mensagem += ` Tente novamente em ${minutos} minuto(s) e ${segundos} segundo(s).`;

            // Iniciar contagem regressiva
            iniciarContagemRegressiva(parseInt(tempo));
        }
        mostrarMensagem('erro', mensagem, true);
    } else if (erro === 'seguranca') {
        mostrarMensagem('erro', 'Falha de segurança detectada! Por favor, tente novamente.');
    } else if (sucesso === 'true') {
        mostrarTelaCarregamento('dashboard.php');
    }

    // Função para iniciar contagem regressiva
    function iniciarContagemRegressiva(segundosRestantes) {
        const msgBox = document.querySelector('.mensagem-erro');
        if (!msgBox) return;

        const timerSpan = document.createElement('span');
        timerSpan.className = 'timer';
        timerSpan.textContent = formatarTempo(segundosRestantes);
        msgBox.appendChild(timerSpan);

        const interval = setInterval(() => {
            segundosRestantes--;

            if (segundosRestantes <= 0) {
                clearInterval(interval);
                window.location.href = 'login.php';
                return;
            }

            timerSpan.textContent = formatarTempo(segundosRestantes);
        }, 1000);
    }

    // Função para formatar tempo em MM:SS
    function formatarTempo(totalSegundos) {
        const minutos = Math.floor(totalSegundos / 60);
        const segundos = totalSegundos % 60;
        return `${minutos.toString().padStart(2, '0')}:${segundos.toString().padStart(2, '0')}`;
    }

    // Função para mostrar mensagem animada de erro
    function mostrarMensagem(tipo, texto, comTimer = false) {
        console.log(`Criando mensagem: ${tipo} - ${texto}`); // Debug

        // Limpa mensagens anteriores
        mensagemContainer.innerHTML = '';

        // Cria o elemento de mensagem
        const mensagem = document.createElement('div');
        mensagem.className = `mensagem mensagem-${tipo}`;

        // Adiciona ícone adequado
        const icone = document.createElement('span');
        icone.className = 'mensagem-icone';

        if (tipo === 'erro') {
            icone.innerHTML = '❌';
        } else if (tipo === 'sucesso') {
            icone.innerHTML = '✅';
        }

        mensagem.appendChild(icone);

        // Adiciona o texto
        const textoElement = document.createElement('span');
        textoElement.textContent = texto;
        mensagem.appendChild(textoElement);

        // Adiciona ao container
        mensagemContainer.appendChild(mensagem);

        // Adiciona botão de fechar para mensagens de erro (exceto se for bloqueio com timer)
        if (tipo === 'erro' && !comTimer) {
            const botaoFechar = document.createElement('span');
            botaoFechar.className = 'mensagem-fechar';
            botaoFechar.innerHTML = '&times;';
            botaoFechar.addEventListener('click', function () {
                mensagem.style.opacity = '0';
                setTimeout(() => {
                    mensagemContainer.removeChild(mensagem);
                }, 500);
            });
            mensagem.appendChild(botaoFechar);
        }
    }

    // Função para mostrar tela de carregamento
    function mostrarTelaCarregamento(url) {
        console.log("Criando tela de carregamento"); // Debug

        // Criar e exibir a tela de carregamento
        const telaCarregamento = document.createElement('div');
        telaCarregamento.className = 'tela-carregamento';

        const spinner = document.createElement('div');
        spinner.className = 'spinner';

        const mensagem = document.createElement('p');
        mensagem.textContent = 'Entrando...';

        telaCarregamento.appendChild(spinner);
        telaCarregamento.appendChild(mensagem);
        document.body.appendChild(telaCarregamento);

        // Redirecionar após um breve atraso
        setTimeout(() => {
            window.location.href = url;
        }, 1000);
    }

    // Limpar parâmetros da URL para evitar mensagens ao recarregar (opcional)
    if (erro || sucesso) {
        try {
            if (!erro || erro !== 'bloqueado') {
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        } catch (e) {
            console.error("Erro ao modificar histórico:", e);
        }
    }
});

