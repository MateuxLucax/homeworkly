<?php

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
}