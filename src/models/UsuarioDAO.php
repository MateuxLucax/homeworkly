<?php

require_once "Usuario.php";
require_once "../database/StatementBuilder.php";
require_once "../utils/PasswordUtil.php";
require_once "../exceptions/UnauthorizedException.php";

class UsuarioDAO
{

    public static function Registrar(Usuario $usuario) : bool {
        $sql = "INSERT INTO usuario(tipo, nome, login, hash_senha)
                VALUES (:tipo, :nome, :login, :hash_senha)";

        $params = [
            'tipo' => $usuario->getTipo(),
            'nome' => $usuario->getNome(),
            'login' => $usuario->getLogin(),
            'hash_senha' => PasswordUtil::hash($usuario->getHashSenha())
        ];

        return StatementBuilder::insert($sql, $params);
    }

    /**
     * @throws UnauthorizedException
     */
    public static function Login(Usuario $usuario) : Usuario {
        $sql = "SELECT * FROM usuario WHERE login = :login";

        $params = [
            'login' => $usuario->getLogin()
        ];

        $foundUser = StatementBuilder::select($sql, $params);

        if (!PasswordUtil::validate($usuario->getHashSenha(), $foundUser['hash_senha'])) {
            throw new UnauthorizedException();
        }

        return self::Populate($foundUser);
    }

    private static function Populate(array $usuario) : Usuario {
        $usuario = new Usuario;

        $usuario->setIdUsuario($usuario['id_usuario']);
        $usuario->setTipo($usuario['tipo']);
        $usuario->setNome($usuario['nome']);
        $usuario->setLogin($usuario['login']);
        $usuario->setCadastro($usuario['cadastro']);
        $usuario->setUltimoAcesso($usuario['ultimo_acesso']);

        return $usuario;
    }

    public static function ListarTodos() : array {
        $sql = "SELECT id_usuario AS id, nome, tipo, login FROM usuario";

        return StatementBuilder::select($sql);
    }
}