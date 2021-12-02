<?php

$root = '../../../';

require_once $root.'utils/response-utils.php';

forbidMethodsNot('PUT');

require_once $root.'database/Query.php';
require_once $root.'controllers/UsuarioController.php';
require_once $root.'models/TipoUsuario.php';

try
{
    UsuarioController::validaSessaoTipo(TipoUsuario::ADMINISTRADOR);

    $dados = readJsonRequestBody();

    $editado = Query::execute(
        'UPDATE usuario SET nome = :nome, login = :login WHERE id_usuario = :id',
        [
            ':id'    => $dados['id'],
            ':nome'  => $dados['nome'],
            ':login' => $dados['login']
        ]
    );

    respondJson($editado ? HttpCodes::OK : HttpCodes::BAD_REQUEST);
}
catch (Exception $e)
{
    respondJson(HttpCodes::BAD_REQUEST, ['exception' => $e]);
}