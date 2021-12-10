<?php

$root = '../../../';

require_once $root.'dao/UsuarioDAO.php';
require_once $root.'models/TipoUsuario.php';
require_once $root.'database/Query.php';

UsuarioDAO::validaSessaoTipo(TipoUsuario::ADMINISTRADOR);

$view['title'] = 'Turmas';
$view['turmas'] = Query::select('SELECT id_turma AS id, nome, ano FROM turma');

require $root.'views/turmas/listar.php';