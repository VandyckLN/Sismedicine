/**
 * Script para funcionalidades do dashboard
 */

document.addEventListener('DOMContentLoaded', function () {
    console.log('DOM carregado completamente');

    // Inicializar o relógio
    atualizarRelogio();
    setInterval(atualizarRelogio, 1000);

    // Esperar um momento para garantir que o Chart.js esteja carregado
    setTimeout(function () {
        console.log('Tentando inicializar o gráfico...');

        // Verificar se o Chart.js foi carregado
        if (typeof Chart === 'undefined') {
            console.error('Chart.js não está disponível. Verificando...');
            // Tentar carregar novamente o Chart.js
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js';
            script.onload = function () {
                console.log('Chart.js carregado manualmente');
                inicializarGraficoPizzaAnual();
            };
            document.head.appendChild(script);
        } else {
            console.log('Chart.js está disponível, inicializando gráfico');
            inicializarGraficoPizzaAnual();
        }
    }, 1000);
});

// Função para atualizar o relógio
function atualizarRelogio() {
    const agora = new Date();
    const horas = agora.getHours().toString().padStart(2, '0');
    const minutos = agora.getMinutes().toString().padStart(2, '0');
    const segundos = agora.getSeconds().toString().padStart(2, '0');

    const elementoRelogio = document.getElementById('relogio');
    if (elementoRelogio) {
        elementoRelogio.textContent = `${horas}:${minutos}:${segundos}`;
    }
}

// Dados simulados para o total anual de dispensações por medicamento
const dadosMedicamentosAnual = [
    { nome: "Dipirona 500mg", quantidade: 2745, cor: "#FF6384" },
    { nome: "Paracetamol 750mg", quantidade: 1856, cor: "#36A2EB" },
    { nome: "Amoxicilina 500mg", quantidade: 1224, cor: "#FFCE56" },
    { nome: "Losartana 50mg", quantidade: 987, cor: "#4BC0C0" },
    { nome: "Omeprazol 20mg", quantidade: 856, cor: "#9966FF" },
    { nome: "Metformina 850mg", quantidade: 742, cor: "#FF9F40" },
    { nome: "Ibuprofeno 600mg", quantidade: 632, cor: "#7ED321" },
    { nome: "Atenolol 50mg", quantidade: 512, cor: "#B8E986" },
    { nome: "Azitromicina 500mg", quantidade: 423, cor: "#50E3C2" },
    { nome: "Loratadina 10mg", quantidade: 362, cor: "#BD10E0" }
];

// Função para inicializar o gráfico de pizza com dados anuais
function inicializarGraficoPizzaAnual() {
    console.log("Inicializando gráfico de pizza anual...");

    try {
        // Verificar se o elemento canvas existe na página
        const canvas = document.getElementById('dispensacoes-chart');
        if (!canvas) {
            console.error('Elemento canvas para o gráfico não encontrado');
            return;
        }

        console.log("Canvas encontrado:", canvas);

        // Verificar se o contexto 2D pode ser obtido
        const ctx = canvas.getContext('2d');
        if (!ctx) {
            console.error('Não foi possível obter o contexto 2D do canvas');
            return;
        }

        console.log("Contexto 2D obtido com sucesso");

        // Limpar qualquer gráfico existente
        if (window.graficoDispensacoes) {
            window.graficoDispensacoes.destroy();
            console.log("Gráfico anterior destruído");
        }

        // Esconder o indicador de carregamento
        const loading = document.getElementById('chart-loading');
        if (loading) {
            loading.style.display = 'none';
            console.log("Indicador de carregamento escondido");
        }

        // Preparar os dados para o Chart.js
        console.log("Preparando dados para o gráfico...");
        const labels = dadosMedicamentosAnual.map(item => item.nome);
        const dados = dadosMedicamentosAnual.map(item => item.quantidade);
        const cores = dadosMedicamentosAnual.map(item => item.cor);

        console.log(`Dados preparados: ${labels.length} itens`);

        // Criar o gráfico de pizza
        console.log("Criando o gráfico de pizza...");
        window.graficoDispensacoes = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: dados,
                    backgroundColor: cores,
                    borderColor: '#ffffff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((acc, val) => acc + val, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        console.log("Gráfico criado com sucesso!");

        // Criar legenda personalizada
        atualizarLegendaAnual(dadosMedicamentosAnual);

        // Configurar botão de exportação
        const btnExport = document.getElementById('btn-export-chart');
        if (btnExport) {
            btnExport.removeEventListener('click', exportarGrafico);
            btnExport.addEventListener('click', exportarGrafico);
            console.log("Botão de exportação configurado");
        }
    } catch (error) {
        console.error("Erro ao criar gráfico:", error);

        // Mostrar mensagem de erro no container do gráfico
        const chartContainer = document.querySelector('.chart-container');
        const loading = document.getElementById('chart-loading');

        if (loading) {
            loading.innerHTML = `<i class="fas fa-exclamation-circle"></i><span>Erro ao carregar o gráfico: ${error.message}</span>`;
            loading.style.color = '#e74c3c';
        }
    }
}

// Função para criar uma legenda personalizada para o gráfico anual
function atualizarLegendaAnual(dados) {
    console.log("Atualizando legenda...");
    const legendaContainer = document.getElementById('chart-legend-container');
    if (!legendaContainer) {
        console.error('Elemento container da legenda não encontrado');
        return;
    }

    legendaContainer.innerHTML = '';

    const total = dados.reduce((acc, item) => acc + item.quantidade, 0);

    // Criar elementos da legenda para cada medicamento
    dados.forEach(item => {
        const porcentagem = Math.round((item.quantidade / total) * 100);

        const legendaItem = document.createElement('div');
        legendaItem.className = 'legend-item';

        const legendaCor = document.createElement('div');
        legendaCor.className = 'legend-color';
        legendaCor.style.backgroundColor = item.cor;

        const legendaLabel = document.createElement('span');
        legendaLabel.className = 'legend-label';
        legendaLabel.textContent = `${item.nome}: `;

        const legendaValor = document.createElement('span');
        legendaValor.className = 'legend-value';
        legendaValor.textContent = `${item.quantidade} (${porcentagem}%)`;

        legendaItem.appendChild(legendaCor);
        legendaItem.appendChild(legendaLabel);
        legendaItem.appendChild(legendaValor);

        legendaContainer.appendChild(legendaItem);
    });

    // Adicionar total
    const totalItem = document.createElement('div');
    totalItem.className = 'legend-item total';
    totalItem.innerHTML = `<strong>Total de Dispensações:</strong> <span class="legend-value">${total}</span>`;
    legendaContainer.appendChild(totalItem);

    console.log("Legenda atualizada com sucesso!");
}

// Função para exportar o gráfico em PDF
function exportarGrafico() {
    console.log("Iniciando exportação para PDF...");

    try {
        // Verificar se as bibliotecas necessárias estão carregadas
        if (typeof html2canvas === 'undefined' || typeof window.jspdf === 'undefined') {
            console.error("Bibliotecas de exportação não carregadas");
            alert("Não foi possível carregar as bibliotecas necessárias para exportação. Tente novamente mais tarde.");
            return;
        }

        // Mostrar feedback visual
        const btnExport = document.getElementById('btn-export-chart');
        const btnTextOriginal = btnExport.innerHTML;
        btnExport.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Gerando PDF...';
        btnExport.disabled = true;

        // Capturar a seção do gráfico
        const chartSection = document.querySelector('.content-section:has(#dispensacoes-chart)');
        if (!chartSection) {
            console.error("Seção do gráfico não encontrada");
            btnExport.innerHTML = btnTextOriginal;
            btnExport.disabled = false;
            return;
        }

        // Usar html2canvas para capturar a seção
        html2canvas(chartSection, {
            scale: 2,
            logging: true,
            useCORS: true,
            allowTaint: true
        }).then(function (canvas) {
            // Criar o PDF
            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF('p', 'mm', 'a4');

            // Dimensões da página A4
            const pageWidth = pdf.internal.pageSize.getWidth();
            const pageHeight = pdf.internal.pageSize.getHeight();

            // Calcular proporção para ajustar à página
            const imgWidth = pageWidth - 20;
            const imgHeight = (canvas.height * imgWidth) / canvas.width;

            // Adicionar título
            pdf.setFontSize(16);
            pdf.setTextColor(44, 62, 80);
            pdf.text('Relatório de Dispensações de Medicamentos (2025)', pageWidth / 2, 20, { align: 'center' });

            // Adicionar subtítulo com data
            pdf.setFontSize(12);
            pdf.setTextColor(100, 100, 100);
            pdf.text(`Gerado em: ${new Date().toLocaleString('pt-BR')}`, pageWidth / 2, 30, { align: 'center' });

            // Adicionar a imagem
            const imgData = canvas.toDataURL('image/png');
            pdf.addImage(imgData, 'PNG', 10, 40, imgWidth, imgHeight);

            // Adicionar rodapé
            pdf.setFontSize(10);
            pdf.setTextColor(150, 150, 150);
            pdf.text('SIS Medicine - Sistema de Gestão de Medicamentos', pageWidth / 2, pageHeight - 10, { align: 'center' });

            // Salvar o PDF
            pdf.save('Dispensacoes_Medicamentos_2025.pdf');

            // Restaurar o botão
            btnExport.innerHTML = btnTextOriginal;
            btnExport.disabled = false;

            console.log("PDF gerado com sucesso!");
        }).catch(function (error) {
            console.error("Erro ao gerar PDF:", error);
            alert("Ocorreu um erro ao gerar o PDF. Tente novamente.");

            btnExport.innerHTML = btnTextOriginal;
            btnExport.disabled = false;
        });
    } catch (error) {
        console.error("Erro na exportação:", error);
        alert("Ocorreu um erro ao exportar o gráfico. Tente novamente.");
    }
}
