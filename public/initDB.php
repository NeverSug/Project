<?php

try {

    $db = new PDO("sqlite:../data/database.db");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = file_get_contents("../data/database.sql");
    $db->exec($sql);

    $db = null;
} catch (Exception $exeption) {

    echo $exeption->getMessage();
}
