<?php

$root = '../..';

require_once $root.'/classes/Connection.php';

$DB = Connection::getInstance();

$stmt = $DB->query('SELECT id_usuario AS id, nome, tipo, login FROM usuario', PDO::FETCH_ASSOC);
$usuarios = $stmt->fetchAll();

$view['title'] = 'Homeworkly - Usuários';
$view['usuarios'] = $usuarios;

require $root.'/views/usuarios/listar.php';