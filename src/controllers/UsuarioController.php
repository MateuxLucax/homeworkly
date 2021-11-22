<?php

$root = $_SERVER['DOCUMENT_ROOT'] . "/../";

require_once $root . "models/Usuario.php";
require_once $root . "database/Query.php";
require_once $root . "utils/PasswordUtil.php";
require_once $root . "exceptions/UnauthorizedException.php";
require_once $root . "exceptions/UserNotFoundException.php";

class UsuarioController
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
     * @throws UserNotFoundException
     */
    public static function Login(Usuario $usuario) : Usuario {
        $sql = "SELECT * FROM usuario WHERE login = :login";

        $params = [
            ':login' => $usuario->getLogin()
        ];

        $foundUser = Query::select($sql, $params);

        if (empty($foundUser)) {
            throw new UserNotFoundException();
        }

        $foundUser = $foundUser[0];

        if (!PasswordUtil::validate($usuario->getHashSenha(), $foundUser['hash_senha'])) {
            throw new UnauthorizedException();
        }

        $foundUser = self::Populate($foundUser);

        session_start();
        $_SESSION['id_usuario'] = $foundUser->getId();
        $_SESSION['nome'] = $foundUser->getNome();
        $_SESSION['tipo'] = $foundUser->getTipo();

        return $foundUser;
    }

    private static function Populate(array $data) : Usuario {
        $usuario = new Usuario;

        // TODO: Parse timestamp to dateTime
        $usuario->setId($data['id_usuario']);
        $usuario->setTipo($data['tipo']);
        $usuario->setNome($data['nome']);
        $usuario->setLogin($data['login']);
      //  $usuario->setCadastro(DateUtil::parseTimestamp($data['cadastro']));
      //  $usuario->setUltimoAcesso($data['ultimo_acesso']);

        return $usuario;
    }

    public static function ListarTodos() : array {
        $sql = "SELECT id_usuario AS id, nome, tipo, login FROM usuario";

        return Query::select($sql);
    }
}