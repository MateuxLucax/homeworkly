<?php

$root = '../../../';

require_once $root.'utils/response-utils.php';

forbidMethodsNot('PUT');

require_once $root.'database/Query.php';
require_once $root.'dao/UsuarioDAO.php';
require_once $root.'models/TipoUsuario.php';

try
{
    UsuarioDAO::validaSessaoTipo(TipoUsuario::ADMINISTRADOR);

    $dados = readJsonRequestBody();

    $usuario = (new Usuario)
        ->setId($dados['id'])
        ->setNome($dados['nome'])
        ->setLogin($dados['login']);
    
    UsuarioDAO::alterar($usuario);

    respondJson(HttpCodes::OK);
}
catch (Exception $e)
{
    respondJson(HttpCodes::BAD_REQUEST, ['exception' => $e]);
}