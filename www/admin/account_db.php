<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require "../vendor/autoload.php";
require_once("db.php");

function generateRandomString($length = 6)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function generateRandomNumber($length = 10)
{
    $characters = '0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function getAllAccount()
{
    $sql = "SELECT * FROM users";
    $conn = create_connection();

    $result = $conn->query($sql);
    $data = array();

    for ($i = 0; $i < $result->num_rows; $i++) {
        $row = $result->fetch_assoc();
        $data[] = $row;
    }
    return $data;
}

function getAccount($username)
{
    $conn = create_connection();
    $sql = "SELECT * FROM users WHERE username = ?";
    $stm = $conn->prepare($sql);
    $stm->bind_param("s", $username);

    if (!$stm->execute()) {
        return "Can not login, please contact your admin";
    }
    $result = $stm->get_result();

    if ($result->num_rows == 0) {
        return "Username does not exist";
    }
    $data = $result->fetch_assoc();
    return $data;
}

function getAccountByEmail($email)
{
    $conn = create_connection();
    $sql = "SELECT * FROM users WHERE email = ?";
    $stm = $conn->prepare($sql);
    $stm->bind_param("s", $email);

    if (!$stm->execute()) {
        return "Can not login, please contact your admin";
    }
    $result = $stm->get_result();

    if ($result->num_rows == 0) {
        return false;
    }
    $data = $result->fetch_assoc();
    return $data;
}

function login($username, $password)
{
    $data = getAccount($username);
    if (gettype($data) == 'string') {
        return "Can not login, invalid username or password";
    } else {
        $hashed = $data['password'];
        if (!password_verify($password, $hashed)) {
            return "Can not login, invalid username or password";
        }
        return true;
    }
}

function loginAdmin($username, $password)
{
    $conn = create_connection();
    $sql = "SELECT * FROM admin WHERE username = ?";
    $stm = $conn->prepare($sql);
    $stm->bind_param("s", $username);

    if (!$stm->execute()) {
        return "Can not login, please contact your admin";
    }
    $result = $stm->get_result();

    if ($result->num_rows == 0) {
        return "Username does not exist";
    }
    $data = $result->fetch_assoc();
    $hashed = $data['password'];
    if (!password_verify($password, $hashed)) {
        return "Can not login, invalid username or password";
    }
    return true;
}

function register($fullname, $email, $birthday, $phone_number, $address, $citizen_card_front, $citizen_card_back)
{
    $sql = "SELECT COUNT(*) FROM users WHERE phone_number = ? OR email = ?";
    $conn = create_connection();

    $stm = $conn->prepare($sql);
    $stm->bind_param('ss', $phone_number, $email);
    $stm->execute();

    $result = $stm->get_result();
    $exists = $result->fetch_array()[0] === 1;

    if ($exists) {
        return "Can not register because this Phone Number or Email is already exists";
    }

    $username = generateRandomNumber();
    $password = generateRandomString();
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users(username, balance, fullname, email, birthday, phone_number, address, citizen_card_front, citizen_card_back, password, status, first_login) VALUE('$username', '0', ?, ?, ?, ?, ?, ?, ?, '$hashed', 'pending verification', '1')";

    $stm = $conn->prepare($sql);
    $stm->bind_param("sssssss", $fullname, $email, $birthday, $phone_number, $address, $citizen_card_front, $citizen_card_back);

    if ($stm->execute()) {
        sendActivationEmail($email, $username, $password);
        return true;
    }
    return $stm->error;
}

function sendActivationEmail($email, $username, $password)
{    //Create an instance; passing `true` enables exceptions    
    $mail = new PHPMailer(true);
    try {
        //Server settings
        //$mail->SMTPDebug = SMTP::DEBUG_SERVER; //Enable verbose debug output
        $mail->isSMTP(); //Send using SMTP
        $mail->Host = 'smtp.gmail.com'; //Set the SMTP server to send through
        $mail->SMTPAuth = true; //Enable SMTP authentication
        $mail->Username = 'sangpham1150@gmail.com'; //SMTP username
        $mail->Password = ''; //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; //Enable implicit TLS encryption
        $mail->Port = 465; //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom('sangpham1150@gmail.com', 'MyWallet');
        $mail->addAddress($email, 'Người nhận'); //Add a recipient
        $mail->isHTML(true); //Set email format to HTML
        $mail->Subject = 'Create account with MyWallet'; //
        $mail->Body = "-Your username is: <strong>$username</strong> <br> -Your password is: <strong>$password</strong>";
        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function sendResetEmail($email, $otp)
{    //Create an instance; passing `true` enables exceptions    
    $mail = new PHPMailer(true);
    try {
        //Server settings
        //$mail->SMTPDebug = SMTP::DEBUG_SERVER; //Enable verbose debug output
        $mail->isSMTP(); //Send using SMTP
        $mail->Host = 'smtp.gmail.com'; //Set the SMTP server to send through
        $mail->SMTPAuth = true; //Enable SMTP authentication
        $mail->Username = 'sangpham1150@gmail.com'; //SMTP username
        $mail->Password = ''; //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; //Enable implicit TLS encryption
        $mail->Port = 465; //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom('sangpham1150@gmail.com', 'MyWallet');
        $mail->addAddress($email, 'Người nhận'); //Add a recipient
        $mail->isHTML(true); //Set email format to HTML
        $mail->Subject = 'Reset password with MyWallet'; //
        $mail->Body = "<p>Your OTP code: <strong>$otp</strong></p>";
        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function updatePassword($username, $password)
{
    $sql = "UPDATE users SET first_login = 2, password = ? WHERE username = ?";
    $conn = create_connection();

    $stm = $conn->prepare($sql);
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stm->bind_param("ss", $hashed, $username);
    if (!$stm->execute()) {
        return "Can not execute command";
    }
    return true;
}

function activeAccount($username)
{
    $sql = "UPDATE users SET status = 'activated' WHERE username = ?";
    $conn = create_connection();

    $stm = $conn->prepare($sql);
    $stm->bind_param("s", $username);
    if (!$stm->execute()) {
        return "Can not execute command";
    }
    return true;
}
