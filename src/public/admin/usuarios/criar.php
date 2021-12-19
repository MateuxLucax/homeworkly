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

    $dados = readJsonRequestBody();

    if (!PasswordUtil::safe($dados['senha'])) {
        respondJson(HttpCodes::BAD_REQUEST, ['mensagem' => 'Senha não é forte o suficiente (pelo menos 12 caracteres)']);
    }

    $usuario = (new Usuario)
        ->setNome($dados['nome'])
        ->setTipo($dados['tipo'])
        ->setHashSenha(PasswordUtil::hash($dados['senha']))
        ->setLogin($dados['login']);

    $registrado = UsuarioDAO::registrar($usuario);

    respondJson($registrado ? HttpCodes::CREATED : HttpCodes::BAD_REQUEST);
}
catch (Exception $e)
{
    respondJson(HttpCodes::BAD_REQUEST, ['exception' => $e]);
}