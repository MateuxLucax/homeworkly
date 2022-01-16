<?php

require_once $root . '/models/Tarefa.php';
require_once $root . '/dao/TarefaDAO.php';
require_once $root . '/dao/PermissaoTarefa.php';

if (!isset($_GET['id'])) {
    respondWithNotFoundPage('<b>Erro do sistema:</b> nenhum <b>ID</b> de tarefa fornecido.');
}

$id = $_GET['id'];
$tarefa = TarefaDAO::buscar($id);

if (is_null($tarefa)) {
    respondWithNotFoundPage("Tarefa de <b>id $id</b> não encontrada.");
}

$permissao = new PermissaoTarefa($id);
$permissaoVisualizar = $permissao->visualizar($_SESSION['id_usuario'], $_SESSION['tipo']);

if ($permissaoVisualizar == PermissaoTarefa::PODE)
{
    $view['tarefa'] = $tarefa;
    $view['permissao'] = $permissao;
    require $root . '/views/tarefas/tarefa.php';
}
else if ($permissaoVisualizar == PermissaoTarefa::NAO_AUTORIZADO)
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
        'Segundo o sistema, você não pode visualizar essa tarefa, mas não sabemos o motivo.<br/><small>Cód.:'.$permissaoVisualizar.'</small>'
    );
}
