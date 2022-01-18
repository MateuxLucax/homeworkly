<?php

require_once $root . '/models/Tarefa.php';
require_once $root . '/dao/TarefaDAO.php';
require_once $root . '/dao/PermissaoTarefa.php';

function buscarTarefaOuNotFound(): Tarefa
{
    if (!isset($_GET['id'])) {
        respondWithNotFoundPage('<b>Erro do sistema:</b> nenhum <b>ID</b> de tarefa fornecido.');
    }

    $id = $_GET['id'];
    $tarefa = TarefaDAO::buscar($id);

    if (is_null($tarefa)) {
        respondWithNotFoundPage("Tarefa de <b>id $id</b> não encontrada.");
    }

    return $tarefa;
}

function responsePermissaoNaoPode(int $permissao): never
{
    assert($permissao != PermissaoTarefa::PODE);
    if ($permissao == PermissaoTarefa::NAO_AUTORIZADO)
    {
        switch ($_SESSION['tipo']) {
        case TipoUsuario::PROFESSOR:
            $codigo = HttpCodes::BAD_REQUEST;
            $titulo = 'Não autorizado';
            $motivo = 'Não é professor da disciplina da tarefa';
            break;
        case TipoUsuario::ALUNO:
            $codigo = HttpCodes::BAD_REQUEST;
            $titulo = 'Não autorizado';
            $motivo = 'Não é aluno da turma da tarefa';
            break;
        default:
            $codigo = HttpCodes::INTERNAL_SERVER_ERROR;
            $titulo = 'Erro do sistema';
            $motivo = 'Desconhecido (<b>erro do sistema</b>: o usuário é administrador mas não está autorizado a ver a tarefa)';
            break;
        }

        respondWithErrorPage(
            $codigo,
            $titulo,
            'Você não tem autorização para visualizar essa tarefa.<br/>Motivo: '.$motivo
        );
    }
    else
    {
        respondWithErrorPage(
            HttpCodes::INTERNAL_SERVER_ERROR,
            'Erro do sistema',
            'Segundo o sistema, você não pode visualizar essa tarefa, mas não sabemos o motivo.<br/><small>Cód.:'.$permissao.'</small>'
        );
    }
}