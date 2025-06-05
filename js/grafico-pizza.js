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

// Função para criar o gráfico
function criarGraficoPizza() {
    console.log("Iniciando criação do gráfico de pizza...");

    try {
        // Verificar se o Chart.js está disponível
        if (typeof Chart === 'undefined') {
            throw new Error("Chart.js não está carregado");
        }

        // Verificar se o canvas existe
        const canvas = document.getElementById('dispensacoes-chart');
        if (!canvas) {
            throw new Error("Canvas não encontrado");
        }

        // Verificar se podemos obter o contexto 2D
        const ctx = canvas.getContext('2d');
        if (!ctx) {
            throw new Error("Não foi possível obter o contexto 2D");
        }

        // Esconder o indicador de carregamento
        const loading = document.getElementById('chart-loading');
        if (loading) {
            loading.style.display = 'none';
        }

        // Verificar se temos os dados do gráfico
        if (!dadosMedicamentosAnual || !Array.isArray(dadosMedicamentosAnual) || dadosMedicamentosAnual.length === 0) {
            throw new Error("Dados para o gráfico não estão disponíveis");
        }

        // Preparar os dados
        const labels = dadosMedicamentosAnual.map(item => item.nome);
        const dados = dadosMedicamentosAnual.map(item => item.quantidade);
        const cores = dadosMedicamentosAnual.map(item => item.cor);

        console.log("Dados preparados para o gráfico:", labels, dados);

        // Criar o gráfico
        window.graficoPizza = new Chart(ctx, {
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

        console.log("Gráfico criado com sucesso");

        // Criar legenda personalizada
        criarLegenda();

        // Configurar botão de exportação
        const btnExport = document.getElementById('btn-export-chart');
        if (btnExport) {
            btnExport.addEventListener('click', exportarGraficoPDF);
            console.log("Botão de exportação configurado");
        }
    } catch (error) {
        console.error("Erro ao criar gráfico:", error);
        // Mostrar mensagem de erro
        const loading = document.getElementById('chart-loading');
        if (loading) {
            loading.innerHTML = `<i class="fas fa-exclamation-circle" style="color:red"></i>
                <span style="color:red">Erro: ${error.message}</span>`;
        }
    }
}

// Função para criar a legenda
function criarLegenda() {
    console.log("Criando legenda...");
    const container = document.getElementById('chart-legend-container');
    if (!container) {
        console.error("Container da legenda não encontrado");
        return;
    }

    container.innerHTML = '';

    const total = dadosMedicamentosAnual.reduce((acc, item) => acc + item.quantidade, 0);

    dadosMedicamentosAnual.forEach(item => {
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

        container.appendChild(legendaItem);
    });

    // Adicionar total
    const totalItem = document.createElement('div');
    totalItem.className = 'legend-item total';
    totalItem.innerHTML = `<strong>Total de Dispensações:</strong> <span class="legend-value">${total}</span>`;
    container.appendChild(totalItem);

    console.log("Legenda criada com sucesso");
}

// Função para exportar o gráfico como PDF
function exportarGraficoPDF() {
    console.log("Iniciando exportação para PDF...");

    try {
        // Verificar se as bibliotecas necessárias estão disponíveis
        if (typeof html2canvas === 'undefined') {
            alert("A biblioteca html2canvas não está disponível. Não é possível exportar.");
            return;
        }

        if (typeof window.jspdf === 'undefined') {
            alert("A biblioteca jsPDF não está disponível. Não é possível exportar.");
            return;
        }

        // Modificar o botão para indicar que está processando
        const btnExport = document.getElementById('btn-export-chart');
        const textoOriginal = btnExport.innerHTML;
        btnExport.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Gerando PDF...';
        btnExport.disabled = true;

        // Capturar a seção inteira do gráfico
        const chartSection = document.querySelector('.content-section:has(#dispensacoes-chart)');
        if (!chartSection) {
            throw new Error("Não foi possível encontrar a seção do gráfico");
        }

        html2canvas(chartSection, {
            scale: 2,
            logging: false,
            useCORS: true,
            allowTaint: true
        }).then(canvas => {
            // Criar o PDF
            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF('p', 'mm', 'a4');

            // Dimensões da página A4
            const pageWidth = pdf.internal.pageSize.getWidth();
            const pageHeight = pdf.internal.pageSize.getHeight();

            // Adicionar título
            pdf.setFontSize(16);
            pdf.setTextColor(44, 62, 80);
            pdf.text('Relatório de Dispensações de Medicamentos (2025)', pageWidth / 2, 20, { align: 'center' });

            // Adicionar data de geração
            pdf.setFontSize(12);
            pdf.setTextColor(100, 100, 100);
            pdf.text(`Gerado em: ${new Date().toLocaleString('pt-BR')}`, pageWidth / 2, 30, { align: 'center' });

            // Calcular dimensões para a imagem
            const imgWidth = pageWidth - 20;
            const imgHeight = (canvas.height * imgWidth) / canvas.width;

            // Adicionar a imagem
            const imgData = canvas.toDataURL('image/png', 1.0);
            pdf.addImage(imgData, 'PNG', 10, 40, imgWidth, imgHeight);

            // Adicionar rodapé
            pdf.setFontSize(10);
            pdf.setTextColor(150, 150, 150);
            pdf.text('SIS Medicine - Sistema de Gestão de Medicamentos', pageWidth / 2, pageHeight - 10, { align: 'center' });

            // Salvar o PDF
            pdf.save('Dispensacoes_Medicamentos_2025.pdf');

            console.log("PDF gerado com sucesso");
        }).catch(error => {
            console.error("Erro ao gerar canvas para PDF:", error);
            alert("Ocorreu um erro ao gerar o PDF. Tente novamente.");
        }).finally(() => {
            // Restaurar o botão
            btnExport.innerHTML = textoOriginal;
            btnExport.disabled = false;
        });
    } catch (error) {
        console.error("Erro na exportação:", error);
        alert(`Erro ao exportar: ${error.message}`);

        // Restaurar o botão se houver erro
        const btnExport = document.getElementById('btn-export-chart');
        if (btnExport) {
            btnExport.innerHTML = '<i class="fas fa-file-pdf"></i> Exportar PDF';
            btnExport.disabled = false;
        }
    }
}

// Inicializar quando o documento estiver pronto
document.addEventListener('DOMContentLoaded', function () {
    console.log("DOM carregado. Aguardando para criar o gráfico...");
    // Esperar um momento para garantir que todas as bibliotecas estejam carregadas
    setTimeout(criarGraficoPizza, 500);
});

// Adicionar um backup caso o evento DOMContentLoaded já tenha ocorrido
if (document.readyState === 'complete' || document.readyState === 'interactive') {
    console.log("Documento já carregado. Criando gráfico...");
    setTimeout(criarGraficoPizza, 500);
}