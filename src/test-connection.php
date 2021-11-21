<?php

require_once "Connection.php";

$result = Connection::getInstance()->query("SELECT 1")->fetchAll();

die(json_encode($result));