<?php 

class Turma
{
    private int    $id;
    private string $nome;
    private int    $ano;
    private array  $disciplinas;
    private array  $alunos;

    public function setId(int $id): Turma {
        $this->id = $id;
        return $this;
    }

    public function setNome(string $nome): Turma {
        $this->nome = $nome;
        return $this;
    }

    public function setAno(int $ano): Turma {
        $this->ano = $ano;
        return $this;
    }

    public function setDisciplinas(array $disciplinas): Turma {
        $this->disciplinas = $disciplinas;
        return $this;
    }

    public function setAlunos(array $alunos): Turma {
        $this->alunos = $alunos;
        return $this;
    }

    public function getId(): int { return $this->id; }
    public function getNome(): string { return $this->nome; }
    public function getAno(): int { return $this->ano; }
    public function getDisciplinas(): array { return $this->disciplinas; }
    public function getAlunos(): array { return $this->alunos; }

    public function addDisciplina(Disciplina $disciplina): Turma {
        $this->disciplinas[] = $disciplina;
        return $this;
    }

    public function podeExcluir(): bool {
        foreach ($this->disciplinas as $disciplina)
            if (!$disciplina->podeExcluir())
                return false;
        return true;
    }

}