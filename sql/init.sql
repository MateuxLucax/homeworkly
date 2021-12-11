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
      id_tarefa     serial    primary key
    , id_professor  bigint    references usuario
    , id_disciplina bigint    references disciplina
    , descricao     text      not null
    , esforco_horas real      not null check (esforco_horas > 0)
    , com_nota      boolean   not null
    , abertura      timestamp not null
    , entrega       timestamp
    -- A tarefa pode ser fechada por data ou manualmente pelo professor
    -- tarefa fechada = data de fechamento já passou ou, se fechamento = null, fechada = true
    , fechamento    timestamp
    , fechada       boolean   not null default false
    , check (entrega is null or entrega > abertura)
    , check (fechamento is null or entrega is null or fechamento > entrega)
    );

create table if not exists entrega (
      id_entrega serial    primary key
    , id_tarefa  bigint    references tarefa
    , id_aluno   bigint    references usuario
    , visto      boolean
    , nota       real      check (nota is null or (nota >= 0 and nota <= 10))
    , data_hora  timestamp not null
    , conteudo   text      not null
    , comentario text
    );
