<?php

enum CorEvento
{
    case AZUL;
    case AMARELO;
    case VERMELHO;
    case VERDE;

    public function toString(): string
    {
        return match ($this) {
            self::AZUL     => '#0d6efd',
            self::VERMELHO => '#dc3545',
            self::AMARELO  => '#ffc107',
            self::VERDE    => '#198754'
        };
    }
}
