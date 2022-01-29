<?php

$root = '../../../';

require_once $root . 'dao/UsuarioDAO.php';
require_once $root . 'dao/TurmaDAO.php';
require_once $root . 'dao/TarefaDAO.php';
require_once $root . 'models/TipoUsuario.php';
require_once $root . 'utils/response-utils.php';
require_once $root . 'models/Evento.php';
require_once $root . 'utils/SessionUtil.php';

UsuarioDAO::validaSessaoTipo(TipoUsuario::ALUNO);

try {
    var_dump("bosta");
    $usuario = SessionUtil::usuarioLogado();

    $turmaDoAluno = TurmaDAO::turmaAtualDeAluno($usuario->getId());
    $tarefas = TarefaDAO::listarPorAluno($usuario->getId(), $turmaDoAluno->getId());

    $eventos = Evento::tarefasToEventos($tarefas);
    $eventos = array_map(
        fn (Evento $row) => $row->toArray(),
        $eventos
    );

    var_dump($eventos);

    respondJson(HttpCodes::OK, $eventos);
} catch (Exception $e) {
    respondJson(HttpCodes::BAD_REQUEST, ['exception' => $e]);
}
