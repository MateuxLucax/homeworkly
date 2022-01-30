<?php

$root = '../../../';

require_once $root . 'utils/response-utils.php';
forbidMethodsNot('GET');
require_once $root . 'dao/UsuarioDAO.php';
require_once $root . 'models/TipoUsuario.php';
UsuarioDAO::validaSessaoTipo(TipoUsuario::ALUNO);

// -------------------------------------------------------

require_once $root . 'public/base/tarefas/tarefa.php';
// traz buscarTarefaOuNotFound e responsePermissaoNaoPode

require_once $root . 'models/Entrega.php';
require_once $root . 'dao/EntregaDAO.php';
require_once $root . 'dao/PermissaoEntrega.php';

$tarefa = buscarTarefaOuNotFound();
$permissaoTarefa = new PermissaoTarefa($tarefa->id());
$permissaoTarefaVisualizar = $permissaoTarefa->visualizar(
    $_SESSION['id_usuario'],
    $_SESSION['tipo']
);
if ($permissaoTarefaVisualizar != PermissaoTarefa::PODE) {
    responsePermissaoNaoPode($permissaoTarefaVisualizar);
}

$view['tarefa'] = $tarefa;
$view['permissaoTarefa'] = $permissaoTarefa;

$entrega = EntregaDAO::buscar($_SESSION['id_usuario'], $tarefa->id());
$entrega?->setTarefa($tarefa);

$view['entrega'] = $entrega;
$view['permissaoEntrega']
= $entrega == null
? PermissaoEntrega::criar($_SESSION['id_usuario'], $_SESSION['tipo'], $tarefa)
: PermissaoEntrega::alterar($_SESSION['id_usuario'], $_SESSION['tipo'], $entrega);

$view['content_path'] = 'views/tarefas/tarefa.php';
$view['sidebar_links'] = 'aluno/componentes/sidebar.php';

require_once $root . 'views/componentes/base.php';