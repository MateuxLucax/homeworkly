<?php

$root = '../../../';
require_once $root . 'utils/response-utils.php';
forbidMethodsNot('GET');
require_once $root . 'dao/UsuarioDAO.php';
require_once $root . 'models/TipoUsuario.php';
UsuarioDAO::validaSessaoTipo(TipoUsuario::PROFESSOR);

// ------------------------------

require_once $root . 'models/Tarefa.php';
require_once $root . 'models/Entrega.php';
require_once $root . 'dao/TarefaDAO.php';
require_once $root . 'dao/EntregaDAO.php';

if (!isset($_GET['tarefa'])) respondWithErrorPage(
    HttpCodes::BAD_REQUEST,
    'Erro do sistema',
    'A página de entregas foi acessada sem uma tarefa ser fornecida'
);

$idTarefa = $_GET['tarefa'];
$tarefa = TarefaDAO::buscar($idTarefa);

if ($tarefa == null) respondWithNotFoundPage(
    'Não existe tarefa de ID ' . $idTarefa
);

$professorDaDisciplina = (bool) Query::select(
    'SELECT EXISTS(
        SELECT 1
          FROM professor_de_disciplina
         WHERE (id_professor, id_disciplina) = (:idProf, :idDisc)
    ) AS professor_da_disciplina', [
        ':idProf' => $_SESSION['id_usuario'],
        ':idDisc' => $tarefa->disciplina()->getId()
    ]
)[0]['professor_da_disciplina'];

$professorDaDisciplina = true;

if (!$professorDaDisciplina) respondWithErrorPage(
    HttpCodes::UNAUTHORIZED,
    'Não autorizado',
    'Você não é um professor da disciplina dessa tarefa, então não pode visualizar suas entregas'
);

$podeAvaliarEntregas = $tarefa->estado() != TarefaEstado::ARQUIVADA;

$entregasPorAluno = EntregaDAO::entregasPorAluno($tarefa);

// ------------------------------

$view['tarefa'] = $tarefa;
$view['podeAvaliarEntregas'] = $podeAvaliarEntregas;
$view['entregasPorAluno'] = $entregasPorAluno;

require $root . 'views/professor/entregas/index.php';