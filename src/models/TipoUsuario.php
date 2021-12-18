<?php

// TODO usar enum (introduzido no php 8.1)

class TipoUsuario
{
    public const ADMINISTRADOR = "administrador";
    public const ALUNO = "aluno";
    public const PROFESSOR = "professor";

    public const ALL = [
        self::ADMINISTRADOR,
        self::ALUNO,
        self::PROFESSOR
    ];
}