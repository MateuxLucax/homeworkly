<?php

require_once $root . '/utils/DateUtil.php';
require_once $root . '/models/TarefaEstado.php';
require_once $root . '/models/EntregaSituacao.php';

class Tarefa
{
    private int        $id;
    private Usuario    $professor;
    private Disciplina $disciplina;
    private string     $titulo;
    private string     $descricao;
    private int        $esforco_minutos;
    private bool       $com_nota;
    private DateTime   $abertura;
    private DateTime   $entrega;
    private DateTime   $fechamento;

    public function id():                 int        { return $this->id; }
    public function professor():          Usuario    { return $this->professor; }
    public function disciplina():         Disciplina { return $this->disciplina; }
    public function titulo():             string     { return $this->titulo; }
    public function descricao():          string     { return $this->descricao; }
    public function esforcoMinutos():     int        { return $this->esforco_minutos; }
    public function comNota():            bool       { return $this->com_nota; }
    public function dataHoraAbertura():   DateTime   { return $this->abertura; }
    public function dataHoraEntrega():    DateTime   { return $this->entrega; }
    public function dataHoraFechamento(): DateTime   { return $this->fechamento; }

    public function setId(int $id): Tarefa {
        $this->id = $id;
        return $this;
    }

    public function setProfessor(Usuario $professor): Tarefa {
        $this->professor = $professor;
        return $this;
    }

    public function setDisciplina(Disciplina $disciplina): Tarefa {
        $this->disciplina = $disciplina;
        return $this;
    }

    public function setTitulo(string $titulo): Tarefa {
        $this->titulo = $titulo;
        return $this;
    }

    public function setDescricao(string $descricao): Tarefa {
        $this->descricao = $descricao;
        return $this;
    }

    public function setEsforcoMinutos(int $esforco_minutos): Tarefa {
        $this->esforco_minutos = $esforco_minutos;
        return $this;
    }

    public function setComNota(bool $com_nota): Tarefa {
        $this->com_nota = $com_nota;
        return $this;
    }

    public function setDataHoraAbertura(DateTime $abertura): Tarefa {
        $this->abertura = $abertura;
        return $this;
    }

    public function setDataHoraEntrega(DateTime $entrega): Tarefa {
        $this->entrega = $entrega;
        return $this;
    }

    public function setDataHoraFechamento(?DateTime $fechamento): Tarefa {
        $this->fechamento = $fechamento;
        return $this;
    }

    // -------------------------------------------------------

    public function estado(): TarefaEstado
    {
        $agora = DateUtil::toLocalDateTime('now');

        if ($this->disciplina->getTurma()->getAno() < $agora->format('Y')) return TarefaEstado::ARQUIVADA;
        if ($agora < $this->abertura) return TarefaEstado::ESPERANDO_ABERTURA;
        if ($agora < $this->fechamento) return TarefaEstado::ABERTA; 
        return TarefaEstado::FECHADA; 
    }

    public function fechada(): bool
    {
        $estado = $this->estado();
        return $estado == TarefaEstado::FECHADA || $estado == TarefaEstado::ARQUIVADA;
    }

    public function entregaSituacao(?Entrega $entrega): EntregaSituacao
    {
        $agora = DateUtil::toLocalDateTime('now');

        if ($entrega == null || !$entrega->emDefinitivo()) {
            if (!$this->fechada()) {
                return $agora >= $this->dataHoraEntrega()
                     ? EntregaSituacao::PENDENTE_ATRASADA
                     : EntregaSituacao::PENDENTE;
            } else {
                return EntregaSituacao::NAO_FEITA;
            }
        } else {
            return $entrega->dataHora() >= $this->dataHoraEntrega()
                 ? EntregaSituacao::ENTREGUE_ATRASADA
                 : EntregaSituacao::ENTREGUE;
        }
    }
}