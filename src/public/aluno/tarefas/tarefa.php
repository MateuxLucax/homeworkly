<?php

$root = '../../..';

require_once $root . '/utils/response-utils.php';
forbidMethodsNot('GET');
require_once $root . '/dao/UsuarioDAO.php';
require_once $root . '/models/TipoUsuario.php';
UsuarioDAO::validaSessaoTipo(TipoUsuario::ALUNO);

require_once $root . '/public/base/tarefas/tarefa.php';

$tarefa = buscarTarefaOuNotFound();
$permissao = new PermissaoTarefa($tarefa->id());
$permissaoVisualizar = $permissao->visualizar($_SESSION['id_usuario'], $_SESSION['tipo']);
if ($permissaoVisualizar != PermissaoTarefa::PODE) responsePermissaoNaoPode($permissaoVisualizar);

$resEntrega = Query::select(
    'SELECT id_entrega AS id, conteudo, data_hora, visto, nota, comentario
       FROM entrega
      WHERE (id_aluno, id_tarefa) = (:idAluno, :idTarefa)',
    [':idAluno' => $_SESSION['id_usuario'],
      ':idTarefa' => $tarefa->id() ]
);

$entrega = count($resEntrega) == 0 ? null : $resEntrega[0];

$view['tarefa'] = $tarefa;
$view['permissao'] = $permissao;
$view['entrega'] = $entrega;
require $root . '/views/tarefas/tarefa.php';