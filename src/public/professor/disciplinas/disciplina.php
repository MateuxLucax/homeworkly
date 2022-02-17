<?php

$root = '../../../';

require_once $root . 'utils/response-utils.php';
forbidMethodsNot('GET');
require_once $root . 'dao/UsuarioDAO.php';
require_once $root . 'models/TipoUsuario.php';
UsuarioDAO::validaSessaoTipo(TipoUsuario::PROFESSOR);

// -------------------------------------------------------

require_once $root . 'models/Tarefa.php';
require_once $root . 'models/TarefaEstado.php';
require_once $root . 'dao/TarefaDAO.php';

if (!isset($_GET['id'])) respondWithErrorPage(
    HttpCodes::BAD_REQUEST,
    'Erro do sistema',
    'A tela da disciplina foi acessada sem o ID de uma disciplina ser fornecido'
);

$id = $_GET['id'];

$disciplina = DisciplinaDAO::buscar($id);

if ($disciplina == null) respondWithNotFoundPage(
    'Não existe disciplina de ID '.$id
);

$disciplina->setProfessores(UsuarioDAO::buscarProfessoresDeDisciplina($disciplina->getId()));

$tarefas = TarefaDao::buscarDeDisciplina($id);

$tarefasPorEstado = [
    'esperando_abertura' => [],
    'aberta' => [],
    'fechada' => []
];

foreach ($tarefas as $tarefa) {
    $indice = match($tarefa->estado()) {
        TarefaEstado::ESPERANDO_ABERTURA => 'esperando_abertura',
        TarefaEstado::ABERTA             => 'aberta',
        TarefaEstado::FECHADA            => 'fechada',
        TarefaEstado::ARQUIVADA          => 'fechada'
    };
    $tarefasPorEstado[$indice][] = $tarefa;
}

// -------------------------------------------------------

$view['disciplina'] = $disciplina;
$view['tarefasPorEstado'] = $tarefasPorEstado;

$view['title'] = $disciplina->getNome();
$view['content_path'] = 'views/professor/disciplinas/disciplina.php';
$view['sidebar_links'] = 'professor/componentes/sidebar.php';
require_once $root . 'views/componentes/base.php';