<?php

$root = '../../..';

require_once $root.'/utils/response-utils.php';
forbidMethodsNot('GET');
require_once $root.'/dao/UsuarioDAO.php';
require_once $root.'/models/TipoUsuario.php';
UsuarioDAO::validaSessaoTipo(TipoUsuario::ADMINISTRADOR);

require_once $root.'/database/Query.php';

$id_turma = $_GET['id'];

$sql =
'SELECT a.id_usuario, a.nome, a.login, a.ultimo_acesso
   FROM usuario a
   JOIN aluno_em_turma at
     ON at.id_aluno = a.id_usuario
    AND at.id_turma = :id';

$rows = Query::select($sql, [':id' => $id_turma]);

respondJson(HttpCodes::OK, $rows);