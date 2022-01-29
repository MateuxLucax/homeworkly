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
    , em_definitivo boolean   default false
    , visto         boolean
    , nota          real      check (nota is null or (nota >= 0 and nota <= 10))
    , comentario    text
    , primary key (id_tarefa, id_aluno)
    );

create table if not exists historico_tarefa (
      id_historico_tarefa serial    primary key
    , id_tarefa           serial    references tarefa
        on delete cascade
        on update cascade
    , titulo              text      not null
    , descricao           text      not null
    , data_alteracao      timestamp not null
    );

create table if not exists historico_entrega (
      id_historico_entrega serial primary key
    , id_tarefa     bigint
    , id_aluno      bigint
    , conteudo      text      not null
    , data_alteracao      timestamp not null
    , foreign key (id_tarefa, id_aluno) references entrega (id_tarefa, id_aluno)
        on delete cascade
        on update cascade
);

create or replace function registra_historico_tarefa()
    returns trigger
    language plpgsql
    as $_$
        begin
            if (tg_op = 'UPDATE' or tg_op = 'INSERT') then
                insert into historico_tarefa (id_tarefa, titulo, descricao, data_alteracao) values (new.id_tarefa, new.titulo, new.descricao, now());
            end if;

            return null;
        end;
    $_$;

create trigger tr_registra_historico_tarefa
    after update or insert on tarefa
        for each row
            execute procedure registra_historico_tarefa();


create or replace function registra_historico_entrega()
    returns trigger
    language plpgsql
as $_$
begin
    if (tg_op = 'UPDATE' or tg_op = 'INSERT') then
        insert into historico_entrega (id_tarefa, id_aluno, conteudo, data_alteracao) values (new.id_tarefa, new.id_aluno, new.conteudo, now());
    end if;

    return null;
end;
$_$;

create trigger tr_registra_historico_entrega
    after update or insert on entrega
        for each row
            execute procedure registra_historico_entrega();
