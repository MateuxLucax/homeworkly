<?php

$root = '../../../';

require_once $root.'utils/response-utils.php';

forbidMethodsNot('POST');

require_once $root.'dao/UsuarioDAO.php';
require_once $root.'models/TipoUsuario.php';
require_once $root.'models/Usuario.php';

try
{
    UsuarioDAO::validaSessaoTipo(TipoUsuario::ADMINISTRADOR);

    $data = readJsonRequestBody();

    $usuario = (new Usuario)
        ->setNome($data['nome'])
        ->setTipo($data['tipo'])
        ->setHashSenha($data['senha'])
        ->setLogin($data['login']);

    $registrado = UsuarioDAO::registrar($usuario);

    respondJson($registrado ? HttpCodes::CREATED : HttpCodes::BAD_REQUEST);
}
catch (Exception $e)
{
    respondJson(HttpCodes::BAD_REQUEST, ['exception' => $e]);
}