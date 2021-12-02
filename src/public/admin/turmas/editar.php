<?php

$root = '../../../';

require_once $root.'utils/response-utils.php';

forbidMethodsNot('POST');

require_once $root.'controllers/UsuarioController.php';
require_once $root.'models/TipoUsuario.php';
require_once $root.'database/Query.php';
require_once $root.'utils/HttpCodes.php';

try
{
    UsuarioController::validaSessaoTipo(TipoUsuario::ADMINISTRADOR);

    $data = readJsonRequestBody();
    $ok = Query::execute('UPDATE turma SET nome = :nome, ano = :ano WHERE id_turma = :id', [
        ':id'   => $data['id'],
        ':nome' => $data['nome'],
        ':ano'  => $data['ano']
    ]);
    respondJson($ok ? HttpCodes::OK : HttpCodes::BAD_REQUEST);
}
catch (Exception $e)
{
    respondJson(HttpCodes::BAD_REQUEST, ['exception' => $e]);
}
