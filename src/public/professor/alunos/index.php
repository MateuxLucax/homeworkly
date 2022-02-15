<?php
$root = '../../../';
require_once $root . 'utils/response-utils.php';

forbidMethodsNot('GET');
require_once $root . 'dao/UsuarioDAO.php';
require_once $root . 'dao/TurmaDAO.php';
require_once $root . 'utils/SessionUtil.php';
require_once $root . 'dao/DisciplinaDAO.php';
require_once $root . 'models/TipoUsuario.php';

UsuarioDAO::validaSessaoTipo(TipoUsuario::PROFESSOR);

$usuario = SessionUtil::usuarioLogado();

$turmaAtual = isset($_GET['id_turma'])
    ? TurmaDAO::buscar($_GET['id_turma'])
    : TurmaDAO::turmasDeProfessor($usuario->getId())[0];

$alunos = DisciplinaDAO::alunosDeProfessor($usuario->getId(), $turmaAtual->getId());

$view['title'] = 'Alunos';
$view['sidebar_links'] = 'professor/componentes/sidebar.php';
$view['content_path'] = 'views/professor/alunos/listar.php';
$view['alunos'] = $alunos;

require_once $root . 'views/componentes/base.php';
