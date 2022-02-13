<?php

require_once $root."/models/Usuario.php";
require_once $root."/database/Query.php";
require_once $root."/utils/PasswordUtil.php";
require_once $root."/utils/DateUtil.php";
require_once $root."/exceptions/UnauthorizedException.php";
require_once $root."/exceptions/UserNotFoundException.php";

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

        $usuario
            ->setId($foundUser['id_usuario'])
            ->setTipo($foundUser['tipo'])
            ->setNome($foundUser['nome'])
            ->setLogin($foundUser['login'])
            ->setHashSenha($foundUser['hash_senha'])
            ->setCadastro(DateUtil::toLocalDateTime($foundUser['cadastro']))
            ->setUltimoAcesso(is_null($foundUser['ultimo_acesso']) ? null : DateUtil::toLocalDateTime($foundUser['ultimo_acesso']));

        self::criarSessao($usuario);

        return $usuario;
    }

    public static function buscarTodos(): array {
        return array_map(
            fn($row) => self::armazenarPodeExcluir((new Usuario)
                ->setId($row['id_usuario'])
                ->setNome($row['nome'])
                ->setTipo($row['tipo'])
                ->setLogin($row['login']))
                ->setCadastro(DateUtil::toLocalDateTime($row['cadastro']))
                ->setUltimoAcesso(is_null($row['ultimo_acesso']) ? null : DateUtil::toLocalDateTime($row['ultimo_acesso'])),
            Query::select('SELECT id_usuario, nome, tipo, login, cadastro, ultimo_acesso FROM usuario')
        );
    }

    public static function validaSessao() : bool {
        if(!isset($_SESSION)) {
            session_start();
        }

        if (isset($_SESSION['id_usuario'])) {
            return true;
        }

        header("location: /entrar");
        return false;
    }

    public static function sair() : bool {
        self::removerSessao();

        header("location: /entrar");
        return true;
    }

    private static function criarSessao(Usuario $usuario) : void {
        session_start();
        $_SESSION['id_usuario'] = $usuario->getId();
        $_SESSION['nome'] = $usuario->getNome();
        $_SESSION['tipo'] = $usuario->getTipo();
    }

    private static function removerSessao() : void {
        if (!isset($_SESSION)) {
            session_start();
        }
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

    public static function validaSessaoTipos(array $tipos) : void {
        if (self::validaSessao()) {
            $tipoSessao = (string) $_SESSION['tipo'];
            if (in_array(TipoUsuario::ADMINISTRADOR, $tipos) || !in_array($tipoSessao, $tipos)) {
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

    public static function alunoDaTurma(int $idUsuario, int $idTurma): bool
    {
        return (bool) Query::select(
            'SELECT EXISTS(
                SELECT 1
                  FROM aluno_em_turma
                 WHERE (id_aluno, id_turma) = (:idUsuario, :idTurma)
            ) AS aluno_da_turma',
            [ ':idUsuario' => $idUsuario,
              ':idTurma' => $idTurma ]
        )[0]['aluno_da_turma'];
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

    /**
     * @throws QueryException
     * @throws UserNotFoundException
     * @throws UnauthorizedException
     */
    public static function validaSenha(Usuario $usuario): bool
    {
        $sql = "SELECT * FROM usuario WHERE id_usuario = :id";

        $params = [
            ':id' => $usuario->getId()
        ];

        $foundUser = Query::select($sql, $params);

        if (empty($foundUser)) {
            throw new UserNotFoundException();
        }

        $foundUser = $foundUser[0];

        if (!PasswordUtil::validate($usuario->getHashSenha(), $foundUser['hash_senha'])) {
            throw new UnauthorizedException();
        }
        return true;
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

    public static function buscarUsuario(Usuario $usuario): Usuario
    {
        return array_map(
            fn($row) => self::armazenarPodeExcluir((new Usuario)
                ->setId($row['id_usuario'])
                ->setNome($row['nome'])
                ->setTipo($row['tipo'])
                ->setLogin($row['login']))
                ->setCadastro(DateUtil::toLocalDateTime($row['cadastro']))
                ->setUltimoAcesso(is_null($row['ultimo_acesso']) ? null : DateUtil::toLocalDateTime($row['ultimo_acesso'])),
            Query::select('SELECT id_usuario, nome, tipo, login, cadastro, ultimo_acesso FROM usuario WHERE id_usuario = :id', [':id' => $usuario->getId()])
        )[0];
    }

    public static function professorDaDisciplina(int $idProfessor, int $idDisciplina): bool
    {
        return (bool) Query::select(
            'SELECT EXISTS(
                SELECT 1
                  FROM professor_de_disciplina
                 WHERE (id_professor, id_disciplina) = (:idProf, :idDisc)
            ) AS professor_da_disciplina', [
                ':idProf' => $idProfessor,
                ':idDisc' => $idDisciplina
            ]
        )[0]['professor_da_disciplina'];
    }
}