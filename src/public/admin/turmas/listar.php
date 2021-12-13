<?php

$root = '../../../';

require_once $root.'dao/UsuarioDAO.php';
require_once $root.'dao/TurmaDAO.php';
require_once $root.'models/TipoUsuario.php';
require_once $root.'database/Query.php';

UsuarioDAO::validaSessaoTipo(TipoUsuario::ADMINISTRADOR);

$view['title'] = 'Turmas';
$view['ativo-nav'] = 'turmas';
$view['turmas'] = TurmaDAO::buscarTodas();

require $root.'views/turmas/listar.php';