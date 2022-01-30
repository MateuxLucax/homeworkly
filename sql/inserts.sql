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

insert into tarefa (titulo, descricao, esforco_minutos, com_nota, abertura, entrega, fechamento, fechada, id_professor, id_disciplina) values
  ('Contas', 'Realize as contas: 1+1, 2+2, 3+3, 5*7, 9*1, 0^0', 30, true, current_timestamp, current_timestamp + interval '1' day, current_timestamp + interval '3' day, false, 5, 1)
, ('Contas', 'Realize as contas: 2+2, 9+2+3, 9*9*9, e^i', 60, false, current_timestamp + interval '7' day, current_timestamp + interval '9' day, current_timestamp + interval '10' day, false, 5, 2)
, ('Cagar no mato', 'Cague no mato por 10h', 600, false, current_timestamp, current_timestamp + interval '5' day, current_timestamp + interval '7' day, false, 5, 2)
, ('Procurar aipim', 'procure aipim no mato', 30, false, current_timestamp, current_timestamp + interval '1' day, current_timestamp + interval '2' day, false, 5, 2)
, ('Bater em mendigos', 'procure mendigos para espancar', 3000, false, current_timestamp, current_timestamp + interval '30' day, current_timestamp + interval '45' day, false, 5, 2)
on conflict do nothing;

insert into entrega (id_tarefa, id_aluno, visto, nota, data_hora, conteudo, comentario, em_definitivo) values
  (1, 3, null, 8.0, current_timestamp + interval '3' hour, '2, 4, 6, 40, 9, 1', null, true)
, (1, 4, null, 0.0, current_timestamp + interval '2' day, '1, 2, 3, 4, 5, 6', 'O quê??', true)
, (1, 5, null, 10.0, current_timestamp, '2, 4, 6, 35, 9, indeterminado', 'Parabéns!', true)
on conflict do nothing;
