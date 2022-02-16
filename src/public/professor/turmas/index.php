<?php
$root = '../../../';
require_once $root . 'utils/response-utils.php';

forbidMethodsNot('GET');
require_once $root . 'dao/UsuarioDAO.php';
require_once $root . 'dao/TurmaDAO.php';
require_once $root . 'utils/SessionUtil.php';
require_once $root . 'models/TipoUsuario.php';

UsuarioDAO::validaSessaoTipo(TipoUsuario::PROFESSOR);

$usuario = SessionUtil::usuarioLogado();

$view['title'] = 'Tur,as';
$view['sidebar_links'] = 'professor/componentes/sidebar.php';
$view['content_path'] = 'views/professor/turmas/listar.php';
$view['turmas'] = TurmaDAO::alunosDeProfessor($usuario->getId());

require_once $root . 'views/componentes/base.php';
