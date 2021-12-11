<?php

require_once $root.'/database/Connection.php';
require_once $root.'/models/Disciplina.php';
require_once $root.'/dao/UsuarioDAO.php';

class DisciplinaDAO
{
    public static function buscarDeTurma(Turma $turma): array
    {
        $rows = Query::select(
            'SELECT id_disciplina AS id, nome FROM disciplina WHERE id_turma = :idTurma',
            [':idTurma' => $turma->getId()]
        );

        return array_map(
            fn($row) => (new Disciplina)
                ->setTurma($turma)
                ->setId($row['id'])
                ->setNome($row['nome'])
                ->setProfessores(UsuarioDao::buscarProfessoresDeDisciplina($row['id'])),
            $rows
        );
    }
}