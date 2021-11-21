<?php

class UnauthorizedException extends Exception
{
    protected $message = "Usuário não autorizado.";

    protected $code = 401;
}