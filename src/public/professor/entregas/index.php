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
require_once $root . 'dao/PermissaoEntrega.php';

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

$permAvaliar = PermissaoEntrega::avaliar($_SESSION['id_usuario'], $_SESSION['tipo'], $tarefa);
if ($permAvaliar != PermissaoEntrega::PODE && $permAvaliar != PermissaoEntrega::ARQUIVADA) {
    list($codigo, $titulo, $motivo) = match($permAvaliar) {
        PermissaoEntrega::NAO_EH_PROFESSOR => [HttpCodes::UNAUTHORIZED, 'Não autorizado', 'não é um usuário do tipo professor'],
        PermissaoEntrega::NAO_EH_DA_DISCIPLINA => [HttpCodes::UNAUTHORIZED, 'Não autorizado', 'não é um professor da disciplina'],
        PermissaoEntrega::ESPERANDO_ABERTURA => [HttpCodes::BAD_REQUEST, 'Tarefa esperando abertura', 'a tarefa ainda não foi aberta'],
        default => [HttpCodes::INTERNAL_SERVER_ERROR, 'Erro do servidor', 'ErroMatchAvaliarNaoExaustivo']
    };
    respondWithErrorPage($codigo, $titulo, 'O usuário não pode avaliar as entregas pois '.$motivo);
}


$podeAvaliarEntregas = $permAvaliar == PermissaoEntrega::PODE;

$entregasPorAluno = EntregaDAO::entregasPorAluno($tarefa);

// ------------------------------

$view['tarefa'] = $tarefa;
$view['entregasPorAluno'] = $entregasPorAluno;

require $root . 'views/professor/entregas/index.php';