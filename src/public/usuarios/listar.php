<?php

$root = '../..';

require_once $root.'/classes/Connection.php';

$DB = Connection::getInstance();

$stmt = $DB->query('SELECT id_usuario AS id, nome, tipo, login FROM usuario', PDO::FETCH_ASSOC);
$usuarios = $stmt->fetchAll();

$view['title'] = 'Homeworkly - Usuários';
$view['pode-modificar-usuarios'] = true;  // TODO true quando o tipo do usuário logado for 'Administrador'
$view['usuarios'] = $usuarios;

require $root.'/views/usuarios/listar.php';