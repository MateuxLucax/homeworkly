<?php

class QueryException extends Exception
{
    public function __construct(
        protected string $sql, 
        protected array $params, 
        protected PDOException $e
    ) {
        $this->message = $e->message;
    }
}