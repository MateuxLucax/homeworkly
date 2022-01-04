<?php

$root = '../../..';

require_once $root . '/utils/response-utils.php';
forbidMethodsNot('POST');
require_once $root . '/dao/UsuarioDao.php';
require_once $root . '/models/TipoUsuario.php';
UsuarioDao::validaSessaoTipo(TipoUsuario::ADMINISTRADOR);

require_once $root . '/database/Query.php';

$login = readJsonRequestBody()['login'];
$em_uso = Query::select(
    'SELECT EXISTS(SELECT 1 FROM usuario WHERE login = :login) AS em_uso',
    ['login' => $login]
)[0]['em_uso'];
respondJson(HttpCodes::OK, ['emUso' => $em_uso]);