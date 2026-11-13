-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 13/11/2026 às 03:18
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `foco`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `alunos`
--

CREATE TABLE `alunos` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `matricula` varchar(9) DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `alunos`
--

INSERT INTO `alunos` (`id`, `nome`, `matricula`, `criado_em`) VALUES
(1, 'lucas', NULL, '2026-10-20 04:16:15'),
(3, 'Matheus Spindola', '202611859', '2026-10-21 03:21:59'),
(6, 'Matheu', '202688385', '2026-10-21 03:50:23'),
(7, 'Roberto', '202633351', '2026-10-21 04:16:57'),
(8, 'Jefferson', '202689947', '2026-10-21 19:03:56'),
(9, 'Pedro', '202645897', '2026-10-21 21:53:58'),
(12, 'Mathias', '202611111', '2026-10-21 22:45:40'),
(13, 'Jorge', '202678955', '2026-10-23 23:35:55');

-- --------------------------------------------------------

--
-- Estrutura para tabela `atividades`
--

CREATE TABLE `atividades` (
  `id` int(11) NOT NULL,
  `professor_id` int(11) NOT NULL,
  `materia` varchar(50) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `conteudo` text NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `atividades`
--

INSERT INTO `atividades` (`id`, `professor_id`, `materia`, `titulo`, `conteudo`, `criado_em`) VALUES
(2, 3, 'Matemática', 'Função do 2º grau', 'Matemática: Função do 2º grau\r\n\r\n1. Resumo da matéria\r\n\r\nA função do 2º grau é uma expressão matemática representada por:\r\n\\[\r\nf(x)=ax^2+bx+c\r\n\\]\r\n\r\nNela, \\(a\\), \\(b\\) e \\(c\\) são números reais, e \\(a\\) precisa ser diferente de zero.\r\n\r\nExemplo:\r\n\\[\r\nf(x)=x^2-5x+6\r\n\\]\r\n\r\nNesse caso:\r\n- \\(a=1\\)\r\n- \\(b=-5\\)\r\n- \\(c=6\\)\r\nComo calcular as raízes?\r\nAs raízes são os valores de \\(x\\) que fazem a função ser igual a zero. Para encontrá-las, usamos a fórmula de Bhaskara:\r\n\\[\r\nx=\\frac{-b\\pm\\sqrt{\\Delta}}{2a}\r\n\\]\r\n\r\nPrimeiro, calculamos o delta (\\(\\Delta\\)):\r\n\\[\r\n\\Delta=b^2-4ac\r\n\\]\r\n\r\nO valor do delta indica quantas raízes reais a função possui:\r\n- \\(\\Delta>0\\): duas raízes reais diferentes.\r\n- \\(\\Delta=0\\): uma raiz real (raiz dupla).\r\n- \\(\\Delta<0\\): nenhuma raiz real.\r\nExemplo resolvido\r\nVamos encontrar as raízes de:\r\n\\[\r\nx^2-5x+6=0\r\n\\]\r\n\r\n1º passo: identificar os coeficientes.\r\n\r\n\\(a=1,\\ b=-5,\\ c=6\\)\r\n\r\n2º passo: calcular o delta.\r\n\\[\r\n\\Delta=(-5)^2-4\\cdot1\\cdot6\r\n\\]\r\n\r\n\\[\r\n\\Delta=25-24=1\r\n\\]\r\n\r\n3º passo: aplicar Bhaskara.\r\n\\[\r\nx=\\frac{-(-5)\\pm\\sqrt{1}}{2\\cdot1}\r\n\\]\r\n\r\n\\[\r\nx=\\frac{5\\pm1}{2}\r\n\\]\r\n\r\nAssim, temos:\r\n\\[\r\nx_1=\\frac{5+1}{2}=3\r\n\\]\r\n\r\n\\[\r\nx_2=\\frac{5-1}{2}=2\r\n\\]\r\n\r\nResposta: as raízes são \\(x=2\\) e \\(x=3\\).', '2026-10-21 03:48:20'),
(4, 6, 'Inglês', 'Verbo to be', 'verbar o to be e dale nos guri', '2026-10-21 22:53:39'),
(6, 7, 'Ciências', 'big bang', 'aaaaa', '2026-10-23 22:27:02'),
(7, 8, 'História', 'Era Vargas', 'Erro na IA. Verifique sua chave ou tente mais tarde.', '2026-10-23 23:22:22'),
(9, 8, 'História', 'Era Industrial', 'Aqui está o resumo direto e em três pontos rápidos:\n\nA **Era Industrial** começou na **Inglaterra** por volta de 1760 e mudou o mundo. A grande mudança foi trocar o trabalho manual e artesanal pelo uso de **máquinas nas fábricas**. \n\nA principal invenção da época foi a **máquina a vapor**, que acelerou a produção de mercadorias e melhorou os transportes. Por causa disso, milhares de pessoas **saíram do campo para viver e trabalhar nas cidades**.\n\nPor outro lado, a vida dos trabalhadores era muito difícil, com **salários baixos, jornadas cansativas e trabalho infantil**. Ainda assim, essa época transformou para sempre a tecnologia e a economia, criando a base do nosso **mundo moderno**.', '2026-10-23 23:59:55'),
(10, 8, 'História', 'Era Industrial', '🚨 O Google recusou: This model is currently experiencing high demand. Spikes in demand are usually temporary. Please try again later.', '2026-10-24 00:04:02'),
(11, 9, 'Geografia', 'Climas do Brasil', 'Puxa, o meu cérebro de robô deu uma engasgada agora. Tente clicar em \'Nova explicação\' mais uma vez! 🤖', '2026-10-30 22:55:39'),
(12, 3, 'Matemática', 'soma básica', 'uiuiuiuuiu', '2026-10-30 23:53:58');

-- --------------------------------------------------------

--
-- Estrutura para tabela `perguntas`
--

CREATE TABLE `perguntas` (
  `id` int(11) NOT NULL,
  `atividade_id` int(11) NOT NULL,
  `enunciado` text NOT NULL,
  `alternativas` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`alternativas`)),
  `correta` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `perguntas`
--

INSERT INTO `perguntas` (`id`, `atividade_id`, `enunciado`, `alternativas`, `correta`) VALUES
(2, 2, 'Qual é o coeficiente a da função f(x) = 3x² + 2x − 7?', '[\"2\",\"3\",\"-7\",\"-2\"]', 1),
(3, 2, 'Qual é o valor de Δ na equação x² − 4x + 3 = 0?', '[\"4\",\"7\",\"16\",\"-4\"]', 0),
(4, 2, 'Quantas raízes reais tem uma função quando Δ < 0?', '[\"Duas raízes diferentes\",\"Uma raiz real\",\"Nenhuma raiz real\",\"Três raízes reais\"]', 2),
(5, 2, 'Quais são as raízes da equação x² − 5x + 6 = 0?', '[\"1 e 6\",\"−2 e −3\",\"2 e 3\",\"−2 e 3\"]', 2),
(6, 2, 'Qual é o valor de Δ na equação x² + 2x + 1 = 0?', '[\"0\",\"1\",\"4\",\"-4\"]', 0),
(8, 4, 'She____ my best friend', '[\"am\",\"is\",\"are\",\"be\"]', 1),
(10, 6, 'o que é o big bang', '[\"hjfjg\",\"gghh\",\"yyyy\",\"kkkk\"]', 3),
(11, 7, 'Qual foi uma das principais medidas trabalhistas implementadas durante a Era Vargas?', '[\"A criação do voto censitário\",\"A criação da Consolidação das Leis do Trabalho (CLT)\",\"em 1943\",\"A abolição da indústria nacional\",\"O fim dos direitos dos trabalhadores urbanos.\"]', 1),
(12, 7, 'O que caracterizou o Estado Novo, iniciado em 1937?', '[\"A ampliação da democracia e das eleições diretas\",\"A descentralização do poder político\",\"A implantação de uma ditadura\",\"com censura e perseguição aos opositores\",\"A saída do Brasil da Segunda Guerra Mundial.\"]', 2),
(14, 9, 'Qual foi uma das principais mudanças provocadas pela Era Industrial?', '[\"A diminuição do uso de máquinas nas fábricas\",\"A substituição do trabalho artesanal pela produção com máquinas\",\"O fim do comércio entre os países\",\"O retorno da população das cidades para o campo.\"]', 1),
(15, 10, 'Qual foi uma das principais mudanças provocadas pela Era Industrial?', '[\"A diminuição do uso de máquinas nas fábricas\",\"A substituição do trabalho artesanal pela produção com máquinas\",\"O fim do comércio entre os países\",\"O retorno da população das cidades para o campo.\"]', 1),
(16, 11, 'Qual tipo de clima é caracterizado por temperaturas elevadas e poucas chuvas durante grande parte do ano?', '[\"Equatorial\",\"Subtropical\",\"Semiárido\",\"Polar\"]', 2),
(17, 12, '3 + 4', '[\"2\",\"3\",\"7\",\"6\"]', 2);

-- --------------------------------------------------------

--
-- Estrutura para tabela `professores`
--

CREATE TABLE `professores` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `professores`
--

INSERT INTO `professores` (`id`, `nome`, `senha`, `criado_em`) VALUES
(1, 'Prof.Demo', 'foco123', '2026-10-08 03:30:12'),
(2, 'Professor.Hoffmann', 'Foco123', '2026-10-21 03:20:54'),
(3, 'Jefferson', 'Foco123', '2026-10-21 03:31:04'),
(4, 'Prof.Ana', 'Foco123', '2026-10-21 19:03:13'),
(5, 'Gustavo', 'Foco123', '2026-10-21 21:52:37'),
(6, 'Vanessa', 'Foco123', '2026-10-21 22:49:41'),
(7, 'carol', 'Foco123', '2026-10-23 22:24:38'),
(8, 'Amanda', 'Foco123', '2026-10-23 23:17:19'),
(9, 'Kleber', 'Foco123', '2026-10-30 22:50:27'),
(10, 'Fernanda', 'Foco123', '2026-11-04 22:46:58');

-- --------------------------------------------------------

--
-- Estrutura para tabela `progresso`
--

CREATE TABLE `progresso` (
  `id` int(11) NOT NULL,
  `aluno_id` int(11) NOT NULL,
  `atividade_id` int(11) NOT NULL,
  `acertos` int(11) NOT NULL,
  `total_perguntas` int(11) NOT NULL,
  `concluido_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `progresso`
--

INSERT INTO `progresso` (`id`, `aluno_id`, `atividade_id`, `acertos`, `total_perguntas`, `concluido_em`) VALUES
(3, 6, 2, 2, 5, '2026-10-21 03:52:17'),
(5, 3, 2, 2, 5, '2026-10-21 04:12:25'),
(6, 8, 2, 4, 5, '2026-10-21 19:05:53'),
(7, 9, 2, 2, 5, '2026-10-21 21:56:09'),
(9, 12, 2, 2, 5, '2026-10-21 22:48:28'),
(11, 12, 4, 1, 1, '2026-10-21 22:58:34'),
(13, 12, 6, 1, 1, '2026-10-23 22:27:40'),
(14, 12, 12, 1, 1, '2026-10-30 23:54:47');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `nome` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `alunos`
--
ALTER TABLE `alunos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome` (`nome`),
  ADD UNIQUE KEY `matricula` (`matricula`);

--
-- Índices de tabela `atividades`
--
ALTER TABLE `atividades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `professor_id` (`professor_id`);

--
-- Índices de tabela `perguntas`
--
ALTER TABLE `perguntas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `atividade_id` (`atividade_id`);

--
-- Índices de tabela `professores`
--
ALTER TABLE `professores`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `progresso`
--
ALTER TABLE `progresso`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unico_aluno_atividade` (`aluno_id`,`atividade_id`),
  ADD KEY `atividade_id` (`atividade_id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `alunos`
--
ALTER TABLE `alunos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de tabela `atividades`
--
ALTER TABLE `atividades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `perguntas`
--
ALTER TABLE `perguntas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de tabela `professores`
--
ALTER TABLE `professores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `progresso`
--
ALTER TABLE `progresso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `atividades`
--
ALTER TABLE `atividades`
  ADD CONSTRAINT `atividades_ibfk_1` FOREIGN KEY (`professor_id`) REFERENCES `professores` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `perguntas`
--
ALTER TABLE `perguntas`
  ADD CONSTRAINT `perguntas_ibfk_1` FOREIGN KEY (`atividade_id`) REFERENCES `atividades` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `progresso`
--
ALTER TABLE `progresso`
  ADD CONSTRAINT `progresso_ibfk_1` FOREIGN KEY (`aluno_id`) REFERENCES `alunos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `progresso_ibfk_2` FOREIGN KEY (`atividade_id`) REFERENCES `atividades` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
