<?php

$root = '../../../';

require_once $root.'dao/UsuarioDAO.php';
require_once $root.'dao/TurmaDAO.php';
require_once $root.'models/TipoUsuario.php';
require_once $root.'database/Query.php';
require_once $root.'utils/response-utils.php';

UsuarioDAO::validaSessaoTipo(TipoUsuario::ADMINISTRADOR);

$headers = getallheaders();
if ($headers['Accept'] == 'application/json')
{

    // TODO não agrupar por ano no servidor, mas no lado do cliente mesmo

    try {
        $dados = readJsonRequestBody();

        $turmas = Query::select('SELECT id_turma AS id, nome, ano FROM turma');

        if (isset($dados['agrupar_por'])) {
            $col = $dados['agrupar_por'];
            $retorno = [];
            foreach ($turmas as $turma)
                $retorno[$turma[$col]][] = $turma;
        } else {
            $retorno = $turmas;
        }
        
        respondJson(HttpCodes::OK, $retorno);
    } catch (Exception $e) {
        respondJson(HttpCodes::BAD_REQUEST, ['exception' => $e]);
    }
}
else
{
    $view['title'] = 'Turmas';
    $view['ativo-nav'] = 'turmas';
    $view['turmas'] = TurmaDAO::buscarTodas();

    require $root.'views/turmas/listar.php';
}
