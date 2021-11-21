<?php

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(403);
    die('{"mensagem": "Método não permitido"}');
}

try {
    $root = $_SERVER['DOCUMENT_ROOT'].'/..';

    $data = json_decode(file_get_contents('php://input'));
    $id = $data->id;

    require $root.'/database/Connection.php';
    require $root.'/database/Query.php';

    Query::execute('DELETE FROM usuario WHERE id_usuario = :id', [':id' => $id]);

    http_response_code(200);
    die('{"message": "Usuário de id '.$id.' excluído com sucesso"}');
} catch (Exception $e) {
    http_response_code(400);
    die(json_encode(['exception' => $e]));
}

