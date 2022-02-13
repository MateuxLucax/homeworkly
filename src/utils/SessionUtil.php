<?php

require_once $root."/models/Usuario.php";

class SessionUtil
{
    // TODO: adicionar turma do aluno na sessao
    static public function usuarioLogado() : Usuario | null {
        if (!isset($_SESSION)) return null;
        if (!isset($_SESSION['id_usuario'])) return null;
        return (new Usuario)
            ->setId($_SESSION['id_usuario'])
            ->setNome($_SESSION['nome'])
            ->setTipo($_SESSION['tipo']);
    }
}