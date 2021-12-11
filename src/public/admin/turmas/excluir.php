<?php

$root = '../../../';

require_once $root.'utils/response-utils.php';

forbidMethodsNot('POST');

require_once $root.'dao/UsuarioDAO.php';
require_once $root.'dao/TurmaDAO.php';
require_once $root.'models/TipoUsuario.php';
require_once $root.'models/Turma.php';
require_once $root.'database/Query.php';
require_once $root.'utils/HttpCodes.php';

try
{
    UsuarioDAO::validaSessaoTipo(TipoUsuario::ADMINISTRADOR);

    $dados = readJsonRequestBody();

    $turma = (new Turma)->setId($dados['id']);

    $pdo = Connection::getInstance();
    $pdo->beginTransaction();
    TurmaDAO::excluir($turma);
    $pdo->commit();

    respondJson(HttpCodes::OK);
}
catch (Exception $e)
{
    $pdo->rollBack();
    respondJson(HttpCodes::BAD_REQUEST, ['exception' => $e]);
}
