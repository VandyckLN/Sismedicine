-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 02/06/2025 às 00:55
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `farmacia_hospitalar`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `dispensacoes`
--

CREATE TABLE `dispensacoes` (
  `id` int(11) NOT NULL,
  `medicamento_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `paciente` varchar(255) NOT NULL DEFAULT current_timestamp(),
  `data_disp` date NOT NULL,
  `observacao` text NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `dispensacoes`
--

INSERT INTO `dispensacoes` (`id`, `medicamento_id`, `quantidade`, `paciente`, `data_disp`, `observacao`) VALUES
(15, 11, 1, 'David Martins', '2025-06-01', 'Protocolo 10000'),
(16, 13, 1, 'Roberto Silas', '2025-06-01', 'Protocolo 10002'),
(17, 13, 1, 'Paulo Martins', '2025-06-01', 'Protocolo 10003'),
(18, 13, 1, 'Patricia Silva', '2025-06-01', 'Procolo 10004'),
(19, 13, 1, 'Magno Silva', '2025-06-01', 'Protocolo 10005'),
(20, 13, 1, 'Maria das Graças Martins', '2025-06-01', 'Protocolo 1006'),
(21, 13, 1, 'David Martins', '2025-06-01', 'Protocolo 1007'),
(22, 11, 1, 'Roberto Silas', '2025-06-01', 'Protocolo 1008'),
(23, 11, 1, 'Pedro Massa', '2025-06-01', 'Protocolo 1009'),
(24, 11, 1, 'Martins Silveira', '2025-06-01', 'Paciente encontra-se em Observação'),
(25, 13, 1, 'Francisco Silva', '2025-06-02', 'Protocolo 100010');

-- --------------------------------------------------------

--
-- Estrutura para tabela `medicamentos`
--

CREATE TABLE `medicamentos` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `validade` date NOT NULL,
  `quantidade` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `medicamentos`
--

INSERT INTO `medicamentos` (`id`, `nome`, `descricao`, `validade`) VALUES
(11, 'Ciclofosfamida 50 mg', 'N04.1 - Síndrome nefrótica - lesões glomerulares focais e\\r\\nsegmentares\\r\\nN04.2 - Síndrome nefrótica - glomerulonefrite membranosa difusa\\r\\nN04.3 - Síndrome nefrótica - glomerulonefrite proliferativa mesangial\\r\\ndifusa\\r\\nN04.4 - Síndrome nefrótica - glomerulonefrite proliferativa\\r\\nendocapilar difusa\\r\\nN04.5 - Síndrome nefrótica - glomerulonefrite mesangiocapilar difusa\\r\\nN04.6 - Síndrome nefrótica - doença de depósito denso\\r\\nN04.7 - Síndrome nefrótica - glomerulonefrite difusa em crescente\\r\\nN04.8 - Síndrome nefrótica – outras\\r\\nN04.9 – Síndrome nefrótica – não especificada', '2025-08-13'),
(12, 'Ciclosporina 100 mg/ml solução oral', 'D59.0 - Anemia hemolítica auto-imune induzida por droga\\r\\nD59.1 - Outras anemias hemolíticas auto-imunes\\r\\nD60.1 – Aplasia pura de glóbulos vermelhos adquirida transitória\\r\\nD60. 8 – Outras aplasias puras adquiridas da série vermelha\\r\\nD61.0 — Anemia Aplástica Constitucional.\\r\\nD61.1 — Anemia Aplástica Induzida por Drogas.\\r\\nD61.2 — Anemia Aplástica Devida a Outros Agentes Externos.\\r\\nD 61.3 — Anemia Aplástica Idiopática.\\r\\nD 61.8 — Outras Anemias Aplásticas Especificadas.\\r\\nZ94.8 – Outros órgãos e tecidos transplantados\\r\\nG70.0 - Miastenia gravis\\r\\nG70.2 - Miastenia congênita e do desenvolvimento\\r\\nH20.1 — Iridociclite crônica\\r\\nH15.0 — Esclerite\\r\\nH30.1 - Inflamação corrorretiniana disseminada\\r\\nH30.2 - Ciclite posterior\\r\\nH30.8 - Outras inflamações coriorretinianas\\r\\nK51.0 - Enterocolite ulcerativa (crônica)\\r\\nK51.1 - Ileocolite ulcerativa (crônica)', '2025-05-31'),
(13, 'Clobazam 20 mg', 'G40.0 - Epilepsia e síndr. epilépt. idiop. def. por sua local. (focal - parcial)c/\\r\\ncrises de iníc. focal\\r\\nG40.1 - Epilepsia e síndr. epilépt. sintom. def. por sua local. (focal - parcial) c/\\r\\ncrises parciais simples\\r\\nG40.2 - Epilepsia e síndr. epilépt. sintom. def. por sua local. (focal - parcial) c/\\r\\ncrises parc. complexas\\r\\nG40.3 - Epilepsia e síndrome epiléptica generalizadas idiopáticas G40.4 -\\r\\nOutras epilepsias e síndromes epilépticas generalizadas G40.5 - Síndromes\\r\\nepilépticas especiais\\r\\nG40.6 - Crise de grande mal, não especificada (com ou sem pequeno mal)\\r\\nG40.7 - Pequeno mal não especificado, sem crises de grande mal', '2025-06-30');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `nivel_acesso` enum('Administrador','Farmaceutico') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `usuario`, `senha`, `nivel_acesso`) VALUES
(1, 'Administrador', 'admin', '$2y$10$A7B/yTv.gUoRFRyBNr5go.KtfuJW/gAs/3YKfIMHM3BjCuXrhFyqu', 'Administrador');

SELECT * FROM usuarios WHERE usuario = 'admin';

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `dispensacoes`
--
ALTER TABLE `dispensacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `medicamento_id` (`medicamento_id`);

--
-- Índices de tabela `medicamentos`
--
ALTER TABLE `medicamentos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `dispensacoes`
--
ALTER TABLE `dispensacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de tabela `medicamentos`
--
ALTER TABLE `medicamentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `dispensacoes`
--
ALTER TABLE `dispensacoes`
  ADD CONSTRAINT `fk_medicamento` FOREIGN KEY (`medicamento_id`) REFERENCES `medicamentos` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
