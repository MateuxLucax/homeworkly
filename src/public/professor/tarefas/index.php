<?php
$root = '../../../';
require_once $root . 'utils/response-utils.php';

forbidMethodsNot('GET');
require_once $root . 'dao/UsuarioDAO.php';
require_once $root . 'models/TipoUsuario.php';

UsuarioDAO::validaSessaoTipo(TipoUsuario::PROFESSOR);

require_once $root . 'dao/TurmaDAO.php';
require_once $root . 'dao/TarefaDAO.php';
require_once $root . 'models/TipoUsuario.php';
require_once $root . 'models/Evento.php';
require_once $root . 'utils/SessionUtil.php';

$usuario = SessionUtil::usuarioLogado();

$turmaAtual = isset($_GET['id_turma'])
    ? TurmaDAO::buscar($_GET['id_turma'])
    : TurmaDAO::turmasDeProfessor($usuario->getId())[0];

$tarefas = TarefaDAO::listarPorProfessor($usuario->getId(), $turmaAtual->getId());

$view['title'] = 'Tarefas';
$view['content_path'] = 'views/professor/tarefas/listar.php';
$view['sidebar_links'] = 'professor/componentes/sidebar.php';
$view['tarefas'] = $tarefas;

require_once $root . 'views/componentes/base.php';
