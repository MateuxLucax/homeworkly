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
    $data = readRequestBody();

    try {
        $pdo->beginTransaction();

        $pdo->prepare('INSERT INTO turma (nome, ano) VALUES (:nome, :ano)')->execute([
            ':nome' => $data['nome'],
            ':ano'  => $data['ano']
        ]);

        $idTurma = $pdo->lastInsertId();

        $disciplinas = $data['disciplinas'];

        $sqlCriarDisciplinas =
        'INSERT INTO disciplina (nome, id_turma)
         VALUES '. join(',', array_fill(0, count($disciplinas), '(?, ?)'));

        $paramsCriarDisciplinas = [];
        foreach ($disciplinas as $disciplina) {
            $paramsCriarDisciplinas[] = $disciplina;
            $paramsCriarDisciplinas[] = $idTurma;
        }
        
        $pdo->prepare($sqlCriarDisciplinas)->execute($paramsCriarDisciplinas);

        $pdo->commit();
        respond(HttpCodes::CREATED, ['id' => $idTurma]);
    } catch(Exception $e) {
        $pdo->rollBack();
        respond(HttpCodes::BAD_REQUEST, ['exception' => $e]);
    }
}
else
{
    respond(HttpCodes::METHOD_NOT_ALLOWED);
}