-- A senha de todos é senha123 passado pelo PasswordUtil lá
insert into usuario (tipo, nome, login, hash_senha) values
  ('administrador', 'Lucas Moraes Schwambach', 'lucas',
   '$2y$10$0J2S3EgU8eixWf6STLwAuugD1ga8aFXr9YfN9pN4u8b7A2OnLzpj.')
, ('administrador', 'Mateus Lucas Cruz Brandt', 'mateus',
   '$2y$10$FGbDjNE.Lg/S5rAezIrw8OEOBZMItWumvl/it/yICBKxdiAGQNYae')
, ('aluno', 'Fulano da Silva', 'fulano',
   '$2y$10$9IYPzKVxW0P52vToQM2OE.4fxu49IyagZmgNFxn8kBYFUqViZcQGO')
, ('aluno', 'Beltrano de Oliveira', 'beltrano',
   '$2y$10$lx1sJTolZ7Znr938ZwKgpuSeQxpWY221H3HSZc7TZYLZy.FrJ8GYe')
, ('aluno', 'Sicrano de Souza', 'sicrano',
   '$2y$10$bLz8st1BtkhYBODLxU9v4uq0/fDyccGJ05lRmH6KXmKY1hnm7iJ/G')
, ('professor', 'John Doe', 'john.doe',
   '$2y$10$OLbwrTzoPAO.oJALIove5umq2PYdtlKusA5PEpvPt01BR1S9EwNUq')
, ('professor', 'Jane Doe', 'jane.doe',
   '$2y$10$CwysAF9RO2juurQzCfFEJ.SWx2522do5uSQflLJRxQzcxHArr8NP2')
, ('administrador', 'Asdf Qwerty', 'asdf',
   '$2y$10$MSUlaK2AWNzfgbiM1vymfOMRJymGANVWpQ7MJHHITHTxmq4ZGRunC');

insert into turma (nome, ano) values
  ('6Âº Ano 2020', 2020)
, ('7Âº Ano 2021', 2021)
, ('8Âº Ano 2021', 2021)
on conflict do nothing;

insert into aluno_em_turma (id_aluno, id_turma) values
  (3, 1)
, (4, 1)
, (3, 2)
, (4, 2)
, (5, 3)
on conflict do nothing;

insert into disciplina (id_turma, nome) values
  (1, 'MatemÃ¡tica')
, (2, 'MatemÃ¡tica')
, (2, 'HistÃ³ria')
, (3, 'MatemÃ¡tica')
, (3, 'Geografia')
on conflict do nothing;

insert into professor_de_disciplina (id_professor, id_disciplina) values
  (5, 1)
, (5, 2)
, (5, 4)
, (6, 2)
, (6, 5)
on conflict do nothing;

insert into tarefa (descricao, esforco_horas, com_nota, abertura, entrega, fechamento, fechada, id_professor, id_disciplina) values
  ('Realize as contas: 1+1, 2+2, 3+3, 5*7, 9*1, 0^0', 0.5, true, current_timestamp, current_timestamp + interval '1' day, current_timestamp + interval '3' day, false, 5, 1)
, ('Realize as contas: 2+2, 9+2+3, 9*9*9, e^i', 1, false, current_timestamp + interval '7' day, current_timestamp + interval '9' day, null, false, 5, 2)
on conflict do nothing;

insert into entrega (id_tarefa, id_aluno, visto, nota, data_hora, conteudo, comentario) values
  (1, 3, null, 8.0, current_timestamp + interval '3' hour, '2, 4, 6, 40, 9, 1', null)
, (1, 4, null, 0.0, current_timestamp + interval '2' day, '1, 2, 3, 4, 5, 6', 'O quÃª??')
, (1, 5, null, 10.0, current_timestamp, '2, 4, 6, 35, 9, indeterminado', 'ParabÃ©ns!')
on conflict do nothing;
