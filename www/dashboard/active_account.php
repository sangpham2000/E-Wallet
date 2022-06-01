<?php
require_once("../admin/account_db.php");

if (isset($_GET['username'])) {
    $result = activeAccount($_GET['username']);
    if (gettype($result) == 'boolean') {
        header("Location: dashboard.php");
        exit();
    } else {
        $error = $result;
    }
}
