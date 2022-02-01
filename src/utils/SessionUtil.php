<?php

require_once $root."/models/Usuario.php";

class SessionUtil
{
    // TODO: adicionar turma do aluno na sessao
    static public function usuarioLogado() : Usuario | null {
        if(isset($_SESSION)) {
            if (isset($_SESSION['id_usuario'])) {
                $usuario = new Usuario();
                $usuario->setId($_SESSION['id_usuario']);
                $usuario->setNome($_SESSION['nome']);
                $usuario->setTipo($_SESSION['tipo']);
                return $usuario;
            }
        }
        return null;
    }
}