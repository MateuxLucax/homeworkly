<?php

require_once $root."/models/Usuario.php";
require_once $root."/database/Query.php";
require_once $root."/utils/PasswordUtil.php";
require_once $root."/exceptions/UnauthorizedException.php";
require_once $root."/exceptions/UserNotFoundException.php";

// TODO trazer as datas de cadastro, último acesso também

class UsuarioDAO
{
    public static function registrar(Usuario $usuario) : bool {
        $sql = "INSERT INTO usuario(tipo, nome, login, hash_senha, cadastro)
                VALUES (:tipo, :nome, :login, :hash_senha, CURRENT_TIMESTAMP)";

        $params = [
            ':tipo' => $usuario->getTipo(),
            ':nome' => $usuario->getNome(),
            ':login' => $usuario->getLogin(),
            ':hash_senha' => $usuario->getHashSenha()
        ];

        return Query::execute($sql, $params);
    }

    /**
     * @throws UnauthorizedException
     * @throws UserNotFoundException
     * @throws QueryException
     */
    public static function login(Usuario $usuario, string $senha) : Usuario {
        $sql = "SELECT * FROM usuario WHERE login = :login";

        $params = [
            ':login' => $usuario->getLogin()
        ];

        $foundUser = Query::select($sql, $params);

        if (empty($foundUser)) {
            throw new UserNotFoundException();
        }

        $foundUser = $foundUser[0];

        if (!PasswordUtil::validate($senha, $foundUser['hash_senha'])) {
            throw new UnauthorizedException();
        }

        $usuario->setId($foundUser['id_usuario']);
        $usuario->setTipo($foundUser['tipo']);
        $usuario->setNome($foundUser['nome']);
        $usuario->setLogin($foundUser['login']);
        $usuario->setHashSenha($foundUser['hash_senha']);
        // TODO: Parse timestamp to dateTime
        // $usuario->setCadastro(DateUtil::parseTimestamp($foundUser['cadastro']));
        // $usuario->setUltimoAcesso($foundUser['ultimo_acesso']);

        self::criarSessao($usuario);

        return $usuario;
    }

    public static function buscarTodos(): array {
        return array_map(
            fn($row) => self::armazenarPodeExcluir((new Usuario)
                ->setId($row['id_usuario'])
                ->setNome($row['nome'])
                ->setTipo($row['tipo'])   // valor das enums coincide com o que é armazenado diretamente no banco
                ->setLogin($row['login'])),
            Query::select('SELECT id_usuario, nome, tipo, login FROM usuario')
        );
    }

    public static function validaSessao() : bool {
        session_start();
        if (isset($_SESSION['id_usuario'])) {
            return true;
        }

        header("location: http://" .  $_SERVER["HTTP_HOST"] . "/entrar");
        return false;
    }

    public static function sair() : bool {
        self::removerSessao();

        header("location: http://" . $_SERVER["HTTP_HOST"]);
        return true;
    }

    private static function criarSessao(Usuario $usuario) : void {
        session_start();
        $_SESSION['id_usuario'] = $usuario->getId();
        $_SESSION['nome'] = $usuario->getNome();
        $_SESSION['tipo'] = $usuario->getTipo();
    }

    private static function removerSessao() : void {
        session_start();
        unset($_SESSION['id_usuario']);
        unset($_SESSION['nome']);
        unset($_SESSION['tipo']);
        session_destroy();
    }

    public static function validaSessaoTipo(string $tipo) : void {
        if (self::validaSessao()) {
            $tipoSessao = (string) $_SESSION['tipo'];
            if ($tipoSessao != TipoUsuario::ADMINISTRADOR && $tipoSessao != $tipo) {
                self::sair();
            }
        }
    }

    public static function armazenarPodeExcluir(Usuario $usuario): Usuario
    {
        switch ($usuario->getTipo()) {
            case TipoUsuario::ADMINISTRADOR:
                return $usuario->setPodeExcluir(false);
            case TipoUsuario::PROFESSOR:
                $sql = 'SELECT NOT EXISTS(SELECT 1 FROM professor_de_disciplina WHERE id_professor = :id) AS pode_excluir';
                $res = Query::select($sql, [':id' => $usuario->getId()]);
                return $usuario->setPodeExcluir($res[0]['pode_excluir']);
            case TipoUsuario::ALUNO:
                $sql = 'SELECT NOT EXISTS(SELECT 1 FROM aluno_em_turma WHERE id_aluno = :id) AS pode_excluir';
                $res = Query::select($sql, [':id' => $usuario->getId()]);
                return $usuario->setPodeExcluir($res[0]['pode_excluir']);
        }
    }

    public static function buscarAlunosDeTurma(int $idTurma): array
    {
        $rows = Query::select(
            'SELECT al.id_usuario AS id, al.nome, al.login, al.ultimo_acesso
               FROM usuario al
               JOIN aluno_em_turma alt
                 ON alt.id_aluno = al.id_usuario
                AND alt.id_turma = :idTurma',
            [':idTurma' => $idTurma]
        );

        return array_map(
            fn($row) => self::armazenarPodeExcluir(
                (new Usuario)
                ->setId($row['id'])
                ->setNome($row['nome'])
                ->setLogin($row['login'])
                ->setUltimoAcesso($row['ultimo_acesso'])
                ->setTipo(TipoUsuario::ALUNO)),
            $rows
        );
    }

    public static function buscarProfessoresDeDisciplina(int $idDisciplina): array
    {
        $rows = Query::select(
            'SELECT p.id_usuario AS id, p.nome, p.login, p.ultimo_acesso
               FROM usuario p
               JOIN professor_de_disciplina pd
                 ON pd.id_professor = p.id_usuario
                AND pd.id_disciplina = :idDisciplina',
            [':idDisciplina' => $idDisciplina]
        );

        return array_map(
            fn($row) => self::armazenarPodeExcluir((new Usuario)
                ->setId($row['id'])
                ->setNome($row['nome'])
                ->setLogin($row['login'])
                ->setUltimoAcesso($row['ultimo_acesso'])
                ->setTipo(TipoUsuario::PROFESSOR)),
            $rows
        );
    }

    public static function alterar(Usuario $usuario): Usuario
    {
        Query::execute('UPDATE usuario SET nome = :nome, login = :login WHERE id_usuario = :id', [
            ':id'    => $usuario->getId(),
            ':nome'  => $usuario->getNome(),
            ':login' => $usuario->getLogin()
        ]);
        return $usuario;
    }

    public static function alterarSenha(Usuario $usuario): Usuario
    {
        Query::execute('UPDATE usuario SET hash_senha = :hashSenha WHERE id_usuario = :id', [
            ':id'        => $usuario->getId(),
            ':hashSenha' => $usuario->getHashSenha()
        ]);
        return $usuario;
    }

    public static function excluir(Usuario $usuario): void
    {
        Query::execute('DELETE FROM usuario WHERE id_usuario = :id', [':id' => $usuario->getId()]);
    }
}