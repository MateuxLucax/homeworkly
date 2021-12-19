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

    $usuario = (new Usuario)
        ->setId($dados['id'])
        ->setHashSenha(PasswordUtil::hash($dados['senha']));

    // TODO validar segurança da senha
    // tanto no front-end qto no back-end
    
    UsuarioDao::alterarSenha($usuario);

    respondJson(HttpCodes::OK);
}
catch (Exception $e)
{
    respondJson(HttpCodes::BAD_REQUEST, ['exception' => $e]);
}