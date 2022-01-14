<?php

require_once $root . '/models/Tarefa.php';
require_once $root . '/models/TipoUsuario.php';

// TODO documentar possíveis retornos de cada função, facilitando que quem chamar seja exaustivo

class PermissaoTarefa
{
    private int $idDisciplina;
    private int $idTurma;
    private int $idProfessor;
    private bool $arquivada;
    private bool $temEntregas;
    private bool $fechada;

    public const PODE = 0;
    public const ARQUIVADA = 1;
    public const TEM_ENTREGAS = 2;
    public const NAO_AUTORIZADO = 3;
    public const FECHADA = 4;

    public function __construct(int $idTarefa)
    {
        $dados = Query::select(
            'SELECT ta.id_professor
                  , ta.fechada OR (ta.fechamento IS NOT NULL AND ta.fechamento < CURRENT_TIMESTAMP) AS fechada
                  , di.id_disciplina
                  , tu.id_turma
                  , tu.ano != :ano AS arquivada
                  , EXISTS(SELECT 1 FROM entrega WHERE id_tarefa = :id) as tem_entregas
               FROM tarefa ta
               JOIN disciplina di ON ta.id_disciplina = di.id_disciplina
               JOIN turma tu ON di.id_turma = tu.id_turma
              WHERE ta.id_tarefa = :id',
            [ ':id' => $idTarefa,
              ':ano' => date('Y') ]
        );
        $dados = $dados[0];
        $this->idProfessor = $dados['id_professor'];
        $this->idDisciplina = $dados['id_disciplina'];
        $this->idTurma = $dados['id_turma'];
        $this->fechada = $dados['fechada'];
        $this->arquivada = $dados['arquivada'];
        $this->temEntregas = $dados['tem_entregas'];
    }

    // TODO método static criar
    // recebe usuário (id e tipo), disciplina e turma

    public function alterar(int $idUsuario, string $tipoUsuario): int
    {
        if ($tipoUsuario == TipoUsuario::ADMINISTRADOR) return self::PODE;
        if ($tipoUsuario == TipoUsuario::ALUNO) return self::NAO_AUTORIZADO;
        assert($tipoUsuario == TipoUsuario::PROFESSOR);
        if ($this->idProfessor != $idUsuario) return self::NAO_AUTORIZADO;
        if ($this->arquivada) return self::ARQUIVADA;
        if ($this->fechada) return self::FECHADA;
        return self::PODE;
    }

    public function excluir(int $idUsuario, string $tipoUsuario): int
    {
        if ($tipoUsuario == TipoUsuario::ALUNO) return self::NAO_AUTORIZADO;
        // Nem admin pode excluir se tiver entregas porque nem o BD vai deixar (fere FK id_tarefa da tabela de entrega)
        if ($this->temEntregas) return self::TEM_ENTREGAS;
        if ($tipoUsuario == TipoUsuario::ADMINISTRADOR) return self::PODE;
        assert($tipoUsuario == TipoUsuario::PROFESSOR);
        if ($this->idProfessor != $idUsuario) return self::NAO_AUTORIZADO;
        if ($this->arquivada) return self::ARQUIVADA;
        if ($this->fechada) return self::FECHADA;
        return self::PODE;
    }

    public function visualizar(int $idUsuario, string $tipoUsuario): int
    {
        if ($tipoUsuario == TipoUsuario::ADMINISTRADOR) return self::PODE;
        if ($tipoUsuario == TipoUsuario::ALUNO) {
            $alunoDaTurma = Query::select(
                'SELECT EXISTS(
                    SELECT 1
                      FROM aluno_em_turma
                     WHERE id_aluno = :idAluno
                       AND id_turma = :idTurma) AS aluno_da_turma',
                [ ':idAluno' => $idUsuario,
                   ':idTurma' => $this->idTurma ]
            )[0]['aluno_da_turma'];
            return $alunoDaTurma ? self::PODE : self::NAO_AUTORIZADO;
        }
        assert($tipoUsuario == TipoUsuario::PROFESSOR);
        $professorDaDisciplina = Query::select(
            'SELECT EXISTS(
                SELECT 1
                    FROM professor_de_disciplina
                    WHERE id_professor = :idProfessor
                    AND id_disciplina = :idDisciplina
                ) AS professor_da_disciplina',
            [ ':idProfessor' => $idUsuario, 
                ':idDisciplina' => $this->idDisciplina ]
        )[0]['professor_da_disciplina'];
        return $professorDaDisciplina ? self::PODE : self::NAO_AUTORIZADO;
    }
}