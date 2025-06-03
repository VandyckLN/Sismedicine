document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('graficoDispensacoes').getContext('2d');
    const grafico = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: medicamentosNomes,
            datasets: [{
                label: 'Quantidade Dispensada',
                data: medicamentosQuantidades,
                backgroundColor: 'rgba(75, 192, 192, 0.6)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 2
            }]
        },
        options: { 
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: { enabled: true }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Quantidade' }
                },
                x: {
                    title: { display: true, text: 'Medicamento' }
                }
            }
        }
    });
});

function exportarPDF() {
    html2canvas(document.querySelector(".chart-container")).then(canvas => {
        const imgData = canvas.toDataURL("image/png");
        const pdf = new jspdf.jsPDF();
        pdf.addImage(imgData, 'PNG', 10, 10, 190, 100);
        pdf.save("dashboard_dispensacoes.pdf");
    });
}