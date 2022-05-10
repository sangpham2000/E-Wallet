<?php
define("HOST", "mysql-server");
define("USER", "root");
define("PASS", "root");
define("DB_NAME", "e_wallet");

function create_connection()
{
    $conn = new mysqli(HOST, USER, PASS, DB_NAME);
    $conn->set_charset("utf8");
    if ($conn->connect_error) {
        die("Can not connect to the server: " . $conn->connect_error);
    }
    return $conn;
}
