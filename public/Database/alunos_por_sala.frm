TYPE=VIEW
query=select `a`.`id` AS `aluno_id`,`a`.`nome` AS `aluno_nome`,`s`.`nome` AS `sala_nome`,`a`.`status` AS `status`,`a`.`horario_retirada` AS `horario_retirada`,`a`.`horario_devolucao` AS `horario_devolucao` from (`unlock_me`.`alunos` `a` left join `unlock_me`.`salas` `s` on(`a`.`sala_id` = `s`.`id`))
md5=4f54ddc728377d487028794bfd50a917
updatable=0
algorithm=0
definer_user=root
definer_host=localhost
suid=2
with_check_option=0
timestamp=2025-05-20 13:01:44
create-version=2
source=SELECT \n  a.id AS aluno_id,\n  a.nome AS aluno_nome,\n  s.nome AS sala_nome,\n  a.status,\n  a.horario_retirada,\n  a.horario_devolucao\nFROM alunos a\nLEFT JOIN salas s ON a.sala_id = s.id
client_cs_name=utf8mb4
connection_cl_name=utf8mb4_unicode_ci
view_body_utf8=select `a`.`id` AS `aluno_id`,`a`.`nome` AS `aluno_nome`,`s`.`nome` AS `sala_nome`,`a`.`status` AS `status`,`a`.`horario_retirada` AS `horario_retirada`,`a`.`horario_devolucao` AS `horario_devolucao` from (`unlock_me`.`alunos` `a` left join `unlock_me`.`salas` `s` on(`a`.`sala_id` = `s`.`id`))
mariadb-version=100424
