<?php

const HOST = 'host.docker.internal';
const PORT = '5432';
const DBNAME = 'homeworkly';
const USER = 'root';
const PASSWORD = 'root';

class Connection {

    private static PDO $pdo;

    public static function getInstance() : PDO {
        if (!isset(self::$pdo)) {
            try {
                $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
                self::$pdo = new PDO("pgsql:host=" . HOST . ";port=" . PORT . "; dbname=" . DBNAME . "; ", USER, PASSWORD, $options);
            } catch (PDOException $e) {
                die(json_encode(['outcome' => false, 'message' => 'Unable to connect', 'exception' => $e]));
            }
        }
        return self::$pdo;
    }
}
