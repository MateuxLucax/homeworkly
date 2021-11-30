<?php

$root = '../../../';

require_once $root.'utils/response-utils.php';

require_once $root.'controllers/UsuarioController.php';
require_once $root.'models/TipoUsuario.php';

UsuarioController::validaSessaoTipo(TipoUsuario::ADMINISTRADOR);

require_once $root.'database/Query.php';
require_once $root.'utils/HttpCodes.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $data = readRequestBody();
    $ok = Query::execute('INSERT INTO turma (nome, ano) VALUES (:nome, :ano)', [
        ':nome' => $data['nome'],
        ':ano'  => $data['ano']
    ]);
    respond($ok ? HttpCodes::CREATED : HttpCodes::BAD_REQUEST);
}
else if ($_SERVER['REQUEST_METHOD'] == 'GET')
{
    $view['title'] = 'Criar turma';
    require_once $root.'views/turmas/criar.php';
    die();
}
else
{
    respond(HttpCodes::METHOD_NOT_ALLOWED);
}