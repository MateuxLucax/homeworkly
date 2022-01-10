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
}