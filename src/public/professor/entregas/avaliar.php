<?php

$root = '../../../';
require_once $root . 'utils/response-utils.php';
forbidMethodsNot('POST');
require_once $root . 'dao/usuarioDAO.php';
require_once $root . 'models/TipoUsuario.php';
UsuarioDAO::validaSessaoTipo(TipoUsuario::PROFESSOR);

// ------------------------------

require_once $root . 'database/Query.php';
require_once $root . 'dao/PermissaoEntrega.php';

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

    $tarefa = TarefaDAO::buscar($idTarefa);

    if (is_null($tarefa)) respondJson(
        HttpCodes::NOT_FOUND,
        ['message' => 'Erro do sistema. Não existe tarefa de ID '.$idTarefa ]
    );

    responderErroSeCampoAusente($tarefa->comNota() ? 'nota' : 'visto');

    $permAvaliar = PermissaoEntrega::avaliar($_SESSION['id_usuario'], $_SESSION['tipo'], $tarefa);
    if ($permAvaliar != PermissaoEntrega::PODE) {
        list($codigo, $motivo) = match($permAvaliar) {
            PermissaoEntrega::NAO_EH_PROFESSOR => [HttpCodes::UNAUTHORIZED, 'não é um usuário do tipo professor'],
            PermissaoEntrega::NAO_EH_DA_DISCIPLINA => [HttpCodes::UNAUTHORIZED, 'não é um professor da disciplina'],
            PermissaoEntrega::ESPERANDO_ABERTURA => [HttpCodes::BAD_REQUEST, 'a tarefa ainda não foi aberta'],
            PermissaoEntrega::ARQUIVADA => [HttpCodes::BAD_REQUEST, 'a tarefa está arquivada'],
            default => [HttpCodes::INTERNAL_SERVER_ERROR, 'ErroMatchAvaliarNaoExaustivo']
        };
        respondJson(
            HttpCodes::BAD_REQUEST,
            ['message' => 'Você não pode avaliar entregas dessa tarefa pois '.$motivo]
        );
    }

    // ------------------------------

    $sql = '
        INSERT INTO avaliacao (id_aluno, id_tarefa, nota, visto, comentario)
        VALUES (:idAluno, :idTarefa, :nota, :visto, :comentario)
        ON CONFLICT (id_aluno, id_tarefa) DO
          UPDATE SET nota = :nota, visto = :visto, comentario = :comentario';

    $params = [
        ':comentario' => $dados['comentario'],
        ':idTarefa'   => $idTarefa,
        ':idAluno'    => $idAluno,
        ':visto'      => $tarefa->comNota() ? null : $dados['visto'],
        ':nota'       => $tarefa->comNota() ? $dados['nota'] : null
    ];

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