create type tipo_usuario as enum ('aluno', 'professor', 'administrador');

create table if not exists usuario (
      id_usuario    serial       primary key
    , tipo          tipo_usuario not null
    , nome          text         not null
    , login         text         not null unique
    , hash_senha    text         not null
    , cadastro      timestamp    not null default current_timestamp
    , ultimo_acesso timestamp
    , check (ultimo_acesso is null or ultimo_acesso >= cadastro)
);

create table if not exists turma (
      id_turma serial primary key
    , nome     text   not null
    , ano      int    not null
);

create table if not exists aluno_em_turma (
      id_aluno bigint references usuario
    , id_turma bigint references turma
    , primary key (id_aluno, id_turma)
    );

create table if not exists disciplina (
      id_disciplina serial primary key
    , id_turma      bigint references turma
    , nome          text   not null
);

create table if not exists professor_de_disciplina (
      id_professor  bigint references usuario
    , id_disciplina bigint references disciplina
    , primary key (id_professor, id_disciplina)
    );

create table if not exists tarefa (
      id_tarefa       serial    primary key
    , id_professor    bigint    references usuario
    , id_disciplina   bigint    references disciplina deferrable initially deferred
    /* A restrição de chave estrangeira do id_disciplina é deferida ao final
       da transação porque implementamos a atualização da turma
       deletando suas disciplinas depois recriando elas com o mesmo id
       (mais as novas e menos as que foram realmente excluídas):
       se não fizéssemos isso, ao deletar o SGBD ia reclamar caso
       a disciplina recriada tivesse alguma tarefa associada. */
    , titulo          text      not null
    , descricao       text      not null
    , esforco_minutos int       not null check (esforco_minutos > 0)
    , com_nota        boolean   not null
    , abertura        timestamp not null
    , entrega         timestamp not null
    , fechamento      timestamp
    , fechada         boolean   not null default false
    , check (entrega is null or entrega > abertura)
    , check (fechamento is null or entrega is null or fechamento > entrega)
    );

create table if not exists entrega (
      id_tarefa     bigint    references tarefa
    , id_aluno      bigint    references usuario
    , conteudo      text      not null
    , data_hora     timestamp not null
    , em_definitivo boolean
    , visto         boolean
    , nota          real      check (nota is null or (nota >= 0 and nota <= 10))
    , comentario    text
    , primary key (id_tarefa, id_aluno)
    );
