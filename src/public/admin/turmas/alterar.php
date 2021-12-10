<?php

$root = '../../..';

require_once $root.'/utils/response-utils.php';
require_once $root.'/dao/UsuarioDAO.php';
require_once $root.'/models/TipoUsuario.php';

UsuarioDAO::validaSessaoTipo(TipoUsuario::ADMINISTRADOR);

require_once $root.'/database/Connection.php';

$id   = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'GET')
{
    // TODO
}
else if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $data = readJsonRequestBody();

    $pdo = Connection::getInstance();

    try {
        $pdo->beginTransaction();

        $pdo->prepare('UPDATE turma SET nome = :nome, ano = :ano WHERE id_turma = :id')->execute([
            ':nome' => $data['nome']
            , ':ano'  => $data['ano']
            , ':id'   => $id
        ]);

        //
        // Atualizar alunos
        //

        $pdo->prepare('DELETE FROM aluno_em_turma WHERE id_turma = :id')->execute([':id' => $id]);

        foreach ($data['alunos'] as $idAluno) {
            $pdo->prepare('INSERT INTO aluno_em_turma (id_aluno, id_turma) VALUES (:idAluno, :idTurma)')->execute([
                ':idAluno' => $idAluno
                , ':idTurma' => $id
            ]);
        }

        //
        // Atualizar disciplinas
        //

        $pdo->prepare(
            'DELETE FROM professor_de_disciplina pd USING disciplina d
            WHERE pd.id_disciplina = d.id_disciplina AND d.id_turma = :id'
        )->execute([':id' => $id]);
        $pdo->prepare('DELETE FROM disciplina WHERE id_turma = :id')->execute([':id' => $id]);

        foreach ($data['disciplinas'] as $disciplina) {
            $pdo->prepare('INSERT INTO disciplina (nome, id_turma) VALUES (:nome, :id)')->execute([
                ':nome' => $disciplina['nome']
                , ':id'   => $id
            ]);
            $idDisciplina = $pdo->lastInsertId();
            foreach ($disciplina['professores'] as $idProfessor) {
                $pdo->prepare(
                    'INSERT INTO professor_de_disciplina (id_professor, id_disciplina) VALUES (:idProf, :idDisc)'
                )->execute([
                    ':idProf'  => $idProfessor
                    , ':idDisc' => $idDisciplina
                ]);
            }
        }

        $pdo->commit();
        respondJson(HttpCodes::OK);
    } catch (Exception $e) {
        $pdo->rollBack();
        respondJson(HttpCodes::BAD_REQUEST, ['exception' => $e]);
    }
}
else
{
    respondJson(HttpCodes::METHOD_NOT_ALLOWED);
}
