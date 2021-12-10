<?php

require_once $root.'/models/Turma.php';
require_once $root.'/database/Connection.php';
require_once $root.'/database/Query.php';
require_once $root.'/dao/DisciplinaDAO.php';

class TurmaDao
{
    public static function buscarTodas(): array
    {
        $rows = Query::select('SELECT id_turma AS id, nome, ano FROM turma');
        return array_map(
            fn($row) => (new Turma)
                ->setId($row['id'])
                ->setNome($row['nome'])
                ->setAno($row['ano']),
            $rows
        );
    }

    public static function buscar(int $id): Turma
    {
        $res = Query::select(
            'SELECT id_turma AS id, nome, ano FROM turma WHERE id_turma = :id',
            [':id' => $id]
        );

        if (count($res) == 0) {
            // TODO criar classe de exceção específica para esse tipo de erro (não existe registro com id dado)
            throw new Exception();
        }


        $res = $res[0];
        $turma = (new Turma)
            ->setId($res['id'])
            ->setNome($res['nome'])
            ->setAno($res['ano']);

        return $turma;
    }

    public static function popularComAlunos(Turma $turma): Turma
    {
        $turma->setAlunos(UsuarioDAO::buscarAlunosDeTurma($turma->getId()));
        return $turma;
    }

    public static function popularComDisciplinas(Turma $turma): Turma
    {
        $turma->setDisciplinas(DisciplinaDAO::buscarDeTurma($turma->getId()));
        foreach ($turma->getDisciplinas() as $d) $d->setTurma($turma);
        return $turma;
    }
}