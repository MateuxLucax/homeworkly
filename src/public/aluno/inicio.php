<?php
$root = '../../';
require_once $root . 'utils/response-utils.php';

forbidMethodsNot('GET');
require_once $root . 'dao/UsuarioDAO.php';
require_once $root . 'models/TipoUsuario.php';
UsuarioDAO::validaSessaoTipo(TipoUsuario::ALUNO);

$view['title'] = 'Inicio';
$view['content_path'] = 'aluno/inicio.php';
include_once $root . 'views/componentes/base.php';