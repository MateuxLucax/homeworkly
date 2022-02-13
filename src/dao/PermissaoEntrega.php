<?php

require_once $root . '/dao/UsuarioDAO.php';
require_once $root . '/models/Turma.php';
require_once $root . '/models/Disciplina.php';
require_once $root . '/models/Tarefa.php';
require_once $root . '/models/Entrega.php';
require_once $root . '/models/TipoUsuario.php';

class PermissaoEntrega
{
    public const PODE = 0;
    public const NAO_EH_ALUNO = 1;
    public const NAO_EH_DA_TURMA = 2;
    public const ARQUIVADA = 3;
    public const FECHADA = 4;
    public const JA_ENTREGUE = 5;
    public const ESPERANDO_ABERTURA = 6;

    public const NAO_EH_PROFESSOR = 7;
    public const NAO_EH_DA_DISCIPLINA = 8;
    // 1, 2, 7 e 8 podiam ser só um NAO_AUTORIZADO
    
    /**
     * Retorna PODE se o usuário pode alterar a entrega fornecida ou, caso não possa, o motivo disso.
     * 
     * @return int PODE, NAO_EH_ALUNO, NAO_EH_DA_TURMA, ARQUIVADA, FECHADA ou JA_ENTREGUE
     */
    public static function alterar(int $idUsuario, string $tipoUsuario, Entrega $entrega)
    {
        $tarefa = $entrega->tarefa();

        if ($tarefa->estado() == TarefaEstado::ARQUIVADA) return self::ARQUIVADA;
        if ($tipoUsuario == TipoUsuario::ADMINISTRADOR) return self::PODE;
        if ($tipoUsuario == TipoUsuario::PROFESSOR) return self::NAO_EH_ALUNO;
        assert($tipoUsuario == TipoUsuario::ALUNO);
        if (!UsuarioDAO::alunoDaTurma($idUsuario, $tarefa->disciplina()->getTurma()->getId())) {
            return self::NAO_EH_DA_TURMA;
        }
        if ($tarefa->estado() == TarefaEstado::FECHADA) return self::FECHADA;
        if ($entrega->emDefinitivo()) return self::JA_ENTREGUE;
        return self::PODE;
    }

    /**
     * Retorna PODE se o usuário pode criar uma entrega na tarefa fornecida,
     * ou, caso não possa, o motivo disso.
     * 
     * @return int PODE, NAO_EH_ALUNO, NAO_EH_DA_TURMA, ESPERANDO_ABERTURA, ARQUIVADA ou FECHADA
     */
    public static function criar(int $idUsuario, string $tipoUsuario, Tarefa $tarefa)
    {
        if ($tarefa->estado() == TarefaEstado::ARQUIVADA) return self::ARQUIVADA;
        if ($tipoUsuario == TipoUsuario::ADMINISTRADOR) return self::PODE;
        if ($tipoUsuario == TipoUsuario::PROFESSOR) return self::NAO_EH_ALUNO;
        assert($tipoUsuario == TipoUsuario::ALUNO);
        if (!UsuarioDAO::alunoDaTurma($idUsuario, $tarefa->disciplina()->getTurma()->getId())) {
            return self::NAO_EH_DA_TURMA;
        }
        $estado = $tarefa->estado();
        if ($estado == TarefaEstado::ESPERANDO_ABERTURA) return self::ESPERANDO_ABERTURA;
        if ($estado == TarefaEstado::FECHADA) return self::FECHADA;
        return self::PODE;
    }

    /**
     * Retorna se o usuário dado pode avaliar as entregas da tarefa dada.
     * 
     * @return int NAO_EH_PROFESSOR, NAO_EH_DA_DISCIPLINA, ESPERANDO_ABERTURA, ARQUIVADA ou PODE
     */
    public static function avaliar(int $idUsuario, string $tipoUsuario, Tarefa $tarefa)
    {
        $idDisciplina = $tarefa->disciplina()->getId();
        $estado = $tarefa->estado();

        if ($tipoUsuario != TipoUsuario::PROFESSOR) return self::NAO_EH_PROFESSOR;
        if (!UsuarioDAO::professorDaDisciplina($idUsuario, $idDisciplina)) return self::NAO_EH_DA_DISCIPLINA;
        if ($estado == TarefaEstado::ESPERANDO_ABERTURA) return self::ESPERANDO_ABERTURA;
        if ($estado == TarefaEstado::ARQUIVADA) return self::ARQUIVADA;
        return self::PODE;
    }
}