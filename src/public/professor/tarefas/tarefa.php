<?php

$root = '../../..';

require_once $root . '/utils/response-utils.php';
forbidMethodsNot('GET');
require_once $root . '/dao/UsuarioDAO.php';
require_once $root . '/models/TipoUsuario.php';
//UsuarioDAO::validaSessaoTipo(TipoUsuario::PROFESSOR); // FIXME dá erro
UsuarioDAO::validaSessao();

require_once $root . '/public/base/tarefas/tarefa.php';