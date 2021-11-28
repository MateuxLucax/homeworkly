<?php

$root = '../../../';

require_once $root.'utils/response-utils.php';

forbidMethodsNot('DELETE');

require_once $root.'/database/Connection.php';
require_once $root.'/database/Query.php';
require_once $root.'/controllers/UsuarioController.php';
require_once $root.'/models/TipoUsuario.php';

try
{
    UsuarioController::validaSessaoTipo(TipoUsuario::ADMINISTRADOR);

    $data = readRequestBody();
    $id = $data['id'];

    Query::execute('DELETE FROM usuario WHERE id_usuario = :id', [':id' => $id]);

    respond(HttpCodes::OK);
}
catch (Exception $e)
{
    respond(HttpCodes::BAD_REQUEST, ['exception' => $e]);
}

