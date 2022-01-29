<?php

require_once $root.'/database/Connection.php';
require_once $root.'/exceptions/QueryException.php';

class Query
{
    /**
     * @throws QueryException
     */
    public static function select(string $sql, array $params = []) : array | false
    {
        try {
            $statement = Connection::getInstance()->prepare($sql);
            $statement->execute($params);
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new QueryException($sql, $params, $e);
        }
    }

    /**
     * @throws QueryException
     */
    public static function execute(string $sql, array $params) : bool
    {
        try {
            $statement = Connection::getInstance()->prepare($sql);
            return $statement->execute($params);
        } catch (PDOException $e) {
            throw new QueryException($sql, $params, $e);
        }
    }
}