<?php

require_once $root.'/utils/HttpCodes.php';

/**
 * Gera resposta do tipo JSON com o código dado e com o corpo sendo a codificação JSON do valor $body dado.
 * 
 * @param int $code código HTTP da resposta a ser dada
 * @param mixed $body valor a ser codificado em JSON a ser enviado como corpo da resposta
 * @return void
 */
function respondJson(int $code, mixed $body = ''): void
{
    // PHP 8.1: mudar de void pra never
    header('Content-Type: application/json; charset=utf-8');
    http_response_code($code);
    echo json_encode($body);
    die();
}

/**
 * Gera resposta 404 NOT FOUND com uma página mostrando a mensagem dada
 * 
 * @param string $message a mensagem a mostrar na página de not found; pode conter HTML.
 * @return void
 */
function respondWithNotFoundPage(string $message): void
{
    respondWithErrorPage(HttpCodes::NOT_FOUND, 'Não encontrado', $message);
}

/**
 * Responde com uma página de erro
 * 
 * @param int $code o código HTTP da resposta
 * @param string $heading mensagem para aprecer como título do alerta na página; pode conter HTML.
 * @param string $message texto explicando o erro ocorrido; pode conter HTML.
 */
function respondWithErrorPage(int $code, string $heading, string $message): void
{
    global $root;

    $view['title'] = 'Erro';
    $view['heading'] = $heading;
    $view['mensagem'] = $message;
    http_response_code($code);
    require $root.'/views/erro.php';
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
            respondJson(HttpCodes::METHOD_NOT_ALLOWED);
        }
    } else if (!in_array($_SERVER['REQUEST_METHOD'], $methods)) {
        respondJson(HttpCodes::METHOD_NOT_ALLOWED);
    }
}

/**
 * Analisa o corpo da request como JSON e retorna ele
 * 
 * @return array Corpo da request decodificado como array associativo
 */
function readJsonRequestBody(): array
{
    return json_decode(file_get_contents('php://input'), true);
}
