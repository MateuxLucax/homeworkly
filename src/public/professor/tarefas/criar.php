<?php

$root = '../../..';

require_once $root . '/dao/UsuarioDAO.php';
require_once $root . '/models/TipoUsuario.php';

UsuarioDAO::validaSessao();
//UsuarioDAO::validaSessaoTipo(TipoUsuario::PROFESSOR);  // FIXME isso tá dando erro no login aqui

require $root . '/public/base/tarefas/criar.php';