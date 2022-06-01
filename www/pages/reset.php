<?php
require_once("../admin/account_db.php");
require_once("../admin/wallet_db.php");
session_start();

if (isset($_SESSION['email'])) {
    $data_user = getAccountByEmail($_SESSION['email']);
    $otp = generateRandomNumber(6);
    sendResetEmail($data_user['email'], $otp);
    $_SESSION['otp'] = $otp;
    $_SESSION['user'] = $data_user['username'];
    header('Location: otp.php');
}