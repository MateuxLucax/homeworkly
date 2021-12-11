<?php

$root = '../../../';

require_once $root.'utils/response-utils.php';

forbidMethodsNot('DELETE');

require_once $root.'/database/Connection.php';
require_once $root.'/database/Query.php';
require_once $root.'/dao/UsuarioDAO.php';
require_once $root.'/models/TipoUsuario.php';

try
{
    UsuarioDAO::validaSessaoTipo(TipoUsuario::ADMINISTRADOR);

    $dados = readJsonRequestBody();

    $usuario = (new Usuario)->setId($dados['id']);
    UsuarioDAO::excluir($usuario);

    respondJson(HttpCodes::OK);
}
catch (Exception $e)
{
    respondJson(HttpCodes::BAD_REQUEST, ['exception' => $e]);
}

