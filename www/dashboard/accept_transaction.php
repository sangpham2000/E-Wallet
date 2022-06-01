<?php
require_once("../admin/wallet_db.php");

if (isset($_GET['id'])) {
    $result = acceptTransaction($_GET['id']);
    if (gettype($result) == 'boolean') {
        header("Location: dashboard.php");
        exit();
    } else {
        $error = $result;
    }
}
