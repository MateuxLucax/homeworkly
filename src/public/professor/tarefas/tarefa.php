<?php

$root = '../../../';

require_once $root . 'utils/response-utils.php';
forbidMethodsNot('GET');
require_once $root . 'dao/UsuarioDAO.php';
require_once $root . 'models/TipoUsuario.php';
UsuarioDAO::validaSessaoTipo(TipoUsuario::PROFESSOR);

require_once $root . '/public/base/tarefas/tarefa.php';

$tarefa = buscarTarefaOuNotFound();
$permissao = new PermissaoTarefa($tarefa->id());
$permissaoVisualizar = $permissao->visualizar($_SESSION['id_usuario'], $_SESSION['tipo']);
if ($permissaoVisualizar != PermissaoTarefa::PODE) responsePermissaoNaoPode($permissaoVisualizar);

$view['tarefa'] = $tarefa;
$view['permissaoTarefa'] = $permissao;
$view['entregasPorAluno'] = EntregaDAO::entregasPorAluno($tarefa);

$view['content_path'] = 'views/tarefas/tarefa.php';
$view['sidebar_links'] = 'professor/componentes/sidebar.php';

require_once $root . 'views/componentes/base.php';