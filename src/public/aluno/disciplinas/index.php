<?php
$root = '../../../';
require_once $root . 'utils/response-utils.php';

forbidMethodsNot('GET');
require_once $root . 'dao/UsuarioDAO.php';
require_once $root . 'models/TipoUsuario.php';

UsuarioDAO::validaSessaoTipo(TipoUsuario::ALUNO);

$view['title'] = 'Disciplinas';
$view['content_path'] = 'views/aluno/disciplinas/listar.php';
$view['sidebar_links'] = 'aluno/componentes/sidebar.php';

require_once $root . 'views/componentes/base.php';
