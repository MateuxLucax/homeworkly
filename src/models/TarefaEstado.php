<?php

enum TarefaEstado
{
    case ESPERANDO_ABERTURA;
    case ABERTA;
    case FECHADA;
    case ARQUIVADA;

    public function toString(): string {
        return match ($this) {
            self::ESPERANDO_ABERTURA => 'Esperando abertura',
            self::ABERTA             => 'Aberta',
            self::FECHADA            => 'Fechada',
            self::ARQUIVADA          => 'Arquivada'
        };
    }
}
