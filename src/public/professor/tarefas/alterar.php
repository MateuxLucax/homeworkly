<?php

$root = '../../..';

require_once $root . '/dao/UsuarioDAO.php';
require_once $root . '/models/TipoUsuario.php';
//UsuarioDAO::validaSessaoTipo(TipoUsuario::PROFESSOR);  // FIXME dá erro
UsuarioDAO::validaSessao();

require $root . '/public/base/tarefas/alterar.php';