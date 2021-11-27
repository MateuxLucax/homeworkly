<?php

$root = '../../../';

require_once $root . 'controllers/UsuarioController.php';
require_once $root . 'models/TipoUsuario.php';

UsuarioController::validaSessaoTipo(TipoUsuario::ADMINISTRADOR);

$view['title'] = 'Usuários';
$view['usuarios'] = UsuarioController::listarTodos();

require $root.'/views/usuarios/listar.php';