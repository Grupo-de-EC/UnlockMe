TYPE=TRIGGERS
triggers='CREATE DEFINER=`root`@`localhost` TRIGGER `alunos_before_insert`\nBEFORE INSERT ON `alunos`\nFOR EACH ROW\nBEGIN\n  IF NEW.status = \'nao pegou\' THEN\n    SET NEW.horario_retirada = NULL;\n    SET NEW.horario_devolucao = NULL;\n  END IF;\nEND' 'CREATE DEFINER=`root`@`localhost` TRIGGER `alunos_before_update`\nBEFORE UPDATE ON `alunos`\nFOR EACH ROW\nBEGIN\n  IF NEW.status = \'nao pegou\' THEN\n    SET NEW.horario_retirada = NULL;\n    SET NEW.horario_devolucao = NULL;\n  END IF;\nEND'
sql_modes=1098907648 1098907648
definers='root@localhost' 'root@localhost'
client_cs_names='utf8mb4' 'utf8mb4'
connection_cl_names='utf8mb4_unicode_ci' 'utf8mb4_unicode_ci'
db_cl_names='utf8mb4_general_ci' 'utf8mb4_general_ci'
created=174774610401 174774610402
