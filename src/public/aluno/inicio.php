<?php
$root = '../../';
require_once $root . 'utils/response-utils.php';

forbidMethodsNot('GET');
require_once $root . 'dao/UsuarioDAO.php';
require_once $root . 'dao/TarefaDAO.php';
require_once $root . 'dao/TurmaDAO.php';
require_once $root . 'models/TipoUsuario.php';
require_once $root . 'models/Evento.php';
require_once $root . 'utils/SessionUtil.php';

UsuarioDAO::validaSessaoTipo(TipoUsuario::ALUNO);

$usuario = SessionUtil::usuarioLogado();

$turmaDoAluno = TurmaDAO::turmaAtualDeAluno($usuario->getId());
$tarefas = TarefaDAO::listarPorAluno($usuario->getId(), $turmaDoAluno->getId());

$eventos = Evento::tarefasToEventos($tarefas);
$eventos = json_encode(array_map(
    fn (Evento $row) => $row->toJson(),
    $eventos
));

$view['title'] = 'Inicio';
$view['content_path'] = 'views/aluno/inicio.php';
$view['sidebar_links'] = 'aluno/componentes/sidebar.php';
$view['inicio_eventos'] = $eventos;
require_once $root . 'views/componentes/base.php';