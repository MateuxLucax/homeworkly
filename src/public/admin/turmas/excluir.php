<?php

$root = '../../../';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' ) {
    http_response_code(405);
    die('{"erro": "Método não permitido"}');
}

try {
    require_once $root.'controllers/UsuarioController.php';
    require_once $root.'models/TipoUsuario.php';

    UsuarioController::validaSessaoTipo(TipoUsuario::ADMINISTRADOR);

    $data = json_decode(file_get_contents('php://input'), true);

    require_once $root.'database/Connection.php';
    require_once $root.'database/Query.php';

    $ok = Query::execute('DELETE FROM turma WHERE id_turma = :id', [
        ':id' => $data['id'],
    ]);

    http_response_code($ok ? 200 : 400);
    die('{"excluido": '. ($ok ? 'true' : 'false') .'}');
} catch (Exception $e) {
    http_response_code(400);
    die(json_encode(['exception' => $e]));
}
