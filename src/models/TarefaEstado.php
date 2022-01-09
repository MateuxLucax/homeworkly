<?php

enum TarefaEstado
{
    case ESPERANDO_ABERTURA;
    case ABERTA;
    case ATRASADA;
    case FECHADA;

    public function toString(): string {
        return match ($this) {
            self::ESPERANDO_ABERTURA => 'Esperando abertura',
            self::ABERTA             => 'Aberta',
            self::ATRASADA           => 'Atrasada',
            self::FECHADA            => 'Fechada'
        };
    }
}
