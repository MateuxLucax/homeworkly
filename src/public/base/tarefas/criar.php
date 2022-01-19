<?php

require_once $root . '/utils/response-utils.php';
require_once $root . '/database/Connection.php';
require_once $root . '/dao/PermissaoTarefa.php';
require_once $root . '/models/Disciplina.php';
require_once $root . '/dao/DisciplinaDAO.php';
require_once $root . '/models/Tarefa.php';
require_once $root . '/dao/TarefaDAO.php';

/**
 * @return retorna array [codigo, titulo, mensagem] para a response caso a permissão não seja PODE
 */
function retornoPermissao(int $permissao, string $tipoUsuario): array
{
    if ($permissao == PermissaoTarefa::ARQUIVADA)
    {
        return [
            HttpCodes::BAD_REQUEST,
            'Disciplina arquivada',
            'Você não pode criar uma tarefa nessa disciplina pois ela é de um ano passado e está arquivada'
        ];
    }
    else if ($permissao == PermissaoTarefa::NAO_AUTORIZADO)
    {
        $mensagemBase = "Você não está autorizado a criar uma tarefa nessa disciplina pois ";
        return match($tipoUsuario) {
            TipoUsuario::PROFESSOR => [
                HttpCodes::UNAUTHORIZED,
                'Não autorizado',
                $mensagemBase .= 'não é um professor da disciplina',
            ],
            TipoUsuario::ALUNO => [
                HttpCodes::UNAUTHORIZED,
                'Não autorizado',
                $mensagemBase .= 'é um aluno, e alunos não podem criar tarefas'
            ],
            default => [
                HttpCodes::INTERNAL_SERVER_ERROR,
                'Erro do sistema',
                'Segundo o sistema, você não está autorizado a criar uma tarefa, mas é um administrador e portanto deveria estar autorizado'
            ],
        };
    }
    else
    {
        return [
            HttpCodes::INTERNAL_SERVER_ERROR,
            'Erro do sistema',
            'Segundo o sistema, você não pode criar uma tarefa, mas não sabemos o motivo<br/><small>Cód.: '.$permissao.'</small>'
        ];
    }
}

// -------------------------------------------------------

$pdo = Connection::getInstance();

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    try
    {
        $dados = readJsonRequestBody();

        $permissao = PermissaoTarefa::criar($_SESSION['id_usuario'], $_SESSION['tipo'], $dados['disciplina']);

        if ($permissao != PermissaoTarefa::PODE) {
            list($codigo, $_, $mensagem) = retornoPermissao($permissao, $_SESSION['tipo']);
            respondJson($codigo, ['message' => $mensagem]);
        }

        $pdo->prepare(
            'INSERT INTO tarefa (id_professor, id_disciplina, titulo, descricao, esforco_minutos, com_nota, abertura, entrega, fechamento)
            VALUES (:idProfessor, :idDisciplina, :titulo, :descricao, :esforcoMinutos, :comNota, :abertura, :entrega, :fechamento)'
        )->execute([
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

        respondJson(HttpCodes::CREATED, ['id' => $pdo->lastInsertId()]);
    }
    catch (Exception $e)
    {
        respondJson(
            HttpCodes::BAD_REQUEST,
            [ 'message' => 'Ocorreu uma exceção durante a criação da tarefa',
              'exception' => $e ]
        );
    }
}
else if ($_SERVER['REQUEST_METHOD'] == 'GET')
{
    $view['titulo'] = 'Criar tarefa';

    if (!isset($_GET['disciplina'])) {
        respondWithNotFoundPage('Erro do sistema: a página de criar tarefa não recebeu a disciplina a qual a tarefa vai pertencer.');
    }

    $idDisciplina = $_GET['disciplina'];

    $disciplina = DisciplinaDAO::buscar($idDisciplina);

    if ($disciplina == null) {
        respondWithNotFoundPage("Não existe uma disciplina com <b>ID $idDisciplina</b>.<br/>Não podemos criar uma tarefa em uma disciplina que não existe");
    }

    $permissao = PermissaoTarefa::criar($_SESSION['id_usuario'], $_SESSION['tipo'], $idDisciplina);

    if ($permissao != PermissaoTarefa::PODE) {
        list($codigo, $titulo, $mensagem) = retornoPermissao($permissao, $_SESSION['tipo']);
        respondWithErrorPage($codigo, $titulo, nl2br($mensagem));
    }

    $view['disciplina'] = $disciplina;
    $view['turma'] = $disciplina->getTurma();

    $view['professor_id']   = $_SESSION['id_usuario'];
    $view['professor_nome'] = $_SESSION['nome'];

    require_once $root . '/views/tarefas/criar.php';
}
else 
{
    respondJson(
        HttpCodes::METHOD_NOT_ALLOWED,
        ['message' => 'Método não '.$_SERVER['RESPONSE_METHOD'].' permitido']
    );
}
