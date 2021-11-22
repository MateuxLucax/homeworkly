<?php

$root = '../../../';

require_once $root . 'controllers/UsuarioController.php';
require_once $root . 'models/TipoUsuario.php';

UsuarioController::validaSessaoTipo(TipoUsuario::ADMINISTRADOR);

$view['title'] = 'Usuários';
$view['pode-modificar-usuarios'] = true;  // TODO true quando o tipo do usuário logado for 'Administrador'
$view['usuarios'] = UsuarioController::listarTodos();

require $root.'/views/usuarios/listar.php';