<?php

require_once $root . '/utils/response-utils.php';

require_once $root . '/dao/PermissaoTarefa.php';
require_once $root . '/dao/TarefaDAO.php';

try
{
    $dados = readJsonRequestBody();

    $id = $dados['id'];

    if (!TarefaDAO::existe($id)) {
        respondJson(HttpCodes::NOT_FOUND, ['message' => 'Não existe tarefa de id '.$id]);
    }

    $permissao = (new PermissaoTarefa($id))->excluir($_SESSION['id_usuario'], $_SESSION['tipo']);

    if ($permissao == PermissaoTarefa::PODE)
    {
        $ok = Query::execute('DELETE FROM tarefa WHERE id_tarefa = :id', ['id' => $id]);
        if ($ok) {
            respondJson(HttpCodes::OK, ['message' => 'A tarefa foi excluída com sucesso']);
        } else {
            respondJson(HttpCodes::INTERNAL_SERVER_ERROR, ['message' => 'O servidor não conseguiu excluir a tarefa']);
        }
    } else {
        $motivo = match ($permissao) {
            PermissaoTarefa::NAO_AUTORIZADO => 'O usuário não está autorizado',
            PermissaoTarefa::ARQUIVADA      => 'Está arquivada (é de um ano passado)',
            PermissaoTarefa::FECHADA        => 'Já foi fechada',
            PermissaoTarefa::TEM_ENTREGAS   => 'Já tem entregas',
            default                         => 'Desconhecido (código '.$permissao.')'
        };
        respondJson(HttpCodes::BAD_REQUEST, ['message' => 'A tarefa não pôde ser excluída. Motivo: '.$motivo]);
    }

}
catch (Exception $e)
{
    respondJson(
          HttpCodes::INTERNAL_SERVER_ERROR
        , ['message' => 'O servidor não conseguiu excluir a tarefa; exceção ocorrida', 'exception' => $e]
    );
}