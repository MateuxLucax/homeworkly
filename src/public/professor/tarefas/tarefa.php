<?php

$root = '../../..';

require_once $root . '/utils/response-utils.php';
forbidMethodsNot('GET');
require_once $root . '/dao/UsuarioDAO.php';
require_once $root . '/models/TipoUsuario.php';
//UsuarioDAO::validaSessaoTipo(TipoUsuario::PROFESSOR); // FIXME dá erro
UsuarioDAO::validaSessao();

require_once $root . '/models/Tarefa.php';
require_once $root . '/dao/TarefaDAO.php';

if (!isset($_GET['id'])) {
    respondWithNotFoundPage('<b>Erro do sistema:</b> nenhum <b>ID</b> de tarefa fornecido.');
}

$id = $_GET['id'];
$tarefa = TarefaDAO::buscar($id);

if (is_null($tarefa)) {
    respondWithNotFoundPage("Tarefa de <b>id $id</b> não encontrada.");
}

// bloquear o botão de alterar caso o professor acessando a tela não seja o que criou a tarefa

$view['tarefa'] = $tarefa;
$view['pasta_usuario'] = TipoUsuario::pasta($_SESSION['tipo']);
require $root . '/views/tarefas/tarefa.php';