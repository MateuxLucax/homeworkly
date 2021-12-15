<?php

$root = '../../../';

require_once $root . 'dao/UsuarioDAO.php';
require_once $root . 'models/TipoUsuario.php';
require_once $root . 'utils/response-utils.php';

UsuarioDAO::validaSessaoTipo(TipoUsuario::ADMINISTRADOR);

$headers = getallheaders();

if ($headers['Accept'] == 'application/json')
{
    $data = readJsonRequestBody();

    $condicoes = [];
    $params = [];

    $filtros = $data['filtros'] ?? [];

    foreach ($filtros as $coluna => $valor) {
        switch ($coluna) {
        case 'nome':
            $condicoes[] = 'LOWER(nome) like :nome';
            $params[':nome'] = '%'.strtolower($valor).'%';
            break;
        case 'tipo':
            $condicoes[] = 'tipo = :tipo';
            $params[':tipo'] = $valor;
            break;
        }
    }

    $sql = 'SELECT id_usuario, nome, login, tipo FROM usuario';

    if (count($condicoes) > 0) {
        $sql .= ' WHERE ';
        $sql .= join(' AND ', $condicoes);
    }

    $usuarios = Query::select($sql, $params);

    respondJson(HttpCodes::OK, $usuarios);
}
else
{
    $view['title'] = 'Usuários';
    $view['ativo-nav'] = 'usuarios';
    $view['usuarios'] = UsuarioDAO::listarTodos();

    // TODO desabilitar botão de excluir em usuários que não podem ser excluidos por restrições do DB
    // (se for aluno e estiver em turma, se for professor e estiver em disciplina)

    require $root.'/views/usuarios/listar.php';
}
