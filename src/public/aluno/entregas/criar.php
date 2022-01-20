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
require_once $root . '/dao/TarefaDAO.php';
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

    $dadosRequest = readJsonRequestBody();

    $conteudo = $dadosRequest['conteudo'];
    $dataHora = DateUtil::toLocalDateTime('now')->format('Y-m-d H:i:s');

    $pdo = Connection::getInstance();
    $ok = $pdo->prepare(
        'INSERT INTO entrega (id_tarefa, id_aluno, conteudo, data_hora)
         VALUES (:idTarefa, :idAluno, :conteudo, :dataHora)'
    )->execute([
        ':idTarefa' => $idTarefa,
        ':idAluno' => $idAluno,
        ':conteudo' => $conteudo,
        ':dataHora' => $dataHora
    ]);

    if ($ok) respondJson(
        HttpCodes::OK,
        [ 'message' => 'A entrega foi feita com sucesso',
          'id' => $pdo->lastInsertId() ]
    );
    else respondJson(
        HttpCodes::INTERNAL_SERVER_ERROR,
        ['message' => 'Erro do servidor ao criar a entrega (execução do INSERT falhou)']
    );
}
catch (Exception $e)
{
    respondJson(
        HttpCodes::INTERNAL_SERVER_ERROR,
        ['message' => 'Erro do servidor ao criar a entrega, exceção ocorreu', 'exception' => $e]
    );
}