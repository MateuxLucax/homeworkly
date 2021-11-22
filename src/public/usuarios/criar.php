<?php

header('Content-Type: application/json; charset=utf-8');

if (!($_SERVER['REQUEST_METHOD'] === 'POST')) {
    http_response_code(405);
    die(json_encode(["erro" => "metodo nao permitido"]));
}

try {
    $root = $_SERVER['DOCUMENT_ROOT'] . '/../';

    require_once $root. 'models/Usuario.php';
    require_once $root. 'controllers/UsuarioController.php';

    $data = json_decode(file_get_contents('php://input'), true);

    $usuario = new Usuario();
    $usuario->setNome($data['nome']);
    $usuario->setTipo($data['tipo']);
    $usuario->setHashSenha($data['senha']);
    $usuario->setLogin($data['login']);

    $registrado = UsuarioController::registrar($usuario);

    http_response_code($registrado ? 201 : 400);
    die(json_encode(["registrado" => $registrado]));
} catch (Exception $e) {
    http_response_code(400);
    die(json_encode(['exception' => $e]));
}