<?php

$root = '../../..';

require_once $root . '/utils/response-utils.php';
forbidMethodsNot('GET');
require_once $root . '/dao/UsuarioDAO.php';
require_once $root . '/models/TipoUsuario.php';
UsuarioDAO::validaSessaoTipo(TipoUsuario::ADMINISTRADOR);

require_once $root . '/dao/TarefaDAO.php';

if (!isset($_GET['id'])) {
    respondWithNotFoundPage('<b>Erro do sistema:</b> nenhum <b>ID</b> de tarefa fornecido.');
}

$id = $_GET['id'];
$tarefa = TarefaDAO::buscar($id);

if (is_null($tarefa)) {
    respondWithNotFoundPage("Tarefa de <b>id $id</b> não encontrada.");
}

$view['tarefa'] = $tarefa;
$view['pasta_usuario'] = TipoUsuario::pasta($_SESSION['tipo']);
require $root . '/views/tarefas/tarefa.php';