<?php

$root = '../../..';

require_once $root . '/dao/UsuarioDAO.php';
require_once $root . '/models/TipoUsuario.php';

UsuarioDAO::validaSessaoTipo(TipoUsuario::PROFESSOR);

require_once $root . '/base/tarefas/excluir.php';