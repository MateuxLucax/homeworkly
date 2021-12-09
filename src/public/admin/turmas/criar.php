<?php

$root = '../../../';

require_once $root.'utils/response-utils.php';

require_once $root.'controllers/UsuarioController.php';
require_once $root.'models/TipoUsuario.php';

UsuarioController::validaSessaoTipo(TipoUsuario::ADMINISTRADOR);

require_once $root.'database/Connection.php';
require_once $root.'database/Query.php';
require_once $root.'utils/HttpCodes.php';

$pdo = Connection::getInstance();

if ($_SERVER['REQUEST_METHOD'] == 'GET')
{
    $view['title'] = 'Criar turma';
    require_once $root.'views/turmas/criar.php';
    die();
}
else if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $data = readJsonRequestBody();

    try {
        $pdo->beginTransaction();

        //
        // Criar turma
        //

        $pdo->prepare('INSERT INTO turma (nome, ano) VALUES (:nome, :ano)')->execute([
            ':nome' => $data['nome'],
            ':ano'  => $data['ano']
        ]);

        $idTurma = $pdo->lastInsertId();

        //
        // Criar disciplinas da turma, com professores
        //

        foreach ($data['disciplinas'] as $disciplina) {
            $pdo->prepare('INSERT INTO disciplina (nome, id_turma) VALUES (:nome, :idTurma)')->execute([
                ':nome'    => $disciplina['nome'],
                ':idTurma' => $idTurma
            ]);
            $idDisciplina = $pdo->lastInsertId();

            foreach ($disciplina['professores'] as $professor) {
                $pdo->prepare('INSERT INTO professor_de_disciplina (id_professor, id_disciplina) VALUES (:idProf, :idDisc)')->execute([
                    ':idProf' => $professor,
                    ':idDisc' => $idDisciplina
                ]);
            }
        }

        //
        // Associar alunos a turma
        //

        $alunos = $data['alunos'];

        if (count($alunos) > 0) {
            $sqlAssociarAlunos =
            'INSERT INTO aluno_em_turma (id_aluno, id_turma)
            VALUES '. join(',', array_fill(0, count($alunos), '(?,?)'));

            $paramsAssociarAlunos = [];
            foreach ($alunos as $aluno) {
                $paramsAssociarAlunos[] = $aluno;
                $paramsAssociarAlunos[] = $idTurma;
            }

            $pdo->prepare($sqlAssociarAlunos)->execute($paramsAssociarAlunos);
        }

        $pdo->commit();
        respondJson(HttpCodes::CREATED, ['id' => $idTurma]);
    } catch(Exception $e) {
        $pdo->rollBack();
        respondJson(HttpCodes::BAD_REQUEST, ['exception' => $e]);
    }
}
else
{
    respondJson(HttpCodes::METHOD_NOT_ALLOWED);
}