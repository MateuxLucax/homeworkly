<?php

require_once $root.'/database/Connection.php';
require_once $root.'/models/Disciplina.php';
require_once $root.'/dao/UsuarioDAO.php';

class DisciplinaDAO
{
    public static function buscarDeTurma(Turma $turma): array
    {
        $rows = Query::select(
            'SELECT d.id_disciplina AS id, d.nome,
                    not exists(select 1 from tarefa t where t.id_disciplina = d.id_disciplina) as pode_excluir
               FROM disciplina d
              WHERE d.id_turma = :idTurma',
            [':idTurma' => $turma->getId()]
        );

        return array_map(
            fn($row) => (new Disciplina)
                ->setTurma($turma)
                ->setId($row['id'])
                ->setNome($row['nome'])
                ->setPodeExcluir($row['pode_excluir'])
                ->setProfessores(UsuarioDAO::buscarProfessoresDeDisciplina($row['id'])),
            $rows
        );
    }

    public static function buscar(int $id): ?Disciplina
    {
        $result = Query::select(
            'SELECT d.nome
                  , t.id_turma
                  , t.nome AS nome_turma
                  , t.ano
               FROM disciplina d
               JOIN turma t ON d.id_turma = t.id_turma
              WHERE d.id_disciplina = :id',
            [':id' => $id]
        );
        if (count($result) == 0) return null;
        $d = $result[0];

        return (new Disciplina)
            ->setId($id)
            ->setNome($d['nome'])
            ->setTurma((new Turma)
                ->setId($d['id_turma'])
                ->setNome($d['nome_turma'])
                ->setAno($d['ano']));
    }

    public static function popularComProfessores(Disciplina $disciplina): Disciplina
    {
        return $disciplina->setProfessores(UsuarioDAO::buscarProfessoresDeDisciplina($disciplina->getId()));
    }

    public static function disciplinasDeTurma(int $idAluno, int $idTurma): array {
        $result = Query::select(
            'SELECT
                    d.id_disciplina,
                    d.nome,
                    count(DISTINCT(ta.id_tarefa)) as tarefas,
                    avg(a.nota) as nota_media
                FROM
                    disciplina d
                INNER JOIN turma t ON
                    d.id_turma = t.id_turma
                    AND t.id_turma = :id_turma
                INNER JOIN tarefa ta ON 
                    ta.id_disciplina  = d.id_disciplina
                INNER JOIN aluno_em_turma aet ON
                    aet.id_turma = aet.id_turma
                    AND aet.id_aluno = :id_aluno
                LEFT JOIN avaliacao a ON 
                    a.id_aluno = aet.id_aluno 
                GROUP BY 1, 2', 
            ['id_turma' => $idTurma, 'id_aluno' => $idAluno]
        );

        return array_map(
            fn ($row) => [
                'disciplina' => $row['nome'],
                'disciplina_id' => $row['id_disciplina'],
                'professores' => UsuarioDAO::buscarProfessoresDeDisciplina($row['id_disciplina']),
                'tarefas' => $row['tarefas'],
                'nota_media' => $row['nota_media']
            ],
            $result
        );
    }

    public static function disciplinaDeTurmaProfessor(int $idProfessor, int $idTurma): array
    {
        $result = Query::select(
            'SELECT
                    d.id_disciplina,
                    d.nome as nome_disciplina,
                    t.nome as nome_turma
                FROM
                    disciplina d
                JOIN professor_de_disciplina pdd ON
                    d.id_disciplina = pdd.id_disciplina
                JOIN turma t ON
                    d.id_turma = t.id_turma
                WHERE
                    pdd.id_professor = :id_professor
                AND t.id_turma = :id_turma',
            ['id_turma' => $idTurma, 'id_professor' => $idProfessor]
        );

        return array_map(
            fn ($row) => (new Disciplina)
                ->setId($row['id_disciplina'])
                ->setNome($row['nome_disciplina'])
                ->setTurma((new Turma)->setId($idTurma)->setNome($row['nome_turma'])),
            $result
        );
    }
}