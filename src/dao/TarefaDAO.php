<?php

require_once $root . '/utils/DateUtil.php';
require_once $root . '/models/Tarefa.php';
require_once $root . '/models/Usuario.php';
require_once $root . '/models/TipoUsuario.php';
require_once $root . '/database/Connection.php';
require_once $root . '/database/Query.php';
require_once $root . '/dao/UsuarioDAO.php';
require_once $root . '/dao/DisciplinaDAO.php';

class TarefaDAO
{
    public static function buscar(int $id): ?Tarefa
    {
        $result = Query::select(
            'SELECT ta.id_tarefa
                  , pro.id_usuario AS id_professor
                  , pro.nome AS nome_professor
                  , di.id_disciplina
                  , di.nome AS nome_disciplina
                  , tu.id_turma
                  , tu.nome AS nome_turma
                  , tu.ano AS turma_ano
                  , ta.titulo
                  , ta.descricao
                  , ta.esforco_minutos
                  , ta.com_nota
                  , ta.abertura
                  , ta.entrega
                  , ta.fechamento
               FROM tarefa ta
               JOIN usuario pro ON ta.id_professor = pro.id_usuario
               JOIN disciplina di ON ta.id_disciplina = di.id_disciplina
               JOIN turma tu ON di.id_turma = tu.id_turma
               WHERE id_tarefa = :id',
            ['id' => $id]
        );

        if (count($result) == 0) return null;

        return self::toModel($result[0]);
    }

    public static function listarPorAluno(int $idAluno, int $idTurma): array
    {
        $rows = Query::select(
            'SELECT ta.id_tarefa
                  , pro.id_usuario AS id_professor
                  , pro.nome AS nome_professor
                  , di.id_disciplina
                  , di.nome AS nome_disciplina
                  , tu.id_turma
                  , tu.nome AS nome_turma
                  , tu.ano AS turma_ano
                  , ta.titulo
                  , ta.descricao
                  , ta.esforco_minutos
                  , ta.com_nota
                  , ta.abertura
                  , ta.entrega
                  , ta.fechamento
               FROM tarefa ta
               JOIN usuario pro ON ta.id_professor = pro.id_usuario
               JOIN disciplina di ON ta.id_disciplina = di.id_disciplina
               JOIN turma tu ON di.id_turma = tu.id_turma
               JOIN aluno_em_turma aet ON aet.id_turma = tu.id_turma
              WHERE aet.id_aluno = :id_aluno
                AND tu.id_turma = :id_turma
                AND CURRENT_DATE >= ta.abertura',
            ['id_aluno' => $idAluno, 'id_turma' => $idTurma]
        );

        return array_map(self::toModel(...), $rows);
    }

    public static function existe(int $idTarefa): bool
    {
        return Query::select(
            'SELECT EXISTS(SELECT 1 FROM tarefa WHERE id_tarefa = :id) as existe',
            ['id' => $idTarefa]
        )[0]['existe'];
    }

    public static function listarPorProfessor(int $idProfessor, int $idTurma): array
    {
        $rows = Query::select(
            'SELECT ta.id_tarefa
                  , pro.id_usuario AS id_professor
                  , pro.nome AS nome_professor
                  , di.id_disciplina
                  , di.nome AS nome_disciplina
                  , tu.id_turma
                  , tu.nome AS nome_turma
                  , tu.ano AS turma_ano
                  , ta.titulo
                  , ta.descricao
                  , ta.esforco_minutos
                  , ta.com_nota
                  , ta.abertura
                  , ta.entrega
                  , ta.fechamento
               FROM tarefa ta
               JOIN usuario pro ON ta.id_professor = pro.id_usuario
               JOIN disciplina di ON ta.id_disciplina = di.id_disciplina
               JOIN turma tu ON di.id_turma = tu.id_turma
               JOIN professor_de_disciplina pdd on di.id_disciplina = pdd.id_disciplina
              WHERE pdd.id_professor = :id_professor
                AND tu.id_turma = :id_turma',
            ['id_professor' => $idProfessor, 'id_turma' => $idTurma]
        );

        return array_map(self::toModel(...), $rows);
    }

    public static function buscarDeDisciplina(int $idDisciplina): array
    {
        $rows = Query::select(
            'SELECT ta.id_tarefa
                  , pro.id_usuario AS id_professor
                  , pro.nome AS nome_professor
                  , di.id_disciplina
                  , di.nome AS nome_disciplina
                  , tu.id_turma
                  , tu.nome AS nome_turma
                  , tu.ano AS turma_ano
                  , ta.titulo
                  , ta.descricao
                  , ta.esforco_minutos
                  , ta.com_nota
                  , ta.abertura
                  , ta.entrega
                  , ta.fechamento
               FROM tarefa ta
               JOIN disciplina di ON ta.id_disciplina = di.id_disciplina
               JOIN turma tu ON di.id_turma = tu.id_turma
               JOIN usuario pro ON ta.id_professor = pro.id_usuario
               JOIN professor_de_disciplina pdd on di.id_disciplina = pdd.id_disciplina
              WHERE ta.id_disciplina = :idDisciplina
           ORDER BY ta.entrega, ta.fechamento',
            [ 'idDisciplina' => $idDisciplina ]
        );

        return array_map(self::toModel(...), $rows);
    }

    public static function toModel(array $row): Tarefa
    {
        return (new Tarefa)
            ->setId($row['id_tarefa'])
            ->setTitulo($row['titulo'])
            ->setDescricao($row['descricao'])
            ->setEsforcoMinutos($row['esforco_minutos'])
            ->setComNota($row['com_nota'])
            ->setDataHoraAbertura(DateUtil::toLocalDateTime($row['abertura']))
            ->setDataHoraEntrega(DateUtil::toLocalDateTime($row['entrega']))
            ->setDataHoraFechamento(DateUtil::toLocalDateTime($row['fechamento']))
            ->setProfessor((new Usuario)
                ->setId($row['id_professor'])
                ->setNome($row['nome_professor'])
                ->setTipo(TipoUsuario::PROFESSOR))
            ->setDisciplina((new Disciplina)
                ->setId($row['id_disciplina'])
                ->setNome($row['nome_disciplina'])
                ->setTurma((new Turma)
                    ->setId($row['id_turma'])
                    ->setNome($row['nome_turma'])
                    ->setAno($row['turma_ano'])));
    }
}