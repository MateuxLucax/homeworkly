<?php

require_once $root . '/models/Tarefa.php';
require_once $root . '/models/Usuario.php';

class Entrega
{
    private Tarefa   $tarefa;
    private Usuario  $aluno; 
    private string   $conteudo;
    private DateTime $dataHora;
    private bool     $emDefinitivo;

    public function tarefa(): Tarefa { return $this->tarefa; }
    public function aluno(): Usuario { return $this->aluno; }
    public function conteudo(): string { return $this->conteudo; }
    public function dataHora(): DateTime { return $this->dataHora; }
    public function emDefinitivo(): bool { return $this->emDefinitivo; }

    public function setTarefa(Tarefa $tarefa): Entrega { $this->tarefa = $tarefa; return $this; }
    public function setAluno(Usuario $aluno): Entrega { $this->aluno = $aluno; return $this; }
    public function setConteudo(string $conteudo): Entrega { $this->conteudo = $conteudo; return $this; }
    public function setDataHora(DateTime $dataHora): Entrega { $this->dataHora = $dataHora; return $this; }
    public function setEmDefinitivo(bool $emDefinitivo): Entrega { $this->emDefinitivo = $emDefinitivo; return $this; }
}