-- A senha de todos � 123 passado pelo PasswordUtil
insert into usuario (tipo, nome, login, hash_senha) values
  ('administrador', 'Lucas Moraes Schwambach', 'lucas',
   '$2y$10$mekCC3F6vhnFz1kaYKFA0.n1vMZbYMnpeLeBAmEMsIbUttdJAcwEK')
, ('administrador', 'Mateus Lucas Cruz Brandt', 'mateus',
   '$2y$10$mekCC3F6vhnFz1kaYKFA0.n1vMZbYMnpeLeBAmEMsIbUttdJAcwEK')
, ('aluno', 'Fulano da Silva', 'fulano',
   '$2y$10$mekCC3F6vhnFz1kaYKFA0.n1vMZbYMnpeLeBAmEMsIbUttdJAcwEK')
, ('aluno', 'Beltrano de Oliveira', 'beltrano',
   '$2y$10$mekCC3F6vhnFz1kaYKFA0.n1vMZbYMnpeLeBAmEMsIbUttdJAcwEK')
, ('aluno', 'Sicrano de Souza', 'sicrano',
   '$2y$10$mekCC3F6vhnFz1kaYKFA0.n1vMZbYMnpeLeBAmEMsIbUttdJAcwEK')
, ('professor', 'John Doe', 'john.doe',
   '$2y$10$mekCC3F6vhnFz1kaYKFA0.n1vMZbYMnpeLeBAmEMsIbUttdJAcwEK')
, ('professor', 'Jane Doe', 'jane.doe',
   '$2y$10$mekCC3F6vhnFz1kaYKFA0.n1vMZbYMnpeLeBAmEMsIbUttdJAcwEK')
, ('administrador', 'Asdf Qwerty', 'asdf',
   '$2y$10$mekCC3F6vhnFz1kaYKFA0.n1vMZbYMnpeLeBAmEMsIbUttdJAcwEK');

insert into turma (nome, ano) values
  ('6� Ano 2020', 2020)
, ('7� Ano 2021', 2021)
, ('8� Ano 2021', 2021)
on conflict do nothing;

insert into aluno_em_turma (id_aluno, id_turma) values
  (3, 1)
, (4, 1)
, (3, 2)
, (4, 2)
, (5, 3)
on conflict do nothing;

insert into disciplina (id_turma, nome) values
  (1, 'Matem�tica')
, (2, 'Matem�tica')
, (2, 'Hist�ria')
, (3, 'Matem�tica')
, (3, 'Geografia')
on conflict do nothing;

insert into professor_de_disciplina (id_professor, id_disciplina) values
  (5, 1)
, (5, 2)
, (5, 4)
, (6, 2)
, (6, 5)
on conflict do nothing;

insert into tarefa (titulo, descricao, esforco_minutos, com_nota, abertura, entrega, fechamento, fechada, id_professor, id_disciplina) values
  ('Contas', 'Realize as contas: 1+1, 2+2, 3+3, 5*7, 9*1, 0^0', 30, true, current_timestamp, current_timestamp + interval '1' day, current_timestamp + interval '3' day, false, 5, 1)
, ('Contas', 'Realize as contas: 2+2, 9+2+3, 9*9*9, e^i', 60, false, current_timestamp + interval '7' day, current_timestamp + interval '9' day, null, false, 5, 2)
on conflict do nothing;

insert into entrega (id_tarefa, id_aluno, visto, nota, data_hora, conteudo, comentario) values
  (1, 3, null, 8.0, current_timestamp + interval '3' hour, '2, 4, 6, 40, 9, 1', null)
, (1, 4, null, 0.0, current_timestamp + interval '2' day, '1, 2, 3, 4, 5, 6', 'O qu�??')
, (1, 5, null, 10.0, current_timestamp, '2, 4, 6, 35, 9, indeterminado', 'Parab�ns!')
on conflict do nothing;
