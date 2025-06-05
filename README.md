# sismedicine
SIS Medicine - Sistema de Gestão de Medicamentos
SIS Medicine Logo

Sobre o Projeto
O SIS Medicine é um sistema completo de gestão de farmácia hospitalar desenvolvido para otimizar o controle de medicamentos, dispensações e gerenciamento de usuários. O sistema oferece uma interface intuitiva e responsiva que facilita o trabalho dos profissionais de saúde no ambiente hospitalar.

Funcionalidades Principais
1. Autenticação e Segurança
Sistema seguro de login com proteção contra ataques CSRF
Senhas criptografadas com hash usando password_hash()
Timeout de sessão automático após 30 minutos de inatividade
Geração de token para segurança nas transações
Diferentes níveis de acesso (Administrador e Farmacêutico)
2. Dashboard
Visão geral das estatísticas do sistema
Cards informativos com total de medicamentos, dispensações diárias e alertas de estoque crítico
Gráfico de pizza para visualização das dispensações por medicamento
Tabela de atividades recentes no sistema
Lista dos medicamentos mais dispensados
3. Gestão de Medicamentos
Cadastro completo de medicamentos com descrição e validade
Listagem com busca e filtros
Controle de estoque com alertas para quantidades críticas
Visualização detalhada de cada medicamento
4. Dispensação de Medicamentos
Interface intuitiva para registro de dispensações
Seleção de medicamentos por nome
Registro de paciente e observações
Baixa automática no estoque
5. Relatórios
Geração de relatórios de dispensações
Exportação de gráficos para PDF
Visualização de dados por períodos (mensal/anual)
6. Gerenciamento de Usuários
Cadastro de novos usuários do sistema
Definição de níveis de acesso
Sistema de sugestão de senhas fortes
Validação de segurança nas senhas
Tecnologias Utilizadas
Frontend
HTML5
CSS3 (Design responsivo para desktop, tablet e mobile)
JavaScript
Chart.js para visualização de dados
Font Awesome para ícones
Backend
PHP 8.0.30
MySQL/MariaDB 10.4.32
Consultas preparadas para segurança contra injeção SQL
Bibliotecas
html2canvas 1.4.1
jsPDF 2.5.1
Chart.js 3.9.1
Desenvolvimento e Colaboração
GitHub para controle de versão
ZeroTier para rede privada virtual de desenvolvimento
XAMPP como ambiente de desenvolvimento local
Estrutura do Banco de Dados
O sistema utiliza um banco de dados relacional com as seguintes tabelas principais:

usuarios: Armazena informações dos usuários do sistema
medicamentos: Cadastro de medicamentos com descrição e validade
dispensacoes: Registro de todas as dispensações realizadas
Instalação e Configuração
Requisitos
XAMPP 8.0.30 ou superior (Apache, MySQL, PHP)
Navegador web moderno (Chrome, Firefox, Edge)
Passos para Instalação
Clone o repositório

Importe o banco de dados

Inicie o XAMPP (Apache e MySQL)
Acesse o phpMyAdmin (http://localhost/phpmyadmin)
Crie um banco de dados chamado farmacia_hospitalar
Importe o arquivo farmacia_hospitalar.sql
Configure a conexão

Verifique se o arquivo conexao.php está com os dados corretos para seu ambiente
Acesse o sistema

Navegue até http://localhost/sismedicineOficial/sismedicine/php/login.php
Use as credenciais padrão: usuário admin e senha admin123
Ambiente de Desenvolvimento
ZeroTier para Teste em Servidor Privado
O desenvolvimento utiliza a tecnologia ZeroTier para criar uma rede privada virtual, permitindo que todos os desenvolvedores testem o sistema em um ambiente compartilhado sem necessidade de hospedagem externa.

Benefícios do ZeroTier no projeto:

Testes em ambiente unificado
Compartilhamento seguro de recursos
Simulação de ambiente de produção
Acesso remoto ao servidor de desenvolvimento
Fluxo de Desenvolvimento
Desenvolvimento local com XAMPP
Controle de versão com GitHub
Testes colaborativos via ZeroTier
Implementação em ambiente de produção
Detalhes de Implementação
Segurança
Validação de entrada em todos os formulários
Proteção contra ataques CSRF com tokens
Senhas armazenadas com hash seguro
Sanitização de dados antes de consultas SQL
Responsividade
O sistema foi desenvolvido seguindo princípios de design responsivo:

Layout adaptável para dispositivos móveis, tablets e desktops
Media queries para ajustes específicos de cada dispositivo
Experiência de usuário otimizada independente do dispositivo
Equipe
O SIS Medicine foi desenvolvido pelo Grupo-04 como parte do projeto acadêmico para o curso de Desenvolvimento de Sistemas.

Licença
Este projeto está licenciado sob a licença MIT - veja o arquivo LICENSE para mais detalhes.

© 2025 Grupo-04. Todos os direitos reservados.

Para mais informações, acesse a página de créditos do sistema.