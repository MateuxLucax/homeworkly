<?php
$root = '../../../';
require_once $root . 'utils/response-utils.php';

forbidMethodsNot('GET');
require_once $root . 'dao/UsuarioDAO.php';
require_once $root . 'dao/TurmaDAO.php';
require_once $root . 'utils/SessionUtil.php';
require_once $root . 'dao/DisciplinaDAO.php';
require_once $root . 'models/TipoUsuario.php';

UsuarioDAO::validaSessaoTipo(TipoUsuario::ALUNO);

$usuario = SessionUtil::usuarioLogado();
$turmaDoAluno = TurmaDAO::turmaAtualDeAluno($usuario->getId());

$disciplinas = DisciplinaDAO::disciplinasDeTurma($usuario->getId(), $turmaDoAluno->getId());

$view['title'] = 'Disciplinas';
$view['sidebar_links'] = 'aluno/componentes/sidebar.php';
$view['content_path'] = 'views/aluno/disciplinas/listar.php';
$view['disciplinas'] = $disciplinas;

require_once $root . 'views/componentes/base.php';
