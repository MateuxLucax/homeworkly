<?php

$root = '../../..';

require_once $root . '/utils/response-utils.php';
forbidMethodsNot('PUT');
require_once $root . '/dao/UsuarioDAO.php';
require_once $root . '/models/TipoUsuario.php';
UsuarioDAO::validaSessaoTipo(TipoUsuario::ALUNO);

// -------------------------------------------------------

require_once $root . '/database/Query.php';
require_once $root . '/utils/DateUtil.php';

try
{

    if (empty($_GET['tarefa']) || empty($_GET['aluno'])) respondJson(
        HttpCodes::BAD_REQUEST,
        ['message' => 'Não foram informados os IDs da tarefa e do aluno']
    );

    $idTarefa = $_GET['tarefa'];
    $idAluno = $_GET['aluno'];

    $result = Query::select(
        'SELECT em_definitivo FROM entrega WHERE (id_tarefa, id_aluno) = (:idTarefa, :idAluno)',
        [ ':idTarefa' => $idTarefa,
          ':idAluno'  => $idAluno ]
    );

    if (count($result) == 0) respondJson(
        HttpCodes::NOT_FOUND,
        ['message' => 'Não existe entrega feita pelo aluno de ID '.$idAluno.' na tarefa de ID '.$idTarefa ]
    );

    if ($result[0]['em_definitivo']) respondJson(
        HttpCodes::UNAUTHORIZED,
        ['message' => 'A entrega não pode ser alterada pois já foi feita em definitivo']
    );

    $conteudo = readJsonRequestBody()['conteudo'];
    $dataHora = DateUtil::toLocalDateTime('now');

    $ok = Query::execute(
        'UPDATE entrega
            SET conteudo = :conteudo, data_hora = :dataHora
          WHERE (id_aluno, id_tarefa) = (:idAluno, :idTarefa)',
        [ ':idAluno'  => $idAluno,
          ':idTarefa'  => $idTarefa,
          ':conteudo' => $conteudo,
          ':dataHora' => $dataHora->format('Y-m-d H:i:s') ]
    );

    if ($ok) respondJson(
        HttpCodes::OK,
        ['message' => 'Entrega atualizada com sucesso']
    );
    else respondJson(
        HttpCodes::INTERNAL_SERVER_ERROR,
        ['message' => 'Não foi possível atualizar a entrega no banco de dados']
    );
}
catch (Exception $e)
{
    respondJson(
        HttpCodes::INTERNAL_SERVER_ERROR,
        ['message' => 'Ocorreu uma exceção durante a alteração da tarefa', 'exception' => $e]
    );
}