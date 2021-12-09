<?php

$root = '../../../';

require_once $root.'utils/response-utils.php';

forbidMethodsNot('DELETE');

require_once $root.'/database/Connection.php';
require_once $root.'/database/Query.php';
require_once $root.'/controllers/UsuarioDAO.php';
require_once $root.'/models/TipoUsuario.php';

try
{
    UsuarioDAO::validaSessaoTipo(TipoUsuario::ADMINISTRADOR);

    $data = readJsonRequestBody();
    $id = $data['id'];

    Query::execute('DELETE FROM usuario WHERE id_usuario = :id', [':id' => $id]);

    respondJson(HttpCodes::OK);
}
catch (Exception $e)
{
    respondJson(HttpCodes::BAD_REQUEST, ['exception' => $e]);
}

