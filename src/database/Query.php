<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/../database/Connection.php";

class Query
{
    public static function select(string $sql, array $params = []) : array | false
    {
        try {
            $statement = Connection::getInstance()->prepare($sql);
            $statement->execute($params);
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            self::returnError($e, $sql, $params);
        }

        return false;
    }

    public static function execute(string $sql, array $params) : bool
    {
        try {
            $statement = Connection::getInstance()->prepare($sql);
            return $statement->execute($params);
        } catch (PDOException $e) {
            self::returnError($e, $sql, $params);
        }

        return false;
    }

    private static function returnError(
        PDOException $e,
        string $sql,
        array $paramBinds
    ) : void {
        $response = [
            "message"   => "Não foi possível executar a query.",
            "sql"       => $sql,
            "params"    => $paramBinds,
            "exception" => json_encode($e)
        ];
        die(json_encode($response));
    }
}