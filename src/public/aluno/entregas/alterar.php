<?php

$root = '../../..';

require_once $root . '/utils/response-utils.php';
forbidMethodsNot('PUT');
require_once $root . '/dao/UsuarioDAO.php';
require_once $root . '/models/TipoUsuario.php';
UsuarioDAO::validaSessaoTipo(TipoUsuario::ALUNO);

// -------------------------------------------------------

require_once $root . '/models/Entrega.php';
require_once $root . '/dao/TarefaDAO.php';
require_once $root . '/dao/EntregaDAO.php';
require_once $root . '/dao/PermissaoEntrega.php';
require_once $root . '/utils/DateUtil.php';

try
{

    if (empty($_GET['tarefa']) || empty($_GET['aluno'])) respondJson(
        HttpCodes::BAD_REQUEST,
        ['message' => 'Não foram informados os IDs da tarefa e do aluno']
    );

    $idTarefa = $_GET['tarefa'];
    $idAluno = $_GET['aluno'];

    //
    // Verifica se aluno pode alterar entrega
    //

    $entrega = EntregaDAO::buscar($idAluno, $idTarefa);
    $entrega->setTarefa(
        TarefaDAO::buscar($entrega->tarefa()->id())
    );

    if ($entrega == null) respondJson(
        HttpCodes::NOT_FOUND,
        ['message' => 'Não existe entrega feita pelo aluno de ID '.$idAluno.' na tarefa de ID '.$idTarefa ]
    );

    $permissao = PermissaoEntrega::alterar($_SESSION['id_usuario'], $_SESSION['tipo'], $entrega);

    if ($permissao == PermissaoEntrega::PODE)
    {
        $dados = readJsonRequestBody();

        $entrega->setConteudo($dados['conteudo']);
        $entrega->setDataHora(DateUtil::toLocalDateTime('now'));

        $ok = EntregaDAO::alterar($entrega);

        if ($ok) respondJson(
            HttpCodes::OK,
            ['message' => 'Entrega atualizada com sucesso']
        );
        else respondJson(
            HttpCodes::INTERNAL_SERVER_ERROR,
            ['message' => 'Não foi possível atualizar a entrega no banco de dados']
        );
    }
    else
    {
        $mensagem = match($permissao) {
            PermissaoEntrega::NAO_EH_ALUNO => 'O usuário logado não é um aluno',
            PermissaoEntrega::NAO_EH_ALUNO => 'O aluno logado não pertence à turma em que a tarefa foi dada',
            PermissaoEntrega::ARQUIVADA => 'A tarefa está arquivada',
            PermissaoEntrega::FECHADA => 'A tarefa já foi fechada',
            PermissaoEntrega::JA_ENTREGUE => 'A entrega já foi feita em definitivo',
            default => 'Segundo o sistema, o usuário não tem permissão de alterar a entrega, mas não sabemos por que'
        };

        respondJson(
            HttpCodes::BAD_REQUEST,
            ['message' => 'A entrega não pôde ser alterada. Motivo: '.$mensagem]
        );
    }

}
catch (Exception $e)
{
    respondJson(
        HttpCodes::INTERNAL_SERVER_ERROR,
        ['message' => 'Ocorreu uma exceção durante a alteração da tarefa', 'exception' => $e]
    );
}