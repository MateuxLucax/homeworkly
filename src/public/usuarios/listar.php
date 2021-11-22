<?php

$root = '../../';

require_once $root.'controllers/UsuarioController.php';

$view['title'] = 'Usuários';
$view['pode-modificar-usuarios'] = true;  // TODO true quando o tipo do usuário logado for 'Administrador'
$view['usuarios'] = UsuarioController::ListarTodos();

require $root.'/views/usuarios/listar.php';