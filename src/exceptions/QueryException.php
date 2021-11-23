<?php

class QueryException extends Exception
{
    protected $message = 'Não foi possível executar a query';

    public function __construct(
        protected string $sql, 
        protected array $params, 
        protected PDOException $e
    ) {
    }
}