<?php

$root = '../../../';

require_once $root.'database/Connection.php';
require_once $root.'database/Query.php';
require_once $root.'models/TipoUsuario.php';
require_once $root.'dao/UsuarioDAO.php';
require_once $root.'dao/TurmaDAO.php';
require_once $root.'utils/response-utils.php';
require_once $root.'utils/HttpCodes.php';

UsuarioDAO::validaSessaoTipo(TipoUsuario::ADMINISTRADOR);

$headers = getallheaders();
$retornarJson = $headers['Accept'] == 'application/json';

if (!isset($_GET['id'])) {
    if ($retornarJson) {
        respondJson(HttpCodes::BAD_REQUEST, ['mensagem' => 'Não foi informado ID']);
    } else {
        header('Location: listar');
  }
}

$id = $_GET['id'];

if (!is_numeric($id)) {
    if ($retornarJson) {
        respondJson(HttpCodes::NOT_FOUND, ['mensagem' => $id.' não é um id válido.']);
    } else {
        respondWithNotFoundPage("<b>$id</b> não é um id válido.");
    }
}

try
{
    $turma = TurmaDao::buscar($id);
    TurmaDao::popularComAlunos($turma);
    TurmaDao::popularComDisciplinas($turma);

    if ($retornarJson) {
        respondJson(HttpCodes::OK, $turma->toArray());
    } else {
        $view['turma'] = $turma;
        $view['title'] = 'Turma';
        $view['ativo-nav'] = 'turmas';
        require_once $root.'views/turmas/turma.php';
    }
}
catch (Exception $e)
{
    if ($retornarJson) {
        respondJson(HttpCodes::NOT_FOUND, ['mensagem' => 'A turma de id '.$id.' não foi encontrada']);
    } else {
        respondWithNotFoundPage("A turma de id <b>$id</b> não foi encontrada.");
    }
}

