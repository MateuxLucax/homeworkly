INSERT INTO turma VALUES (1, '6º Ano', 2022);

-- Senhas todas 123123123123
INSERT INTO usuario VALUES (1, 'administrador', 'Lucas Moraes Schwambach', 'lucas', '$2y$10$mGLt5GFyw/81isZBxgSa9.Zb2j97FzP1l.j.KuafKdOx3.Sg8Pe3O', '2022-02-05 20:37:46.330878', NULL);
INSERT INTO usuario VALUES (2, 'professor', 'Professor 1', 'prof1', '$2y$10$QkqTV.6Eljj0kVx1RnMdCOowIQXg1RuS8vmAsGrI3Vu0SMWOMXnu.', '2022-02-06 02:39:18.691276', NULL);
INSERT INTO usuario VALUES (3, 'professor', 'Professor 2', 'prof2', '$2y$10$HBV4I2Uu8NlC3Wc2rxL4xuPCG1A5MZ6va0L6d9Y1UJ05s5t6ELgrW', '2022-02-06 02:39:29.002763', NULL);
INSERT INTO usuario VALUES (5, 'professor', 'Professor de matemática', 'prof.mat', '$2y$10$vD.Tk0GG9sNgAnRmBKxbc.L/e1erBphzOu7.zsAdnqRSw6MkfWdKq', '2022-02-06 02:40:04.815501', NULL);
INSERT INTO usuario VALUES (6, 'professor', 'Prof Física', 'prof.fisica', '$2y$10$ex1aP2BiHOqIo8E.LmceHOIOkIvMOVbGvkF4JmJeULflxSLI50uiG', '2022-02-06 02:40:18.653048', NULL);
INSERT INTO usuario VALUES (7, 'aluno', 'João', 'joao', '$2y$10$zCKnub9brLTp6BUegjwclusQhsHDK63Ykd0qvh3oknqvla4bD2xLm', '2022-02-06 02:40:37.065691', NULL);
INSERT INTO usuario VALUES (8, 'aluno', 'José', 'jose', '$2y$10$Rgjqo0P77ChsvTzzP3mtjeLyM9X/GKu3boZVk7UoI1lNsOn9Hu1n6', '2022-02-06 02:40:45.579972', NULL);
INSERT INTO usuario VALUES (9, 'aluno', 'Fulano', 'fulano', '$2y$10$celLKxsdcj1uWRt5UoW5gO39fmCF.lww.K2FqXWsZNZr6PGy90Qza', '2022-02-06 02:40:56.021446', NULL);
INSERT INTO usuario VALUES (10, 'aluno', 'Sicrano', 'sicrano', '$2y$10$FOcQYEW5iCAUNfUoDHUF6eE77A6t90VS/obYgrMgJfHXtZ38VF4ZC', '2022-02-06 02:41:07.434192', NULL);
INSERT INTO usuario VALUES (11, 'aluno', 'Maria', 'maria', '$2y$10$9Bo8/m.nCy1SqXOXvG0UpOwWDOPMYG2YMn/LKxE4CgJ0lEuQB9G6G', '2022-02-06 02:41:19.508279', NULL);
INSERT INTO usuario VALUES (12, 'aluno', 'Jonas', 'jonas', '$2y$10$yPm2xiEYW7vxWdI63XBka.BYx1Yg.bRuWies9.vf3FxRLMOCNDhU6', '2022-02-06 02:41:28.996265', NULL);
INSERT INTO usuario VALUES (4, 'professor', 'Mario', 'mario', '$2y$10$iK9nTjv5xO74Kd3JzBJe0O6sIAxgz/oPxbAuNNbB5gyLM8nUUPb96', '2022-02-06 02:39:46.490772', NULL);
INSERT INTO usuario VALUES (13, 'aluno', 'Aluno 1', 'aluno1', '$2y$10$HaJV1CerZW2dyA6x0xe74e5MEoj5rqK.R1k0YudFT2U8hhNv2jXUW', '2022-02-06 02:42:05.642262', NULL);
INSERT INTO usuario VALUES (14, 'aluno', 'Aluno 2', 'aluno2', '$2y$10$BDAIEm9dbdQheDOlFZdiI.GNO0kc2GmMaKHgbOoYpsJTI2Pjtz8rW', '2022-02-06 02:42:35.532562', NULL);

INSERT INTO disciplina VALUES (1, 1, 'Matemática');
INSERT INTO disciplina VALUES (2, 1, 'Física');
INSERT INTO disciplina VALUES (3, 1, 'História e Geografia');
INSERT INTO disciplina VALUES (4, 1, 'Português');

INSERT INTO tarefa VALUES (1, 4, 3, 'Apresentação sobre segunda guerra mundial!', 'Conforme a divisão de grupos e tópicos feitas na sala, preparar uma apresentação a ser apresentada 31/12/2022
Todos os alunos do grupo devem enviar o link para a apresentação, pois o sistema não me deixa dar uma nota sem haver uma entrega
Ok?', 900, true, '2022-02-01 00:00:00', '2022-12-31 00:00:00', '2022-12-31 00:00:00');
INSERT INTO tarefa VALUES (3, 4, 3, 'Apresentação sobre primeira guerra mundial', '', 480, true, '2022-02-01 00:00:00', '2022-02-05 22:05:00', '2022-05-09 22:06:00');

INSERT INTO aluno_em_turma VALUES (7, 1);
INSERT INTO aluno_em_turma VALUES (8, 1);
INSERT INTO aluno_em_turma VALUES (9, 1);
INSERT INTO aluno_em_turma VALUES (10, 1);
INSERT INTO aluno_em_turma VALUES (11, 1);
INSERT INTO aluno_em_turma VALUES (12, 1);

INSERT INTO professor_de_disciplina VALUES (5, 1);
INSERT INTO professor_de_disciplina VALUES (6, 2);
INSERT INTO professor_de_disciplina VALUES (4, 3);
INSERT INTO professor_de_disciplina VALUES (3, 4);

INSERT INTO entrega VALUES (1, 7, 'google.com/apresentacao-slides-segunda-guerra', '2022-02-05 21:23:45', false, NULL, 9.5, 'asdasdsa');
INSERT INTO entrega VALUES (3, 7, 'google.com/apresentacao', '2022-02-05 21:57:43', true, NULL, 9.5, 'Muito boa a apresentação, parabéns!');