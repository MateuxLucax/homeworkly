<?php

require_once $root . '/utils/response-utils.php';
require_once $root . '/models/TipoUsuario.php';

require_once $root . '/dao/TarefaDAO.php';

try
{
    $dados = readJsonRequestBody();

    $id = $dados['id'];

    if (!TarefaDAO::existe($id)) {
        respondJson(HttpCodes::NOT_FOUND, ['message' => 'Não existe tarefa de id'.$id]);
    }

    if (!TarefaDAO::usuarioPodeAlterarTarefa($_SESSION['id_usuario'], $_SESSION['tipo'], $id)) {
        respondJson(HttpCodes::UNAUTHORIZED, ['message' => 'O usuário não está autorizado a excluir a tarefa']);
    }

    if (TarefaDAO::tarefaTemEntregas($id)) {
        respondJson(HttpCodes::FORBIDDEN, ['message' => 'A tarefa não pode ser excluída porque tem entregas']);
    }

    $ok = Query::execute('DELETE FROM tarefa WHERE id_tarefa = :id', ['id' => $id]);

    if ($ok) {
        respondJson(HttpCodes::OK, ['message' => 'A tarefa foi excluída com sucesso']);
    } else {
        respondJson(HttpCodes::INTERNAL_SERVER_ERROR, ['message' => 'O servidor não conseguiu excluir a tarefa']);
    }
}
catch (Exception $e)
{
    respondJson(
          HttpCodes::INTERNAL_SERVER_ERROR
        , ['message' => 'O servidor não conseguiu excluir a tarefa; exceção ocorrida', 'exception' => $e]
    );
}