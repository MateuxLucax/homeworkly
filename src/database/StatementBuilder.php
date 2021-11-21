<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/../database/Connection.php";

class StatementBuilder
{
    public static function select(string $sql, array $colValues = []) : array | false
    {
        try {
            $statement = Connection::getInstance()->prepare($sql);

            $statement = self::bindParams($statement, $colValues);

            $statement->execute();

            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            self::returnError($e, $sql, $colValues);
        }

        return false;
    }

    protected static function iud(string $sql, array $colValues) : bool
    {
        try {
            $statement = Connection::getInstance()->prepare($sql);

            $statement = self::bindParams($statement, $colValues);

            return $statement->execute();
        } catch (PDOException $e) {
            self::returnError($e, $sql, $colValues);
        }

        return false;
    }

    public static function insert(string $sql, array $colValues) : bool
    {
        return self::iud($sql, $colValues);
    }

    public static function update(string $sql, array $colValues): bool
    {
        return self::iud($sql, $colValues);
    }

    public static function delete(string $sql, array $colValues): bool
    {
        return self::iud($sql, $colValues);
    }

    protected static function bindParams(PDOStatement $stmt, array $paramBinds) : PDOStatement
    {
        foreach ($paramBinds as $param => &$bind) {
            $stmt->bindParam(":{$param}", $bind);
        }

        return $stmt;
    }

    protected static function returnError(
        PDOException $e,
        string $sql,
        array $paramBinds
    ) : void {
        $response = [
            "message" => "Não foi possível executar a query.",
            "sql" => $sql,
            "params" => $paramBinds,
            "exception" => $e->getMessage()
        ];

        die(json_encode($response));
    }
}