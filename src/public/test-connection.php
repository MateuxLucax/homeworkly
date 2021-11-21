<?php

require_once "../database/Connection.php";

$result = Connection::getInstance()->query("SELECT 1")->fetchAll();

die(json_encode($result));