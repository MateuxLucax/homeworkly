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

    if (empty($_GET['id'])) respondJson(
        HttpCodes::BAD_REQUEST,
        ['message' => 'Não foi informado o ID da entrega a ser alterada']
    );

    $id = $_GET['id'];

    $existe = (bool) Query::select(
        'SELECT EXISTS(SELECT 1 FROM entrega WHERE id_entrega = :id) AS existe',
        [':id' => $id]
    )[0]['existe'];

    if (!$existe) respondJson(
        HttpCodes::NOT_FOUND,
        ['message' => 'Não existe entrega de ID '.$id]
    );

    $idAlunoEmSessao = $_SESSION['id_usuario'];

    $idAlunoQueFezEntrega = Query::select(
        'SELECT id_aluno FROM entrega WHERE id_entrega = :id',
        [':id' => $id]
    )[0]['id_aluno'];

    if ($idAlunoEmSessao != $idAlunoQueFezEntrega) respondJson(
        HttpCodes::UNAUTHORIZED,
        ['message' => 'Você não é o aluno que fez a entrega']
    );

    // TODO implementar funcionalidade de 'entregar em definitivo'
    // que nem a do Moodle para as entregas.
    // Porque quando um aluno atualizar uma entrega depois
    // da data de entrega, mesmo que uma entrega quase pronta
    // já tenha sido feita antes, ela ficará marcada como atrasada.
    // Isso porque atualizamos a coluna data_hora.
    // Só que se não realizarmos essa atualização, será
    // possível que alunos criem a tarefa antes da data de entrega
    // só pra não ficar atrasada, deixam o campo de conteúdo em
    // branco, e preencham potencialmente depois da data de entrega
    // sem consequências.
    // Com o 'entregar em definitivo' não existe esse impasse.

    $conteudo = readJsonRequestBody()['conteudo'];
    $dataHora = DateUtil::toLocalDateTime('now');

    $ok = Query::execute(
        'UPDATE entrega
            SET conteudo = :conteudo, data_hora = :dataHora
          WHERE id_entrega = :id',
        [ ':id'       => $id,
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