<?php

$root = '../../../';

require_once $root.'controllers/UsuarioController.php';
require_once $root.'models/TipoUsuario.php';
require_once $root.'database/Query.php';

UsuarioController::validaSessaoTipo(TipoUsuario::ADMINISTRADOR);

$view['title'] = 'Turmas';
$view['turmas'] = Query::select('SELECT id_turma AS id, nome, ano FROM turma');

require $root.'views/turmas/listar.php';