<?php
$root = '../../';
require_once $root . 'utils/response-utils.php';

forbidMethodsNot('GET');
require_once $root . 'dao/UsuarioDAO.php';
require_once $root . 'dao/TurmaDAO.php';
require_once $root . 'utils/SessionUtil.php';
require_once $root . 'models/TipoUsuario.php';

UsuarioDAO::validaSessaoTipo(TipoUsuario::ALUNO);

$usuario = SessionUtil::usuarioLogado();

$turma = TurmaDAO::turmaAtualDeAluno($usuario->getId());

$view['title'] = 'Inicio';
$view['content_path'] = 'views/aluno/inicio.php';
$view['sidebar_links'] = 'aluno/componentes/sidebar.php';
$view['inicio_eventos'] = '/aluno/tarefas/eventos.php';
$view['ano_turma'] = $turma->getAno();
require_once $root . 'views/componentes/base.php';