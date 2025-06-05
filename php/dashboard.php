<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

// Verifica tempo de inatividade (30 minutos)
$tempo_maximo_inatividade = 1800; // 30 minutos em segundos
if (isset($_SESSION['ultima_atividade']) && 
    (time() - $_SESSION['ultima_atividade'] > $tempo_maximo_inatividade)) {
    // Destruir sessão
    session_unset();
    session_destroy();
    header("Location: login.php?erro=sessao_expirada");
    exit;
}

// Atualiza timestamp de última atividade
$_SESSION['ultima_atividade'] = time();

// Define a página atual para destacar no menu
$pagina_atual = "dashboard";

// Obtém a primeira letra do nome de usuário para o avatar
$inicial_usuario = strtoupper(substr($_SESSION['usuario'], 0, 1));

// Data atual formatada
$data_atual = date("d/m/Y");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIS Medicine - Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../css/dashboard.css">
    <style>
        /* Cabeçalho da página */
        .page-header {
            margin-bottom: 20px;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 15px;
        }

        .page-header h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-dark);
        }

        .page-header small {
            font-size: 0.9rem;
            color: var(--text-light);
            font-weight: normal;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <!-- Cabeçalho com informações do usuário -->
    <header>
        <h1>SIS Medicine</h1>
        <button class="team-button" onclick="window.location.href='creditos.html'">Equipe</button>
        <div class="user-info">
            <div id="relogio"></div>
            <div class="user-welcome">
                <div class="user-avatar"><?php echo $inicial_usuario; ?></div>
                <span>Bem-vindo, <?php echo htmlspecialchars($_SESSION['usuario']); ?></span>
            </div>
            <a href="logout.php" class="logout">
                <i class="fas fa-sign-out-alt"></i> Sair
            </a>
        </div>
    </header>
    
    <div class="container">
        <div class="dashboard-grid">
            <!-- Menu lateral -->
            <div class="sidebar">
                <div class="menu-title">Menu Principal</div>
                <div class="menu-items">
                    <div class="menu-item <?php echo $pagina_atual === 'dashboard' ? 'active' : ''; ?>">
                        <a href="dashboard.php">
                            <i class="fas fa-home"></i>
                            <span>Dashboard</span>
                        </a>
                    </div>
                    <div class="menu-item <?php echo $pagina_atual === 'dispensacao' ? 'active' : ''; ?>">
                        <a href="dispensacao.php">
                            <i class="fas fa-pills"></i>
                            <span>Dispensação</span>
                        </a>
                    </div>
                    <div class="menu-item <?php echo $pagina_atual === 'cadastro_medicamento' ? 'active' : ''; ?>">
                        <a href="cadastro_medicamento.php">
                            <i class="fas fa-capsules"></i>
                            <span>Cadastrar Medicamentos</span>
                        </a>
                    </div>
                    <div class="menu-item <?php echo $pagina_atual === 'listar_medicamentos' ? 'active' : ''; ?>">
                        <a href="listar_medicamentos.php">
                            <i class="fas fa-list"></i>
                            <span>Listar Medicamentos</span>
                        </a>
                    </div>
                    <div class="menu-item <?php echo $pagina_atual === 'relatorios' ? 'active' : ''; ?>">
                        <a href="relatorios.php">
                            <i class="fas fa-chart-bar"></i>
                            <span>Relatórios</span>
                        </a>
                    </div>
                    
                    <?php 
                    // Verificar se o usuário é administrador (pode ser pelo ID ou outro critério)
                    // Por exemplo, vamos verificar se o ID é 1 (geralmente o primeiro usuário é admin)
                    if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == 1): 
                    ?>
                    <div class="menu-item <?php echo $pagina_atual === 'cadastro_usuario' ? 'active' : ''; ?>">
                        <a href="cadastro_usuario.php">
                            <i class="fas fa-users-cog"></i>
                            <span>Gerenciar Usuários</span>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Área de conteúdo -->
            <div class="content-area">
                <!-- Cabeçalho da página -->
                <div class="page-header">
                    <h2 id="dashboard-title">Dashboard - Visão geral do sistema - <?php echo $data_atual; ?></h2>
                </div>
                
                <!-- Container principal do dashboard -->
                <div class="dashboard-content">
                    <!-- Linha principal com cards e widgets lado a lado -->
                    <div class="main-content-row">
                        <!-- Coluna esquerda - Status Cards -->
                        <div>
                            <div class="stats-container">
                                <div class="status-card">
                                    <div class="card-title">
                                        <i class="fas fa-capsules"></i> Total de Medicamentos
                                    </div>
                                    <div class="card-value">128</div>
                                    <div class="card-desc">Medicamentos cadastrados</div>
                                    <div class="card-icon"><i class="fas fa-pills"></i></div>
                                </div>
                                
                                <div class="status-card">
                                    <div class="card-title">
                                        <i class="fas fa-hand-holding-medical"></i> Dispensações
                                    </div>
                                    <div class="card-value">23</div>
                                    <div class="card-desc">Realizadas hoje</div>
                                    <div class="card-icon"><i class="fas fa-clipboard-list"></i></div>
                                </div>
                                
                                <div class="status-card">
                                    <div class="card-title">
                                        <i class="fas fa-exclamation-triangle"></i> Estoque Crítico
                                    </div>
                                    <div class="card-value">7</div>
                                    <div class="card-desc">Reposição necessária</div>
                                    <div class="card-icon"><i class="fas fa-exclamation-circle"></i></div>
                                </div>
                            </div>
                            
                            <!-- Gráfico de dispensações mensais (modificado para gráfico de pizza) -->
                            <!--
                            <div class="content-section">
                                <div class="section-header">
                                    <div class="section-title">
                                        <i class="fas fa-chart-pie"></i> Dispensações de Medicamentos
                                    </div>
                                    <div class="section-actions">
                                        <button class="btn btn-outline" id="btn-export-chart">
                                            <i class="fas fa-download"></i> Exportar
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Seletor de meses -->
                                <!--
                                <div class="month-selector">
                                    <button class="month-nav prev-month"><i class="fas fa-chevron-left"></i></button>
                                    <span id="current-month">Junho 2025</span>
                                    <button class="month-nav next-month"><i class="fas fa-chevron-right"></i></button>
                                </div>
                                -->
                                
                                <!--
                                <div class="section-body">
                                    <div class="chart-container">
                                        <canvas id="dispensacoes-chart"></canvas>
                                    </div>
                                    <div class="chart-legend" id="chart-legend-container"></div>
                                </div>
                            </div>
                            -->
                        </div>
                        
                        <!-- Coluna direita - Widgets -->
                        <div class="widgets-container">
                            <!-- Widget de medicamentos mais dispensados -->
                            <div class="widget">
                                <div class="widget-header">
                                    <span>Medicamentos Mais Dispensados</span>
                                    <i class="fas fa-pills"></i>
                                </div>
                                <div class="widget-body">
                                    <ul class="widget-list">
                                        <li>1. Dipirona 500mg - 245 dispensações</li>
                                        <li>2. Paracetamol 750mg - 187 dispensações</li>
                                        <li>3. Amoxicilina 500mg - 124 dispensações</li>
                                        <li>4. Losartana 50mg - 98 dispensações</li>
                                        <li>5. Omeprazol 20mg - 76 dispensações</li>
                                    </ul>
                                </div>
                            </div>
                            
                            <!-- Widget de alertas -->
                            <div class="widget">
                                <div class="widget-header">
                                    <span>Alertas do Sistema</span>
                                    <i class="fas fa-bell"></i>
                                </div>
                                <div class="widget-body">
                                    <div class="alert">
                                        <i class="fas fa-exclamation-circle"></i>
                                        <span>3 medicamentos vencendo nos próximos 30 dias</span>
                                    </div>
                                    <div class="alert">
                                        <i class="fas fa-info-circle"></i>
                                        <span>Backup automático agendado para hoje às 23:00</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tabela de atividades recentes -->
                    <div class="content-section">
                        <div class="section-header">
                            <div class="section-title">
                                <i class="fas fa-history"></i> Atividades Recentes
                            </div>
                            <div class="section-actions">
                                <button class="btn btn-outline">
                                    <i class="fas fa-sync-alt"></i> Atualizar
                                </button>
                            </div>
                        </div>
                        <div class="section-body">
                            <table class="activities-table">
                                <thead>
                                    <tr>
                                        <th>Data/Hora</th>
                                        <th>Usuário</th>
                                        <th>Ação</th>
                                        <th>Detalhes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="activity-time">Hoje, 14:32</td>
                                        <td class="activity-user">Maria Silva</td>
                                        <td class="activity-action">Dispensou medicamento</td>
                                        <td>Dipirona 500mg - 20 comprimidos</td>
                                    </tr>
                                    <tr>
                                        <td class="activity-time">Hoje, 11:15</td>
                                        <td class="activity-user">João Santos</td>
                                        <td class="activity-action">Cadastrou medicamento</td>
                                        <td>Amoxicilina 250mg/5ml - Suspensão</td>
                                    </tr>
                                    <tr>
                                        <td class="activity-time">Ontem, 16:45</td>
                                        <td class="activity-user">Ana Oliveira</td>
                                        <td class="activity-action">Atualizou estoque</td>
                                        <td>Paracetamol 750mg - Adicionadas 150 unidades</td>
                                    </tr>
                                    <tr>
                                        <td class="activity-time">Ontem, 09:20</td>
                                        <td class="activity-user">Carlos Mendes</td>
                                        <td class="activity-action">Gerou relatório</td>
                                        <td>Relatório de dispensações do mês de maio</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Gráfico de dispensações anuais (total por medicamento) -->
                    <div class="content-section">
                        <div class="section-header">
                            <div class="section-title">
                                <i class="fas fa-chart-pie"></i> Total de Dispensações de Medicamentos (2025)
                            </div>
                            <div class="section-actions">
                                <button class="btn btn-outline" id="btn-export-chart" title="Exportar para PDF">
                                    <i class="fas fa-file-pdf"></i> Exportar PDF
                                </button>
                            </div>
                        </div>
                        
                        <div class="section-body">
                            <div class="chart-container">
                                <div id="chart-loading" class="chart-loading">
                                    <i class="fas fa-spinner fa-spin"></i>
                                    <span>Carregando gráfico...</span>
                                </div>
                                <canvas id="dispensacoes-chart"></canvas>
                            </div>
                            <div class="chart-legend" id="chart-legend-container"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Carregar as bibliotecas na ordem correta -->
    <!-- Chart.js primeiro -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

    <!-- Bibliotecas para exportação em PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <!-- CSS específico para o gráfico -->
    <link rel="stylesheet" href="../css/grafico-pizza.css">

    <!-- Script simplificado apenas para o gráfico -->
    <script src="../js/grafico-pizza.js"></script>

    <!-- Verificação inline para garantir que o gráfico seja renderizado -->
    <script>
    // Verificação de segurança adicional
    window.addEventListener('load', function() {
        console.log("Página totalmente carregada");
        
        // Se após 3 segundos o gráfico ainda não foi criado, tentar novamente
        setTimeout(function() {
            const canvas = document.getElementById('dispensacoes-chart');
            if (!canvas.__chartjs) {
                console.log("Gráfico não foi inicializado. Tentando novamente...");
                
                // Forçar criação do gráfico
                if (typeof criarGraficoPizza === 'function') {
                    criarGraficoPizza();
                } else {
                    console.error("Função criarGraficoPizza não encontrada");
                    
                    // Último recurso - criar um gráfico inline
                    const ctx = canvas.getContext('2d');
                    if (ctx && typeof Chart !== 'undefined') {
                        document.getElementById('chart-loading').style.display = 'none';
                        new Chart(ctx, {
                            type: 'pie',
                            data: {
                                labels: ["Dipirona", "Paracetamol", "Amoxicilina", "Outros"],
                                datasets: [{
                                    data: [2745, 1856, 1224, 4514],
                                    backgroundColor: ["#FF6384", "#36A2EB", "#FFCE56", "#4BC0C0"]
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false
                            }
                        });
                    } else {
                        console.error("Não foi possível criar um gráfico de emergência");
                    }
                }
            } else {
                console.log("Gráfico já inicializado");
            }
        }, 3000);
    });
    </script>

    <?php
    // Consulta para obter os dados reais de dispensação para o gráfico
    include_once("conexao.php");

    $ano_atual = date('Y');
    $sql_dispensacoes = "SELECT 
        m.nome AS medicamento,
        SUM(d.quantidade) AS total
    FROM dispensacoes d
    JOIN medicamentos m ON d.medicamento_id = m.id
    WHERE YEAR(d.data_disp) = ?
    GROUP BY d.medicamento_id
    ORDER BY total DESC
    LIMIT 10";

    $stmt = $conn->prepare($sql_dispensacoes);
    $stmt->bind_param("i", $ano_atual);
    $stmt->execute();
    $result = $stmt->get_result();

    $dados_grafico = [];
    $cores = [
        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', 
        '#FF9F40', '#7ED321', '#B8E986', '#50E3C2', '#BD10E0'
    ];

    $i = 0;
    while ($row = $result->fetch_assoc()) {
        $dados_grafico[] = [
            'nome' => $row['medicamento'],
            'quantidade' => intval($row['total']),
            'cor' => $cores[$i % count($cores)]
        ];
        $i++;
    }

    $stmt->close();
    $conn->close();
    ?>

    <!-- Script para injetar os dados reais -->
    <script>
    // Substituir os dados estáticos pelos dados do banco
    <?php if (!empty($dados_grafico)): ?>
    const dadosMedicamentosAnual = <?php echo json_encode($dados_grafico); ?>;
    <?php else: ?>
    // Manter os dados fictícios se não houver dados reais
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
    <?php endif; ?>
    </script>

    <!-- Adicionar antes de fechar a tag </body> -->
    <script>
    // Verificação extra para dados vazios
    document.addEventListener('DOMContentLoaded', function() {
        // Se não houver dados e o gráfico não for criado
        setTimeout(function() {
            const canvas = document.getElementById('dispensacoes-chart');
            const loading = document.getElementById('chart-loading');
            
            // Se o gráfico não foi criado e não temos dados
            if ((!canvas.__chartjs || !window.graficoPizza) && 
                (!dadosMedicamentosAnual || dadosMedicamentosAnual.length === 0)) {
                
                if (loading) {
                    loading.innerHTML = '<i class="fas fa-info-circle"></i><span>Nenhum dado de dispensação disponível para o período.</span>';
                }
                
                // Adicionar mensagem na legenda
                const legendContainer = document.getElementById('chart-legend-container');
                if (legendContainer) {
                    legendContainer.innerHTML = '<div class="no-data-message">Não há dados de dispensação registrados para exibir no gráfico. Realize dispensações para visualizar estatísticas.</div>';
                }
            }
        }, 3500);
    });
    </script>
</body>
</html>
