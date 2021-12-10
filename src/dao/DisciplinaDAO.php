<?php

require_once $root.'/database/Connection.php';
require_once $root.'/models/Disciplina.php';
require_once $root.'/dao/UsuarioDAO.php';

class DisciplinaDAO
{
    public static function buscarDeTurma(int $idTurma): array
    {
        $rows = Query::select(
            'SELECT id_disciplina AS id, nome FROM disciplina WHERE id_turma = :idTurma',
            [':idTurma' => $idTurma]
        );

        return array_map(
            fn($row) => (new Disciplina)
                ->setId($row['id'])
                ->setNome($row['nome'])
                ->setProfessores(UsuarioDao::buscarProfessoresDeDisciplina($row['id'])),
            $rows
        );
    }

    public static function criar(Disciplina $disciplina): Disciplina
    {
        $pdo = Connection::getInstance();
        $pdo->prepare('INSERT INTO disciplina (nome, id_turma) VALUES (:nome, :idTurma)')->execute([
            ':nome'   => $disciplina->getNome(),
            'idTurma' => $disciplina->getTurma()->getId()
        ]);
        $disciplina->setId($pdo->lastInsertId());

        foreach ($disciplina->getProfessores() as $professor) {
            $pdo->prepare('INSERT INTO professor_de_disciplina (id_professor, id_disciplina) VALUES (:idProf, :idDisc)')->execute([
                ':idProf' => $professor->getId(),
                ':idDisc' => $disciplina->getId()
            ]);
        }

        return $disciplina;
    }
}