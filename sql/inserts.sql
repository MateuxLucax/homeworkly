-- A senha de todos é senha123 passado pelo PasswordUtil
insert into usuario (tipo, nome, login, hash_senha) values
  ('administrador', 'Lucas Moraes Schwambach', 'lucas',
   '$2y$10$mGLt5GFyw/81isZBxgSa9.Zb2j97FzP1l.j.KuafKdOx3.Sg8Pe3O')
, ('administrador', 'Mateus Lucas Cruz Brandt', 'mateus',
   '$2y$10$mGLt5GFyw/81isZBxgSa9.Zb2j97FzP1l.j.KuafKdOx3.Sg8Pe3O')
, ('aluno', 'Fulano da Silva', 'fulano',
   '$2y$10$mGLt5GFyw/81isZBxgSa9.Zb2j97FzP1l.j.KuafKdOx3.Sg8Pe3O')
, ('aluno', 'Beltrano de Oliveira', 'beltrano',
   '$2y$10$mGLt5GFyw/81isZBxgSa9.Zb2j97FzP1l.j.KuafKdOx3.Sg8Pe3O')
, ('aluno', 'Sicrano de Souza', 'sicrano',
   '$2y$10$mGLt5GFyw/81isZBxgSa9.Zb2j97FzP1l.j.KuafKdOx3.Sg8Pe3O')
, ('aluno', 'Aeiou Aeiou', 'aeiou',
   '$2y$10$mGLt5GFyw/81isZBxgSa9.Zb2j97FzP1l.j.KuafKdOx3.Sg8Pe3O')
, ('professor', 'John Doe', 'john.doe',
   '$2y$10$mGLt5GFyw/81isZBxgSa9.Zb2j97FzP1l.j.KuafKdOx3.Sg8Pe3O')
, ('professor', 'Jane Doe', 'jane.doe',
   '$2y$10$mGLt5GFyw/81isZBxgSa9.Zb2j97FzP1l.j.KuafKdOx3.Sg8Pe3O')
, ('administrador', 'Asdf Qwerty', 'asdf',
   '$2y$10$mGLt5GFyw/81isZBxgSa9.Zb2j97FzP1l.j.KuafKdOx3.Sg8Pe3O');

insert into turma (nome, ano) values
  ('6º Ano 2021', 2021)
, ('7º Ano 2022', 2022)
, ('8º Ano 2022', 2022)
on conflict do nothing;

insert into aluno_em_turma (id_aluno, id_turma) values
  (3, 1)
, (4, 1)
, (3, 2)
, (4, 2)
, (5, 3)
, (6, 1)
, (5, 1)
on conflict do nothing;

insert into disciplina (id_turma, nome) values
  (1, 'Matemática')
, (2, 'Matemática')
, (2, 'História')
, (3, 'Matemática')
, (3, 'Geografia')
on conflict do nothing;

insert into professor_de_disciplina (id_professor, id_disciplina) values
  (7, 1)
, (7, 2)
, (7, 4)
, (8, 2)
, (8, 5)
on conflict do nothing;

insert into tarefa (titulo, descricao, esforco_minutos, com_nota, abertura, entrega, fechamento, id_professor, id_disciplina) values
  ('Contas', 'Realize as contas: 1+1, 2+2, 3+3, 5*7, 9*1, 0^0', 30, true, current_timestamp, current_timestamp + interval '1' day, current_timestamp + interval '3' day, 5, 1)
, ('Contas', 'Realize as contas: 2+2, 9+2+3, 9*9*9, e^i', 60, false, current_timestamp + interval '7' day, current_timestamp + interval '9' day, current_timestamp + interval '10' day, 5, 2)
, ('Nada', 'Fazer nada por 10h', 600, false, current_timestamp, current_timestamp + interval '5' day, current_timestamp + interval '7' day, 5, 2)
, ('Procurar aipim', 'procure aipim no mato', 30, false, current_timestamp, current_timestamp + interval '1' day, current_timestamp + interval '2' day, 5, 2)
, ('Catar carrapatos das costas do inimigo', 'procure carrapatos para espancar', 3000, false, current_timestamp, current_timestamp + interval '30' day, current_timestamp + interval '45' day, 5, 2)
on conflict do nothing;

insert into entrega (id_tarefa, id_aluno, data_hora, conteudo, em_definitivo) values
  (1, 3, current_timestamp + interval '3' hour, '2, 4, 6, 40, 9, 1', true)
, (1, 4, current_timestamp + interval '2' day, '1, 2, 3, 4, 5, 6', true)
, (1, 5, current_timestamp, '2, 4, 6, 35, 9, indeterminado', true)
on conflict do nothing;

insert into avaliacao (id_tarefa, id_aluno, visto, nota, comentario) values
  (1, 3, null, 8.0, null)
, (1, 4, null, 0.0, 'O quê??')
, (1, 5, null, 10.0, 'Parabéns')
on conflict do nothing;


-- para testar o cálculo de esforço

insert into tarefa (id_professor, id_disciplina, titulo, descricao, com_nota, abertura, entrega, fechamento, esforco_minutos) values
(7, 2, 'T1', '', false, current_timestamp, current_timestamp + interval '7 days', current_timestamp + interval '8 days', 60*3),
(7, 2, 'T2', '', false, current_timestamp - interval '3 day', current_timestamp + interval '7 days', current_timestamp + interval '8 days', 60*4),
(7, 2, 'T3', '', false, current_timestamp + interval '2 day', current_timestamp + interval '5 days', current_timestamp + interval '7 days', 60*2),
(7, 2, 'T4', '', false, current_timestamp + interval '2 day', current_timestamp + interval '20 days', current_timestamp + interval '21 days', 60*10),
(7, 2, 'T5', '', false, current_timestamp + interval '1 day', current_timestamp + interval '30 days', current_timestamp + interval '31 days', 60*1),
(7, 2, 'T6', '', false, current_timestamp + interval '15 day', current_timestamp + interval '16 days', current_timestamp + interval '17 days', 60*3);