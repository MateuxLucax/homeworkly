<?php

$root = '../../';
require_once $root . 'utils/response-utils.php';

forbidMethodsNot('GET');
require_once $root . 'dao/UsuarioDAO.php';
require_once $root . 'models/TipoUsuario.php';
require_once $root.'utils/SessionUtil.php';

UsuarioDAO::validaSessaoTipos([TipoUsuario::ALUNO, TipoUsuario::PROFESSOR]);
$usuario = SessionUtil::usuarioLogado();
$usuario = UsuarioDAO::buscarUsuario($usuario);

$view['title'] = 'Perfil';
$view['content_path'] = 'views/componentes/perfil.php';
$view['sidebar_links'] = 'professor/componentes/sidebar.php';
$view['perfil_nome'] = $usuario->getNome();
$view['perfil_login'] = $usuario->getLogin();
$view['perfil_id_usuario'] = $usuario->getId();
$view['perfil_alterar_senha'] = '/base/usuario/alterar-senha';

require_once $root . 'views/componentes/base.php';