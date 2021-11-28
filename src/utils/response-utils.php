<?php

require_once $root.'utils/HttpCodes.php';

/**
 * Gera resposta do tipo JSON com o código dado e com o corpo sendo a codificação JSON do valor $body dado.
 * 
 * @param int $code código HTTP da resposta a ser dada
 * @param mixed $body valor a ser codificado em JSON a ser enviado como corpo da resposta
 * @return void
 */
function respond(int $code, mixed $body = ''): void
{
    // PHP 8.1: mudar de void pra never
    header('Content-Type: application/json; charset=utf-8');
    http_response_code($code);
    die(json_encode($body));
}

/**
 * Gera resposta 404 NOT FOUND com uma página mostrando a mensagem dada
 * 
 * @param string $message a mensagem a mostrar na página de not found
 * @return void
 */
function respondWithNotFoundPage(string $message): void
{
    global $root;

    $view['title'] = 'Não encontrado';
    $view['mensagem'] = $message;
    http_response_code(HttpCodes::NOT_FOUND);
    require $root.'views/nao-encontrado.php';
    die();
}

/**
 * Responde com 405 METHOD NOT ALLOWED caso o método da request não seja um dos $methods fornecidos.
 * 
 * @param string|array $methods Lista de métodos permitidos
 * @return void
 */
function forbidMethodsNot(string|array $methods): void
{
    if (is_string($methods)) {
        if ($_SERVER['REQUEST_METHOD'] != $methods) {
            respond(HttpCodes::METHOD_NOT_ALLOWED);
        }
    } else if (!in_array($_SERVER['REQUEST_METHOD'], $methods)) {
        respond(HttpCodes::METHOD_NOT_ALLOWED);
    }
}

/**
 * Analisa o corpo da request como JSON e retorna ele
 * 
 * @return array Corpo da request decodificado como array associativo
 */
function readRequestBody(): array
{
    return json_decode(file_get_contents('php://input'), true);
}
