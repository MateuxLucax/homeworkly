<?php

class Avaliacao
{
    private  Tarefa  $tarefa;
    private  Usuario $aluno;
    private ?float   $nota;
    private ?bool    $visto;
    private ?string  $comentario;

    public function setTarefa(Tarefa $tarefa): Avaliacao {
        $this->tarefa = $tarefa;
        return $this;
    }

    public function setAluno(Usuario $aluno): Avaliacao {
        $this->aluno = $aluno;
        return $this;
    }

    public function setNota(?float $nota): Avaliacao {
        $this->nota = $nota;
        return $this;
    }

    public function setVisto(?bool $visto): Avaliacao {
        $this->visto = $visto;
        return $this;
    }
     
    public function setComentario(?string $comentario): Avaliacao {
        $this->comentario = $comentario;
        return $this;
    }

    public function tarefa():      Tarefa  { return $this->tarefa; }
    public function aluno():       Usuario { return $this->aluno; }
    public function nota():       ?float   { return $this->nota; }
    public function visto():      ?bool    { return $this->visto; }
    public function comentario(): ?string  { return $this->comentario; }
}