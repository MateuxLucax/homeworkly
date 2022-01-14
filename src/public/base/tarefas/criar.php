<?php

require_once $root . '/utils/response-utils.php';
require_once $root . '/database/Connection.php';

$pdo = Connection::getInstance();


if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    try
    {
        $dados = readJsonRequestBody();

        $pdo->prepare(
            'INSERT INTO tarefa (id_professor, id_disciplina, titulo, descricao, esforco_minutos, com_nota, abertura, entrega, fechamento)
            VALUES (:idProfessor, :idDisciplina, :titulo, :descricao, :esforcoMinutos, :comNota, :abertura, :entrega, :fechamento)'
        )->execute([
            ':idProfessor'    => $dados['professor'],
            ':idDisciplina'   => $dados['disciplina'],
            ':titulo'         => $dados['titulo'],
            ':descricao'      => $dados['descricao'],
            ':esforcoMinutos' => $dados['esforcoMinutos'],
            ':comNota'        => $dados['comNota'] == 'true',
            ':abertura'       => $dados['abertura'],
            ':entrega'        => $dados['entrega'],
            ':fechamento'     => $dados['fechamento'],
        ]);

        respondJson(HttpCodes::CREATED, ['id' => $pdo->lastInsertId()]);
    }
    catch (Exception $e)
    {
        respondJson(HttpCodes::BAD_REQUEST, ['exception' => $e]);
    }
}
else if ($_SERVER['REQUEST_METHOD'] == 'GET')
{
    $view['titulo'] = 'Criar tarefa';

    if (!isset($_GET['disciplina'])) {
        respondWithNotFoundPage('Erro do sistema: a página de criar tarefa não recebeu a disciplina a qual a tarefa vai pertencer.');
    }

    $id_disciplina = $_GET['disciplina'];

    $res = Query::select(
        'SELECT d.nome as disciplina_nome, t.nome as turma_nome, t.ano
           FROM disciplina d JOIN turma t ON d.id_turma = t.id_turma
          WHERE d.id_disciplina = :id',
        ['id' => $id_disciplina]
    );

    if (count($res) == 0) {
        respondWithNotFoundPage("Não existe uma disciplina com <b>ID $id_disciplina</b>.<br/>Não podemos criar uma tarefa em uma disciplina que não existe");
    }

    $disciplina = $res[0];

    $view['disciplina_id']   = $id_disciplina;
    $view['disciplina_nome'] = $disciplina['disciplina_nome'];
    $view['turma_nome']      = $disciplina['turma_nome'];
    $view['ano']             = $disciplina['ano'];

    $view['professor_id']   = $_SESSION['id_usuario'];
    $view['professor_nome'] = $_SESSION['nome'];

    require_once $root . '/views/tarefas/criar.php';
}
else 
{
    respondJson(HttpCodes::METHOD_NOT_ALLOWED, ['mensagem' => 'Método não '.$_SERVER['RESPONSE_METHOD'].' permitido']);
}