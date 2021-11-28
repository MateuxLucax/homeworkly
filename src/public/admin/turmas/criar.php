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

    $data = readRequestBody();
    $ok = Query::execute('INSERT INTO turma (nome, ano) VALUES (:nome, :ano)', [
        ':nome' => $data['nome'],
        ':ano'  => $data['ano']
    ]);
    respond($ok ? HttpCodes::CREATED : HttpCodes::BAD_REQUEST);
}
catch (Exception $e)
{
    respond(HttpCodes::BAD_REQUEST, ['exception' => $e]);
}
