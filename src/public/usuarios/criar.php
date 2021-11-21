<?php

if (!($_SERVER['REQUEST_METHOD'] === 'POST')) {
    die(json_encode(["erro" => "metodo nao permitido"]));
}

$root = $_SERVER['DOCUMENT_ROOT'] . '/../';

require_once $root. 'models/Usuario.php';
require_once $root. 'controllers/UsuarioController.php';

$data = json_decode(file_get_contents('php://input'), true);

$usuario = new Usuario();
$usuario->setNome($data['nome']);
$usuario->setTipo($data['tipo']);
$usuario->setHashSenha($data['senha']);
$usuario->setLogin($data['login']);

$registrado = UsuarioController::Registrar($usuario);

header('Content-Type: application/json; charset=utf-8');
http_response_code($registrado ? 201 : 400);
die(json_encode(["registrado" => $registrado]));