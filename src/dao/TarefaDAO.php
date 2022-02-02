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
                  , ta.fechada
               FROM tarefa ta
               JOIN usuario pro ON ta.id_professor = pro.id_usuario
               JOIN disciplina di ON ta.id_disciplina = di.id_disciplina
               JOIN turma tu ON di.id_turma = tu.id_turma
               WHERE id_tarefa = :id',
            ['id' => $id]
        );

        if (count($result) == 0) return null;

        $ta = $result[0];

        $tarefa = (new Tarefa)
            ->setId($ta['id_tarefa'])
            ->setTitulo($ta['titulo'])
            ->setDescricao($ta['descricao'])
            ->setEsforcoMinutos($ta['esforco_minutos'])
            ->setComNota($ta['com_nota'])
            ->setDataHoraAbertura(DateUtil::toLocalDateTime($ta['abertura']))
            ->setDataHoraEntrega(DateUtil::toLocalDateTime($ta['entrega']))
            ->setDataHoraFechamento($ta['fechamento'] ? DateUtil::toLocalDateTime($ta['fechamento']) : null)
            ->setFechadaManualmente($ta['fechada'])
            ->setProfessor((new Usuario)
                ->setId($ta['id_professor'])
                ->setNome($ta['nome_professor'])
                ->setTipo(TipoUsuario::PROFESSOR))
            ->setDisciplina((new Disciplina)
                ->setId($ta['id_disciplina'])
                ->setNome($ta['nome_disciplina'])
                ->setTurma((new Turma)
                    ->setId($ta['id_turma'])
                    ->setNome($ta['nome_turma'])
                    ->setAno($ta['turma_ano'])));

        return $tarefa;
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
                  , ta.fechada
               FROM tarefa ta
               JOIN usuario pro ON ta.id_professor = pro.id_usuario
               JOIN disciplina di ON ta.id_disciplina = di.id_disciplina
               JOIN turma tu ON di.id_turma = tu.id_turma
               JOIN aluno_em_turma aet ON aet.id_turma = tu.id_turma
              WHERE aet.id_aluno = :id_aluno
                AND tu.id_turma = :id_turma',
            ['id_aluno' => $idAluno, 'id_turma' => $idTurma]
        );

        return self::tarefaParaObjeto($rows);
    }

    public static function existe(int $idTarefa): bool
    {
        return Query::select(
            'SELECT EXISTS(SELECT 1 FROM tarefa WHERE id_tarefa = :id) as existe',
            ['id' => $idTarefa]
        )[0]['existe'];
    }

    public static function listarPorProfessor(int $idAluno, int $idTurma): array
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
                  , ta.fechada
               FROM tarefa ta
               JOIN usuario pro ON ta.id_professor = pro.id_usuario
               JOIN disciplina di ON ta.id_disciplina = di.id_disciplina
               JOIN turma tu ON di.id_turma = tu.id_turma
               JOIN professor_de_disciplina pdd on di.id_disciplina = pdd.id_disciplina
              WHERE pdd.id_professor = :id_professor
                AND tu.id_turma = :id_turma',
            ['id_professor' => $idAluno, 'id_turma' => $idTurma]
        );

        return self::tarefaParaObjeto($rows);
    }

    /**
     * @param bool|array $rows
     * @return array|Tarefa[]
     */
    public static function tarefaParaObjeto(bool|array $rows): array
    {
        return array_map(
            fn($row) => (new Tarefa)
                ->setId($row['id_tarefa'])
                ->setTitulo($row['titulo'])
                ->setDescricao($row['descricao'])
                ->setEsforcoMinutos($row['esforco_minutos'])
                ->setComNota($row['com_nota'])
                ->setDataHoraAbertura(DateUtil::toLocalDateTime($row['abertura']))
                ->setDataHoraEntrega(DateUtil::toLocalDateTime($row['entrega']))
                ->setDataHoraFechamento($row['fechamento'] ? DateUtil::toLocalDateTime($row['fechamento']) : null)
                ->setFechadaManualmente($row['fechada'])
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
                        ->setAno($row['turma_ano']))),
            $rows
        );
    }
}