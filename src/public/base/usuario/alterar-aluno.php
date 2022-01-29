<?php

$root = '../../../';

require_once $root.'utils/response-utils.php';

forbidMethodsNot('PUT');

require_once $root.'dao/UsuarioDAO.php';
require_once $root.'models/TipoUsuario.php';
require_once $root.'exceptions/UnauthorizedException.php';

try
{
    UsuarioDAO::validaSessaoTipo(TipoUsuario::ALUNO);

    $dados = readJsonRequestBody();

    if (!PasswordUtil::safe($dados['senha_nova'])) {
        respondJson(HttpCodes::BAD_REQUEST, ['mensagem' => 'Senha não é forte o suficiente']);
    }

    try {
        $usuario = (new Usuario)
            ->setId($dados['id'])
            ->setHashSenha($dados['senha_atual']);

        if (UsuarioDAO::validaSenha($usuario)) {
            $usuario = (new Usuario)
                ->setId($dados['id'])
                ->setHashSenha(PasswordUtil::hash($dados['senha_nova']));

            UsuarioDAO::alterarSenha($usuario);

            respondJson(HttpCodes::OK);
        }
    } catch (UnauthorizedException $exception) {
        respondJson(HttpCodes::UNAUTHORIZED, ['mensagem' => 'Senha incorreta!']);
    }
}
catch (Exception $e)
{
    respondJson(HttpCodes::BAD_REQUEST, ['exception' => $e]);
}