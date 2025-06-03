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
                    <?php if (isset($_SESSION['nivel_acesso']) && $_SESSION['nivel_acesso'] == 'Administrador'): ?>
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
                            
                            <!-- Gráfico de dispensações mensais -->
                            <div class="content-section">
                                <div class="section-header">
                                    <div class="section-title">
                                        <i class="fas fa-chart-line"></i> Dispensações Mensais
                                    </div>
                                    <div class="section-actions">
                                        <button class="btn btn-outline">
                                            <i class="fas fa-download"></i> Exportar
                                        </button>
                                    </div>
                                </div>
                                <div class="section-body">
                                    <div class="chart-container" id="dispensacoes-chart">
                                        <!-- Aqui será renderizado o gráfico -->
                                        <p>Carregando gráfico de dispensações...</p>
                                    </div>
                                </div>
                            </div>
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
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../js/dashboard.js"></script>
</body>
</html>
