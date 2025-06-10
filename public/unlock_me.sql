-- Use the existing database
USE `unlock_me`;

-- Drop existing tables if they exist
DROP TABLE IF EXISTS `alunos`;
DROP TABLE IF EXISTS `professores`;
DROP TABLE IF EXISTS `salas`;

-- Create table: salas
CREATE TABLE `salas` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(100) NOT NULL,
  `capacidade` VARCHAR(1000) NOT NULL,
  `codigo` VARCHAR(6) NOT NULL UNIQUE,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nome_unique` (`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create table: professores
CREATE TABLE `professores` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(100) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `senha` VARCHAR(255) NOT NULL,
  `role` ENUM('professor') NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nome_unique` (`nome`),
  UNIQUE KEY `email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create table: alunos
CREATE TABLE `alunos` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(100) NOT NULL,
  `senha` VARCHAR(50) NOT NULL,
  `digital` VARCHAR(255), --NOT NULL,
  `status` ENUM('retirou','devolveu','nao pegou') NOT NULL,
  `horario_retirada` VARCHAR(255) DEFAULT NULL,
  `horario_devolucao` VARCHAR(255) DEFAULT NULL,
  `sala_id` INT(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `digital_unique` (`digital`),
  UNIQUE KEY `nome_unique` (`nome`),
  CONSTRAINT `fk_aluno_sala` FOREIGN KEY (`sala_id`) REFERENCES `salas`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert data into salas
INSERT INTO `salas` (`id`, `nome`, `capacidade`) VALUES
(1, 'Elon Musk', '30');

-- Insert data into professores
INSERT INTO `professores` (`id`, `nome`, `email`, `senha`, `role`) VALUES
(1, 'Abacate', 'abacate@pucpr.com', '123', 'professor');

-- Insert data into alunos
INSERT INTO `alunos` (`id`, `nome`, `digital`, `status`, `horario_retirada`, `horario_devolucao`, `sala_id`) VALUES
(1, 'Lucas Salomao Boschiroli', '01010101010', 'nao pegou', NULL, NULL, 1);

-- Trigger: Set horarios to NULL if status is 'nao pegou' (INSERT)
DELIMITER $$
CREATE TRIGGER `alunos_before_insert`
BEFORE INSERT ON `alunos`
FOR EACH ROW
BEGIN
  IF NEW.status = 'nao pegou' THEN
    SET NEW.horario_retirada = NULL;
    SET NEW.horario_devolucao = NULL;
  END IF;
END$$
DELIMITER ;

-- Trigger: Set horarios to NULL if status is 'nao pegou' (UPDATE)
DELIMITER $$
CREATE TRIGGER `alunos_before_update`
BEFORE UPDATE ON `alunos`
FOR EACH ROW
BEGIN
  IF NEW.status = 'nao pegou' THEN
    SET NEW.horario_retirada = NULL;
    SET NEW.horario_devolucao = NULL;
  END IF;
END$$
DELIMITER ;

-- Optional: Create a view to list alunos per sala
CREATE OR REPLACE VIEW `alunos_por_sala` AS
SELECT 
  a.id AS aluno_id,
  a.nome AS aluno_nome,
  s.nome AS sala_nome,
  a.status,
  a.horario_retirada,
  a.horario_devolucao
FROM alunos a
LEFT JOIN salas s ON a.sala_id = s.id;
