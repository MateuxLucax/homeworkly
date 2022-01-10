<?php

$root = '../../../';

require_once $root.'utils/response-utils.php';

forbidMethodsNot('PUT');

require_once $root.'dao/UsuarioDAO.php';
require_once $root.'models/TipoUsuario.php';

try
{
    UsuarioDAO::validaSessaoTipo(TipoUsuario::ADMINISTRADOR);

    $dados = readJsonRequestBody();

    if (!PasswordUtil::safe($dados['senha'])) {
        respondJson(HttpCodes::BAD_REQUEST, ['mensagem' => 'Senha não é forte o suficiente']);
    }

    $usuario = (new Usuario)
        ->setId($dados['id'])
        ->setHashSenha(PasswordUtil::hash($dados['senha']));

    UsuarioDAO::alterarSenha($usuario);

    respondJson(HttpCodes::OK);
}
catch (Exception $e)
{
    respondJson(HttpCodes::BAD_REQUEST, ['exception' => $e]);
}