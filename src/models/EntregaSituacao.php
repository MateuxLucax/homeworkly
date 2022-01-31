<?php

// Situação da entrega de uma tarefa em relação ao aluno
enum EntregaSituacao
{
    // Enquanto o aluno não tiver entregue em definitivo a tarefa, e a tarefa não for fechada, a entrega fica pendente
    case PENDENTE;

    // Enquanto o aluno não tiver entregue em definitivo a tarefa e passar da data de entrega, com a tarefa ainda aberta, ela fica atrasada:
    case PENDENTE_ATRASADA;

    // Quando a data de fechamento estiver passado, as tarefas no estado PENDENTE e PENDENTE_ATRASADA ficam NAO_FEITA
    case NAO_FEITA;

    // Quando a entrega é feita em definitivo depois da data de entrega, ela fica entregue atrasada
    case ENTREGUE_ATRASADA;

    // Quando a entrega é feita em definitivo antes da data de entrega, ela fica entregue normalmente
    case ENTREGUE;

    // -------------------------------------------------------

    public function pendente(): bool
    { return $this == self::PENDENTE || $this == self::PENDENTE_ATRASADA; }

    public function entregue(): bool
    { return $this == self::ENTREGUE || $this == self::ENTREGUE_ATRASADA; }

    public function atrasada(): bool
    { return $this == self::PENDENTE_ATRASADA || $this == self::ENTREGUE_ATRASADA; }
}