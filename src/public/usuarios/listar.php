<?php

$root = '../..';

require_once $root.'/models/UsuarioDAO.php';

$view['title'] = 'Homeworkly - Usuários';
$view['pode-modificar-usuarios'] = true;  // TODO true quando o tipo do usuário logado for 'Administrador'
$view['usuarios'] = UsuarioDAO::ListarTodos();

require $root.'/views/usuarios/listar.php';