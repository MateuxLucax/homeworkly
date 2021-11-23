<?php

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die('{"mensagem": "Método não permitido"}');
}

try {
    $root = '../../..';

    require_once $root.'/database/Connection.php';
    require_once $root.'/database/Query.php';
    require_once $root.'/controllers/UsuarioController.php';
    require_once $root.'/models/TipoUsuario.php';

    UsuarioController::validaSessaoTipo(TipoUsuario::ADMINISTRADOR);

    $data = json_decode(file_get_contents('php://input'));
    $id = $data->id;

    Query::execute('DELETE FROM usuario WHERE id_usuario = :id', [':id' => $id]);

    http_response_code(200);
    die('{"message": "Usuário de id '.$id.' excluído com sucesso"}');
} catch (Exception $e) {
    http_response_code(400);
    die(json_encode(['exception' => $e]));
}

