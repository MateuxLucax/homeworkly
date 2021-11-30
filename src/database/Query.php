<?php

require_once $root.'/database/Connection.php';
require_once $root.'/exceptions/QueryException.php';


// TODO remover essa classe, o próprio PDO já fica bonito o suficiente:
//
// $pdo->prepare('INSERT INTO turma (nome, ano) VALUES (:nome, :ano)')->execute([
//     ':nome' => $data['nome'],
//     ':ano'  => $data['ano']
// ]);

class Query
{
    public static function select(string $sql, array $params = []) : array | false
    {
        try {
            $statement = Connection::getInstance()->prepare($sql);
            $statement->execute($params);
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new QueryException($sql, $params, $e);
        }

        return false;
    }

    public static function execute(string $sql, array $params) : bool
    {
        try {
            $statement = Connection::getInstance()->prepare($sql);
            return $statement->execute($params);
        } catch (PDOException $e) {
            throw new QueryException($sql, $params, $e);
        }

        return false;
    }
}