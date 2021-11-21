<?php

$root = '../..';

require_once $root.'/models/Usuario.php';
require_once $root.'/models/UsuarioDAO.php';

$usuario = new Usuario();
$usuario->setNome($_POST['nome']);
$usuario->setTipo($_POST['tipo']);
$usuario->setHashSenha($_POST['senha']);
$usuario->setLogin($_POST['login']);

UsuarioDAO::Registrar($usuario);

header('Location: listar');