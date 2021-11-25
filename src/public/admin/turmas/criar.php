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

    $ok = Query::execute('INSERT INTO turma (nome, ano) VALUES (:nome, :ano)', [
        ':nome' => $data['nome'],
        ':ano'  => $data['ano']
    ]);

    http_response_code($ok ? 201 : 400);
    die('{"registrado": '. $ok ? 'true' : 'false' .'}');
} catch (Exception $e) {
    http_response_code(400);
    die(json_encode(['exception' => $e]));
}
