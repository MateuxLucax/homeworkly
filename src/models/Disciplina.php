<?php

require_once $root.'/models/Turma.php';

class Disciplina
{
    private int    $id = 0;
    private string $nome;
    private Turma  $turma;
    private array  $professores;
    private bool   $podeExcluir;
    // pode excluir = tem tarefas,
    // mas não queremos ter que buscar as tarefas em todo lugar onde queremos saber se a disciplina pode ser excluída!

    public function setId(int $id): Disciplina {
        $this->id = $id;
        return $this;
    }

    public function setNome(string $nome): Disciplina {
        $this->nome = $nome;
        return $this;
    }

    public function setTurma(Turma $turma): Disciplina {
        $this->turma = $turma;
        return $this;
    }

    public function setProfessores(array $professores): Disciplina {
        $this->professores = $professores;
        return $this;
    }

    public function setPodeExcluir(bool $podeExcluir): Disciplina {
        $this->podeExcluir = $podeExcluir;
        return $this;
    }

    public function getId()         : int    { return $this->id; }
    public function getNome()       : string { return $this->nome; }
    public function getTurma()      : Turma  { return $this->turma; }
    public function getProfessores(): array  { return $this->professores; }

    public function podeExcluir(): bool { return $this->podeExcluir; }
}