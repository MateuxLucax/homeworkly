<?php

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    die('{"mensagem": "Método não permitido"}');
}

$root = '../../..';

require_once $root.'/database/Connection.php';
require_once $root.'/database/Query.php';
require_once $root.'/controllers/UsuarioController.php';
require_once $root.'/models/TipoUsuario.php';


try {
    UsuarioController::validaSessaoTipo(TipoUsuario::ADMINISTRADOR);

    $dados = json_decode(file_get_contents('php://input'));

    $editado = Query::execute(
        'UPDATE usuario SET nome = :nome, login = :login WHERE id_usuario = :id',
        [
            ':id'    => $dados->id,
            ':nome'  => $dados->nome,
            ':login' => $dados->login,
        ]
    );

    http_response_code($editado ? 200 : 400);
    die('{"editado": '.($editado ? 'true' : 'false').'}');
} catch (Exception $e) {
    http_response_code(400);
    die(json_encode(['exception' => $e]));
}