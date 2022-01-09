<?php

require_once $root.'/models/Turma.php';
require_once $root.'/models/Disciplina.php';
require_once $root.'/database/Connection.php';
require_once $root.'/database/Query.php';
require_once $root.'/dao/DisciplinaDAO.php';

class TurmaDAO
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

    // TODO Tratar corretamente dos casos em que retorna null
    public static function buscar(int $id): ?Turma
    {
        $res = Query::select(
            'SELECT id_turma AS id, nome, ano FROM turma WHERE id_turma = :id',
            [':id' => $id]
        );

        if (count($res) == 0) {
            return null;
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
        return $turma->setAlunos(UsuarioDAO::buscarAlunosDeTurma($turma->getId()));
    }

    public static function popularComDisciplinas(Turma $turma): Turma
    {
        return $turma->setDisciplinas(DisciplinaDAO::buscarDeTurma($turma));
    }

    private static function associarAlunos(Turma $turma): Turma
    {
        $pdo = Connection::getInstance();

        foreach ($turma->getAlunos() as $aluno) {
            $pdo->prepare('INSERT INTO aluno_em_turma (id_aluno, id_turma) VALUES (:idAluno, :idTurma)')->execute([
                ':idAluno' => $aluno->getId(),
                ':idTurma' => $turma->getId()
            ]);
        }

        return $turma;
    }

    public static function criar(Turma $turma): Turma
    {
        $pdo = Connection::getInstance();
        $pdo->beginTransaction();
        try {
            $pdo->prepare('INSERT INTO turma (nome, ano) VALUES (:nome, :ano)')->execute([
                ':nome' => $turma->getNome(),
                ':ano'  => $turma->getAno()
            ]);
            $turma->setId($pdo->lastInsertId());

            self::associarAlunos($turma);

            //
            // Criar disciplinas
            //

            foreach ($turma->getDisciplinas() as $disciplina) {
                $pdo->prepare('INSERT INTO disciplina (id_turma, nome) VALUES (:idTurma, :nome)')->execute([
                    ':idTurma' => $disciplina->getTurma()->getId(),
                    ':nome'    => $disciplina->getNome()
                ]);
                $disciplina->setId($pdo->lastInsertId());

                foreach ($disciplina->getProfessores() as $professor) {
                    $pdo->prepare('INSERT INTO professor_de_disciplina (id_professor, id_disciplina) VALUES (:idProf, :idDisc)')->execute([
                        ':idProf' => $professor->getId(),
                        ':idDisc' => $disciplina->getId()
                    ]);
                }
            }

            $pdo->commit();
            return $turma;
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    private static function removerAlunos(Turma $turma): Turma
    {
        $pdo = Connection::getInstance();
        $pdo->prepare('DELETE FROM aluno_em_turma WHERE id_turma = :id')->execute([':id' => $turma->getId()]);
        return $turma;
    }

    private static function excluirDisciplinas(Turma $turma)
    {
        $pdo = Connection::getInstance();

        $pdo->prepare(
            'DELETE FROM professor_de_disciplina pd USING disciplina d
             WHERE pd.id_disciplina = d.id_disciplina AND d.id_turma = :id'
        )->execute([':id' => $turma->getId()]);

        $pdo->prepare('DELETE FROM disciplina WHERE id_turma = :id')->execute([':id' => $turma->getId()]);

        return $turma;
    }

    public static function excluir(Turma $turma): Turma
    {
        $pdo = Connection::getInstance();
        $pdo->beginTransaction();
        try {
            self::removerAlunos($turma);
            self::excluirDisciplinas($turma);
            $pdo->prepare('DELETE FROM turma WHERE id_turma = :id')->execute([':id' => $turma->getId()]);
            $pdo->commit();
            return $turma;
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function alterar(Turma $turma): Turma
    {
        $pdo = Connection::getInstance();
        $pdo->beginTransaction();

        try {
            $pdo->prepare('UPDATE turma SET nome = :nome, ano = :ano WHERE id_turma = :id')->execute([
                ':id'   => $turma->getId(),
                ':nome' => $turma->getNome(),
                ':ano'  => $turma->getAno()
            ]);

            self::removerAlunos($turma);
            self::associarAlunos($turma);

            self::excluirDisciplinas($turma);

            //
            // Criar (ou recriar) disciplinas
            //

            foreach ($turma->getDisciplinas() ?? [] as $disciplina) {
                $recriar = $disciplina->getId() != 0;
                $sql = $recriar
                     ? 'INSERT INTO disciplina (id_disciplina, id_turma, nome) VALUES (:id, :idTurma, :nome)'
                     : 'INSERT INTO disciplina (id_turma, nome) VALUES (:idTurma, :nome)';
                $params = [
                    ':idTurma' => $disciplina->getTurma()->getId(),
                    ':nome'    => $disciplina->getNome()
                ];
                if ($recriar) $params[':id'] = $disciplina->getId();

                $pdo->prepare($sql)->execute($params);

                if (!$recriar) $disciplina->setId($pdo->lastInsertId());

                foreach ($disciplina->getProfessores() as $professor) {
                    $pdo->prepare('INSERT INTO professor_de_disciplina (id_professor, id_disciplina) VALUES (:idProf, :idDisc)')->execute([
                        ':idProf' => $professor->getId(),
                        ':idDisc' => $disciplina->getId()
                    ]);
                }
            }

            $pdo->commit();
            return $turma;
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}