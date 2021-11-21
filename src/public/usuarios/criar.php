<?php

$root = '../..';

require $root.'/classes/Connection.php';

$DB = Connection::getInstance();

$pstmt = $DB->prepare('INSERT INTO usuario (nome, tipo, login, hash_senha) VALUES (:nome, :tipo, :login, :senha)');
$pstmt->bindValue(':nome', $_POST['nome']);
$pstmt->bindValue(':tipo', $_POST['tipo']);
$pstmt->bindValue(':login', $_POST['login']);
$pstmt->bindValue(':senha', hash('sha256', $_POST['nome']));
$pstmt->execute();

header('Location: listar');