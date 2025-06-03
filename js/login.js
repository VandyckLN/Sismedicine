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
        const mensagemBox = document.querySelector('.mensagem-erro');
        if (!mensagemBox) return;

        // Adicionar elemento para contagem regressiva
        const contador = document.createElement('div');
        contador.className = 'contador-bloqueio';
        contador.innerHTML = formatarTempo(segundosRestantes);
        mensagemBox.appendChild(contador);

        // Atualizar contagem a cada segundo
        const intervalo = setInterval(() => {
            segundosRestantes--;
            if (segundosRestantes <= 0) {
                clearInterval(intervalo);
                location.reload(); // Recarrega a página quando o tempo acabar
            } else {
                contador.innerHTML = formatarTempo(segundosRestantes);
            }
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

        // Adicionar o título da tela de carregamento
        const titulo = document.createElement('h2');
        titulo.textContent = 'Login Realizado com Sucesso';
        titulo.className = 'carregamento-titulo';
        telaCarregamento.appendChild(titulo);

        // Adicionar mensagem
        const mensagem = document.createElement('p');
        mensagem.textContent = 'Carregando sistema...';
        mensagem.className = 'carregamento-mensagem';
        telaCarregamento.appendChild(mensagem);

        // Container do progresso
        const progressoContainer = document.createElement('div');
        progressoContainer.className = 'progresso-container';

        // Barra de progresso
        const progressoBar = document.createElement('div');
        progressoBar.className = 'progresso-bar';

        // Valor do progresso
        const progressoValor = document.createElement('span');
        progressoValor.className = 'progresso-valor';
        progressoValor.textContent = '0%';

        // Adicionar elementos à hierarquia
        progressoContainer.appendChild(progressoBar);
        telaCarregamento.appendChild(progressoContainer);
        telaCarregamento.appendChild(progressoValor);

        // Adicionar ao body
        document.body.appendChild(telaCarregamento);

        // Ocultar o container de login e o overlay
        const loginContainer = document.querySelector('.login-container');
        const overlay = document.querySelector('.overlay');
        if (loginContainer) loginContainer.style.display = 'none';
        if (overlay) overlay.style.display = 'none';

        // Animar o progresso ao longo de 2 segundos
        let progresso = 0;
        const duracaoTotal = 2000; // 2 segundos
        const intervalo = 20; // atualizar a cada 20ms para animação suave
        const incremento = 100 / (duracaoTotal / intervalo);

        const animacaoProgresso = setInterval(() => {
            progresso += incremento;
            if (progresso >= 100) {
                progresso = 100;
                clearInterval(animacaoProgresso);

                // Redirecionar após o progresso chegar a 100%
                setTimeout(() => {
                    window.location.href = url;
                }, 200); // Pequeno delay para mostrar o 100%
            }

            // Atualizar a barra de progresso
            progressoBar.style.width = progresso + '%';
            progressoValor.textContent = Math.round(progresso) + '%';
        }, intervalo);
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