<?php

$root = '../../..';

require_once $root . '/utils/response-utils.php';
forbidMethodsNot('PUT');
require_once $root . '/dao/UsuarioDAO.php';
require_once $root . '/models/TipoUsuario.php';
UsuarioDAO::validaSessaoTipo(TipoUsuario::ALUNO);

// -------------------------------------------------------

require_once $root . '/models/Entrega.php';
require_once $root . '/dao/EntregaDAO.php';
require_once $root . '/utils/DateUtil.php';

try
{

    if (empty($_GET['tarefa']) || empty($_GET['aluno'])) respondJson(
        HttpCodes::BAD_REQUEST,
        ['message' => 'Não foram informados os IDs da tarefa e do aluno']
    );

    $idTarefa = $_GET['tarefa'];
    $idAluno = $_GET['aluno'];

    //
    // Verifica se aluno pode alterar entrega
    //

    // TODO fazer as mesmas verificações que em criar.php
    // mas na verdade fazer num objeto PermissaoEntrega
    // para reutilizar nas duas situações

    $entrega = EntregaDAO::buscar($idAluno, $idTarefa);

    if ($entrega == null) respondJson(
        HttpCodes::NOT_FOUND,
        ['message' => 'Não existe entrega feita pelo aluno de ID '.$idAluno.' na tarefa de ID '.$idTarefa ]
    );

    if ($entrega->emDefinitivo()) respondJson(
        HttpCodes::UNAUTHORIZED,
        ['message' => 'A entrega não pode ser alterada pois já foi feita em definitivo']
    );

    $dados = readJsonRequestBody();

    $entrega->setConteudo($dados['conteudo']);
    $entrega->setDataHora(DateUtil::toLocalDateTime('now'));

    $ok = EntregaDAO::alterar($entrega);

    if ($ok) respondJson(
        HttpCodes::OK,
        ['message' => 'Entrega atualizada com sucesso']
    );
    else respondJson(
        HttpCodes::INTERNAL_SERVER_ERROR,
        ['message' => 'Não foi possível atualizar a entrega no banco de dados']
    );
}
catch (Exception $e)
{
    respondJson(
        HttpCodes::INTERNAL_SERVER_ERROR,
        ['message' => 'Ocorreu uma exceção durante a alteração da tarefa', 'exception' => $e]
    );
}