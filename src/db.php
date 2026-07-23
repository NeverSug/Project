<?php

namespace App;

function getDB(): \PDO
{
    static $db = null;

    if (is_null($db)) {
        $db = new \PDO("sqlite:../data/database.db");
        $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
        $db->exec("PRAGMA foreign_keys = ON;");
    }

    return $db;
}
