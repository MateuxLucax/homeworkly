<?php

$root = '../../../';
require_once $root . 'utils/response-utils.php';
forbidMethodsNot('POST');
require_once $root . 'dao/usuarioDAO.php';
require_once $root . 'models/TipoUsuario.php';
UsuarioDAO::validaSessaoTipo(TipoUsuario::PROFESSOR);

// ------------------------------

require_once $root . 'database/Query.php';

try
{
    $dados = readJsonRequestBody();

    function responderErroSeCampoAusente($campo) {
        global $dados;
        if (!array_key_exists($campo, $dados)) respondJson(
            HttpCodes::BAD_REQUEST,
            ['message' => 'Erro do sistema. Campo '.$campo.' não foi fornecido no corpo da request']
        );
    }

    foreach (['aluno', 'tarefa', 'comentario'] as $campoEsperado) {
        responderErroSeCampoAusente($campoEsperado);
    }

    $idAluno = $dados['aluno'];
    $idTarefa = $dados['tarefa'];

    $resultadoComNota = Query::select(
        'SELECT com_nota FROM tarefa WHERE id_tarefa = :idTarefa',
        [ ':idTarefa' => $idTarefa ]
    );

    if (count($resultadoComNota) == 0) respondJson(
        HttpCodes::NOT_FOUND,
        ['message' => 'Erro do sistema. Não existe entrega do aluno de ID '.$idAluno.' para a tarefa de ID'.$idTarefa ]
    );

    $comNota = (bool) $resultadoComNota[0]['com_nota'];

    responderErroSeCampoAusente($comNota ? 'nota' : 'visto');

    // TODO? verificar se professor ($_SESSION['id_usuario']) fazendo avaliação é professor da disciplina da tarefa dessa entrega

    // ------------------------------

    $sql = '
        UPDATE entrega
        SET comentario = :comentario
            , '.($comNota ? 'nota = :nota' : 'visto = :visto').'
        WHERE (id_tarefa, id_aluno) = (:idTarefa, :idAluno)';

    $params = [
        ':comentario' => $dados['comentario'],
        ':idTarefa'   => $idTarefa,
        ':idAluno'    => $idAluno
    ];

    if ($comNota) {
        $params[':nota'] = $dados['nota'];
    } else {
        $params[':visto'] = $dados['visto'];
    }

    $ok = Query::execute($sql, $params);

    if (!$ok) respondJson(
        HttpCodes::INTERNAL_SERVER_ERROR,
        ['message' => 'Erro do servidor ao atualizar a entrega']
    );
    else respondJson(
        HttpCodes::OK,
        ['message' => 'Entrega avaliada com sucesso!']
    );
}
catch (Exception $e)
{
    respondJson(
        HttpCodes::INTERNAL_SERVER_ERROR,
        ['message' => 'Erro do servidor ao atualizar a entrega: ocorreu uma exceção',
         'exception' => $e]
    );
}