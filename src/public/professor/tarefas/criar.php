<?php

$root = '../../../';

require_once $root . 'utils/response-utils.php';
require_once $root . 'database/Connection.php';
require_once $root . 'dao/PermissaoTarefa.php';
require_once $root . 'models/Disciplina.php';
require_once $root . 'dao/DisciplinaDAO.php';
require_once $root . 'models/Tarefa.php';
require_once $root . 'dao/TarefaDAO.php';

UsuarioDAO::validaSessaoTipo(TipoUsuario::PROFESSOR);



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

$view['titulo'] = 'Criar tarefa';
$view['content_path'] = 'views/tarefas/criar.php';
$view['sidebar_links'] = 'professor/componentes/sidebar.php';

require_once $root . 'views/componentes/base.php';