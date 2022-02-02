<?php


$root = '../../';
require_once $root . 'utils/response-utils.php';

forbidMethodsNot('GET');
require_once $root . 'dao/UsuarioDAO.php';
require_once $root . 'dao/TurmaDAO.php';
require_once $root . 'utils/SessionUtil.php';
require_once $root . 'models/TipoUsuario.php';

UsuarioDAO::validaSessaoTipo(TipoUsuario::PROFESSOR);
$usuario = SessionUtil::usuarioLogado();

$turmaAtual = isset($_GET['id_turma'])
    ? TurmaDAO::buscar($_GET['id_turma'])
    : TurmaDAO::turmasDeProfessor($usuario->getId())[0];

$view['title'] = 'Inicio';
$view['content_path'] = 'views/professor/inicio.php';
$view['sidebar_links'] = 'professor/componentes/sidebar.php';
$view['inicio_eventos'] = '/professor/tarefas/eventos.php?id_turma=' . $turmaAtual->getId();
$view['adicionar_tarefa'] = '/professor/tarefas/eventos.php?id_turma=' . $turmaAtual->getId();
$view['ano_turma'] = $turmaAtual->getAno();
$view['criar_tarefa_modal'] = 'views/professor/componentes/criar_tarefa.php';
require_once $root . 'views/componentes/base.php';