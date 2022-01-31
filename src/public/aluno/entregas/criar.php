<?php

$root = '../../..';

require_once $root . '/utils/response-utils.php';
forbidMethodsNot('POST');
require_once $root . '/dao/UsuarioDAO.php';
require_once $root . '/models/TipoUsuario.php';
UsuarioDAO::validaSessaoTipo(TipoUsuario::ALUNO);

// -------------------------------------------------------

require_once $root . '/models/Tarefa.php';
require_once $root . '/models/TarefaEstado.php';
require_once $root . '/models/Entrega.php';
require_once $root . '/dao/TarefaDAO.php';
require_once $root . '/dao/EntregaDAO.php';
require_once $root . '/dao/PermissaoEntrega.php';
require_once $root . '/utils/DateUtil.php';
require_once $root . '/database/Connection.php';

try
{
    if (empty($_GET['tarefa'])) {
        respondJson(
            HttpCodes::BAD_REQUEST,
            ['message' => 'Não foi informado o ID da tarefa a qual a entrega pertence']
        );
    }

    $idTarefa = $_GET['tarefa'];

    if (!TarefaDAO::existe($idTarefa)) {
        respondJson(
            HttpCodes::NOT_FOUND,
            ['message' => 'Não existe tarefa de ID' . $idTarefa]
        );
    }

    $idAluno = $_SESSION['id_usuario'];

    $tarefa = TarefaDAO::buscar($idTarefa);

    $permissao = PermissaoEntrega::criar($_SESSION['id_usuario'], $_SESSION['tipo'], $tarefa);

    if ($permissao == PermissaoEntrega::PODE)
    {
        $dados = readJsonRequestBody();

        $entrega = (new Entrega)
            ->setTarefa((new Tarefa)->setId($idTarefa))
            ->setAluno((new Usuario)->setId($idAluno))
            ->setConteudo($dados['conteudo'])
            ->setDataHora(DateUtil::toLocalDateTime('now'))
            ->setEmDefinitivo(false);

        $ok = EntregaDAO::criar($entrega);

        if ($ok) respondJson(
            HttpCodes::OK,
            [ 'message' => "A entrega foi feita com sucesso.\nNo entanto, você ainda precisa entregar em definitivo para que o professor possa vê-la e avaliá-la." ]
        );
        else respondJson(
            HttpCodes::INTERNAL_SERVER_ERROR,
            ['message' => 'Erro do servidor ao criar a entrega no banco de dados']
        );
    }
    else
    {
        $mensagem = match($permissao) {
            PermissaoEntrega::NAO_EH_ALUNO => 'O usuário não é um aluno',
            PermissaoEntrega::NAO_EH_DA_TURMA => 'O aluno não pertence a turma onde a tarefa foi criada',
            PermissaoEntrega::ESPERANDO_ABERTURA => 'A tarefa ainda não foi aberta',
            PermissaoEntrega::ARQUIVADA => 'A tarefa está arquivada',
            PermissaoEntrega::FECHADA => 'A tarefa já foi fechada',
            default => 'Segundo o sistema, o usuário não tem permissão de fazer a entrega, mas não sabemos por quê'
        };

        respondJson(
            HttpCodes::BAD_REQUEST,
            ['message' => 'A entrega não pôde ser feita. Motivo: '.$mensagem]
        );
    }
}
catch (Exception $e)
{
    respondJson(
        HttpCodes::INTERNAL_SERVER_ERROR,
        ['message' => 'Erro do servidor ao criar a entrega, exceção ocorreu', 'exception' => $e]
    );
}