<?php

$root = '../../../';

require_once $root.'utils/response-utils.php';

forbidMethodsNot('POST');

require_once $root.'controllers/UsuarioController.php';
require_once $root.'models/TipoUsuario.php';
require_once $root.'models/Usuario.php';

try
{
    UsuarioController::validaSessaoTipo(TipoUsuario::ADMINISTRADOR);

    $data = readRequestBody();

    $usuario = new Usuario();
    $usuario->setNome($data['nome']);
    $usuario->setTipo($data['tipo']);
    $usuario->setHashSenha($data['senha']);
    $usuario->setLogin($data['login']);

    $registrado = UsuarioController::registrar($usuario);

    respond($registrado ? HttpCodes::CREATED : HttpCodes::BAD_REQUEST);
}
catch (Exception $e)
{
    respond(HttpCodes::BAD_REQUEST, ['exception' => $e]);
}