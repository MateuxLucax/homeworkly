<?php

header('Content-Type: application/json; charset=utf-8');

if (!($_SERVER['REQUEST_METHOD'] === 'POST')) {
    http_response_code(405);
    die(json_encode(["erro" => "metodo nao permitido"]));
}

try {
    $root = $_SERVER['DOCUMENT_ROOT'] . '/../';

    require_once $root. 'models/Usuario.php';
    require_once $root. 'models/TipoUsuario.php';
    require_once $root. 'dao/UsuarioDAO.php';

    $data = json_decode(file_get_contents('php://input'), true);

    $usuario = new Usuario();
    $usuario->setLogin($data['login']);

    $loggedInUser = UsuarioDAO::login($usuario, $data['senha']);
    $response = array();

    if (!empty($loggedInUser)) {
        $whereIGo = match ($loggedInUser->getTipo()) {
            TipoUsuario::ALUNO         => "/aluno",  // ?turma= opcional, por padrão pega a do ano atual
            TipoUsuario::ADMINISTRADOR => "/admin",
            TipoUsuario::PROFESSOR     => "/professor",
            default                    => "/entrar",
        };

        $response = [
            "location" => $whereIGo
        ];
    }

    http_response_code(200);
    die(json_encode($response));
} catch (UnauthorizedException $e) {
    http_response_code(401);
    die(json_encode(['message' => "Não foi possível realizar o login. Verifique seu usuário e senha."]));
}  catch (UserNotFoundException $e) {
    http_response_code(401);
    die(json_encode(['message' => "Usuário não encontrado."]));
} catch (Exception $e) {
    http_response_code(400);
    die(json_encode(['exception' => $e]));
}