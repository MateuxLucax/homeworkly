<?php

$root = '../../../';

require_once $root.'controllers/UsuarioController.php';
require_once $root.'models/TipoUsuario.php';

UsuarioController::validaSessaoTipo(TipoUsuario::ADMINISTRADOR);

$view['title'] = 'Turmas';

require_once $root.'database/Connection.php';
require_once $root.'database/Query.php';

$view['turmas'] = Query::select('SELECT id_turma AS id, nome, ano FROM turma');

// TODO
// require $root.'/views/admin/turmas/listar.php';