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

    //
    // Verifica se aluno pode entregar a tarefa
    //

    $tarefa = TarefaDAO::buscar($idTarefa);

    $idTurma = $tarefa->disciplina()->getTurma()->getId();
    if (!UsuarioDAO::alunoDaTurma($idAluno, $idTurma)) {
        respondJson(
            HttpCodes::UNAUTHORIZED,
            ['message' => 'O usuário em sessão não é aluno da turma da qual a tarefa pertence']
        );
    }

    $estado = $tarefa->estado();

    if ($estado == TarefaEstado::ARQUIVADA) {
        respondJson(
            HttpCOdes::UNAUTHORIZED,
            ['message' => 'A entrega não pode ser feita pois a tarefa está arquivada']
        );
    }

    if ($estado == TarefaEstado::FECHADA) {
        respondJson(
            HttpCodes::UNAUTHORIZED,
            ['message' => 'A entrega não pode ser feita pois a tarefa já foi fechada']
        );
    }

    if ($estado == TarefaEstado::ESPERANDO_ABERTURA) {
        respondJson(
            HttpCodes::UNAUTHORIZED,
            ['message' => 'A entrega não pode ser feita pois a tarefa ainda não foi aberta']
        );
    }

    //
    // Realiza a entrega
    //

    $dados = readJsonRequestBody();

    $entrega = (new Entrega)
        ->setTarefa((new Tarefa)->setId($idTarefa))
        ->setAluno((new Usuario)->setId($idAluno))
        ->setConteudo($dados['conteudo'])
        ->setDataHora(DateUtil::toLocalDateTime('now'))
        ->setEmDefinitivo(false);

    $ok =  EntregaDAO::criar($entrega);

    if ($ok) respondJson(
        HttpCodes::OK,
        [ 'message' => 'A entrega foi feita com sucesso' ]
    );
    else respondJson(
        HttpCodes::INTERNAL_SERVER_ERROR,
        ['message' => 'Erro do servidor ao criar a entrega no banco de dados']
    );
}
catch (Exception $e)
{
    respondJson(
        HttpCodes::INTERNAL_SERVER_ERROR,
        ['message' => 'Erro do servidor ao criar a entrega, exceção ocorreu', 'exception' => $e]
    );
}