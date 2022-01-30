<?php
$root = '../../../';
require_once $root . 'utils/response-utils.php';

forbidMethodsNot('GET');
require_once $root . 'dao/UsuarioDAO.php';
require_once $root . 'models/TipoUsuario.php';

UsuarioDAO::validaSessaoTipo(TipoUsuario::ALUNO);

require_once $root . 'dao/TurmaDAO.php';
require_once $root . 'dao/TarefaDAO.php';
require_once $root . 'models/TipoUsuario.php';
require_once $root . 'models/Evento.php';
require_once $root . 'utils/SessionUtil.php';

$usuario = SessionUtil::usuarioLogado();

$turmaDoAluno = TurmaDAO::turmaAtualDeAluno($usuario->getId());
$tarefas = TarefaDAO::listarPorAluno($usuario->getId(), $turmaDoAluno->getId());

$view['title'] = 'Tarefas';
$view['content_path'] = 'views/aluno/tarefas/listar.php';
$view['sidebar_links'] = 'aluno/componentes/sidebar.php';
$view['tarefas'] = $tarefas;

require_once $root . 'views/componentes/base.php';
