<?php

$root = $_SERVER['DOCUMENT_ROOT'] . '/../';

require_once $root. 'dao/UsuarioDAO.php';

UsuarioDAO::sair();
