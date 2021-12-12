<?php

require_once $root.'/models/Turma.php';

class Disciplina
{
    private ?int    $id          = null;
    private ?string $nome        = null;
    private ?Turma  $turma       = null;
    private ?array  $professores = null;

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

    public function getId()         : ?int    { return $this->id; }
    public function getNome()       : ?string { return $this->nome; }
    public function getTurma()      : ?Turma  { return $this->turma; }
    public function getProfessores(): ?array  { return $this->professores; }
}