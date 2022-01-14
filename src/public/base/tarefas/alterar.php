<?php

require_once $root . '/utils/response-utils.php';
require_once $root . '/dao/TarefaDAO.php';
require_once $root . '/dao/PermissaoTarefa.php';
require_once $root . '/models/Tarefa.php';
require_once $root . '/models/Usuario.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET')
{
    if (!isset($_GET['id'])) {
        respondWithNotFoundPage('<b>Erro do sistema:</b> a página de alterar tarefa foi acessada sem ser informado o ID da tarefa.');
    }

    $id = $_GET['id'];

    $tarefa = TarefaDAO::buscar($id);

    if ($tarefa == null) {
        respondWithNotFoundPage('Não existe tarefa de <b>ID '.$id.'</b>.');
    }

    $permissao = (new PermissaoTarefa($id))->alterar($_SESSION['id_usuario'], $_SESSION['tipo']);

    if ($permissao == PermissaoTarefa::PODE)
    {
        $disciplina = $tarefa->disciplina();
        $turma = $disciplina->getTurma();

        $view['disciplina_id']   = $disciplina->getId();
        $view['disciplina_nome'] = $disciplina->getNome();
        $view['turma_nome']      = $turma->getNome();
        $view['ano']             = $turma->getAno();
        $view['professor_id']    = $tarefa->professor()->getId();

        $view['titulo'] = 'Alterar tarefa';
        $view['tarefa'] = $tarefa;

        $view['permissao'] = new PermissaoTarefa($tarefa->id());

        require $root . '/views/tarefas/criar.php';
    }
    else 
    {
        $mensagem = match($permissao) {
            PermissaoTarefa::NAO_AUTORIZADO => 'Você não tem autorização para alterar essa tarefa',
            PermissaoTarefa::ARQUIVADA => 'Essa tarefa não pode ser alterada pois é de um ano passado e está arquivada',
            PermissaoTarefa::FECHADA => 'Essa tarefa não pode ser alterada pois já foi fechada',
            default => 'Segundo o sistema, você não pode alterar essa tarefa, mas não sabemos qual o motivo (┬┬﹏┬┬)<br/><small>(Cód: '.$permissao.')</small>',
        };
        respondWithErrorPage(HttpCodes::BAD_REQUEST, 'A tarefa não pode ser alterada', $mensagem);
    }

}
else if ($_SERVER['REQUEST_METHOD'] == 'PUT')
{
    $pdo = Connection::getInstance();

    try
    {
        $dados = readJsonRequestBody();

        $pdo->prepare(
            'UPDATE tarefa
                SET id_professor = :idProfessor
                  , id_disciplina = :idDisciplina
                  , titulo = :titulo
                  , descricao = :descricao
                  , esforco_minutos = :esforcoMinutos
                  , com_nota = :comNota
                  , abertura = :abertura
                  , entrega = :entrega
                  , fechamento = :fechamento
              WHERE id_tarefa = :id'
        )->execute([
            ':id'             => $dados['id'],
            ':idProfessor'    => $dados['professor'],
            ':idDisciplina'   => $dados['disciplina'],
            ':titulo'         => $dados['titulo'],
            ':descricao'      => $dados['descricao'],
            ':esforcoMinutos' => $dados['esforcoMinutos'],
            ':comNota'        => $dados['comNota'] ? 'true' : 'false',
            ':abertura'       => $dados['abertura'],
            ':entrega'        => $dados['entrega'],
            ':fechamento'     => $dados['fechamento'],
        ]);

        respondJson(HttpCodes::OK, ['id' => $dados['id']]);
    }
    catch (Exception $e)
    {
        respondJson(HttpCodes::BAD_REQUEST, ['exception' => $e]);
    }

}
else
{
    respondJson(
        HttpCodes::METHOD_NOT_ALLOWED,
        ['message' => 'Método HTTP não permitido ('.$_SERVER['REQUEST_METOHD'].')']
    );
}
