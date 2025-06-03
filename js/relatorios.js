// Exportar tabela para CSV
function exportarCSV() {
    let csv = [];
    let rows = document.querySelectorAll("#tabela-relatorio tr");
    for (let i = 0; i < rows.length; i++) {
        let cols = rows[i].querySelectorAll("td, th");
        let row = [];
        for (let j = 0; j < cols.length; j++) {
            let text = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, " ").replace(/"/g, '""');
            row.push('"' + text + '"');
        }
        csv.push(row.join(","));
    }
    let csvFile = new Blob([csv.join("\n")], { type: "text/csv" });
    let downloadLink = document.createElement("a");
    downloadLink.download = `relatorio_dispensacoes_${new Date().toISOString().slice(0,10)}.csv`;
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = "none";
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}

// Exportar para Excel usando SheetJS
function exportarExcel() {
    const tabela = document.getElementById('tabela-relatorio');
    const workbook = XLSX.utils.table_to_book(tabela, { sheet: "Relatório" });
    XLSX.writeFile(workbook, `relatorio_dispensacoes_${new Date().toISOString().slice(0,10)}.xlsx`);
}

// Filtro dinâmico na tabela
document.addEventListener('DOMContentLoaded', function() {
    const filtroNome = document.getElementById('filtroNome');
    if (filtroNome) {
        filtroNome.addEventListener('input', function() {
            const filtro = this.value.toLowerCase();
            const linhas = document.querySelectorAll('#tabela-relatorio tbody tr');
            linhas.forEach(linha => {
                const nomeMedicamento = linha.cells[1].innerText.toLowerCase();
                linha.style.display = nomeMedicamento.includes(filtro) ? '' : 'none';
            });
        });
    }

    // Gráfico com Chart.js
    const medicamentos = window.medicamentosGrafico || [];
    const totais = window.totaisGrafico || [];
    if (medicamentos.length === 0) {
        document.getElementById('graficoDispensacoes').insertAdjacentHTML('afterend', '<p>Nenhum dado para exibir no gráfico.</p>');
    } else {
        const ctx = document.getElementById('graficoDispensacoes').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: medicamentos,
                datasets: [{
                    label: 'Dispensações',
                    data: totais,
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        precision: 0
                    }
                },
                plugins: {
                    legend: {
                        display: true
                    }
                }
            }
        });
    }
});