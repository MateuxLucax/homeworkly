<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/../models/Usuario.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/../database/Query.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/../utils/PasswordUtil.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/../exceptions/UnauthorizedException.php";

class UsuarioDAO
{

    public static function Registrar(Usuario $usuario) : bool {
        $sql = "INSERT INTO usuario(tipo, nome, login, hash_senha)
                VALUES (:tipo, :nome, :login, :hash_senha)";

        $params = [
            ':tipo' => $usuario->getTipo(),
            ':nome' => $usuario->getNome(),
            ':login' => $usuario->getLogin(),
            ':hash_senha' => PasswordUtil::hash($usuario->getHashSenha())
        ];

        return Query::execute($sql, $params);
    }

    /**
     * @throws UnauthorizedException
     */
    public static function Login(Usuario $usuario) : Usuario {
        $sql = "SELECT * FROM usuario WHERE login = :login";

        $params = [
            ':login' => $usuario->getLogin()
        ];

        $foundUser = Query::select($sql, $params);

        if (!PasswordUtil::validate($usuario->getHashSenha(), $foundUser['hash_senha'])) {
            throw new UnauthorizedException();
        }

        return self::Populate($foundUser);
    }

    private static function Populate(array $usuario) : Usuario {
        $usuario = new Usuario;

        $usuario->setId($usuario['id_usuario']);
        $usuario->setTipo($usuario['tipo']);
        $usuario->setNome($usuario['nome']);
        $usuario->setLogin($usuario['login']);
        $usuario->setCadastro($usuario['cadastro']);
        $usuario->setUltimoAcesso($usuario['ultimo_acesso']);

        return $usuario;
    }

    public static function ListarTodos() : array {
        $sql = "SELECT id_usuario AS id, nome, tipo, login FROM usuario";

        return Query::select($sql);
    }
}