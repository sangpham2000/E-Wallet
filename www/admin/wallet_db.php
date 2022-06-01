<?php

require_once("db.php");
require_once("account_db.php");

function getCredit($card_number)
{
    $conn = create_connection();
    $sql = "SELECT * FROM credit_card WHERE card_number = ?";

    $stm = $conn->prepare($sql);
    $stm->bind_param("s", $card_number);
    if (!$stm->execute()) {
        return "Can not query, please contact admin";
    }
    $result = $stm->get_result();
    if ($result->num_rows == 0) {
        return "";
    }
    $data = $result->fetch_assoc();
    return $data;
}

function getPhoneCard($carrier)
{
    $conn = create_connection();
    $sql = "SELECT * FROM phone_card WHERE carrier = ?";

    $stm = $conn->prepare($sql);
    $stm->bind_param("s", $carrier);
    if (!$stm->execute()) {
        return "Can not query, please contact admin";
    }
    $result = $stm->get_result();
    if ($result->num_rows == 0) {
        return "";
    }
    $data = $result->fetch_assoc();
    return $data;
}

function updateBalance($username, $amount, $trans_cost)
{
    $conn = create_connection();
    $sql = "UPDATE users SET balance = ? WHERE username = ?";

    $stm = $conn->prepare($sql);
    $user = getAccount($username);
    $balance = $user['balance'] + $amount + $trans_cost;
    $stm->bind_param("is", $balance, $username);
    if (!$stm->execute()) {
        return "Can not execute command";
    }
    return true;
}

function updateCredit($card_number, $amount)
{
    $conn = create_connection();
    $sql = "UPDATE credit_card SET balance = ? WHERE card_number = ?";
    $stm = $conn->prepare($sql);

    $card = getCredit($card_number);
    $balance = $card['balance'] + $amount;
    $stm->bind_param("is", $balance, $card_number);
    if (!$stm->execute()) {
        return "Can not execute command";
    }
    return true;
}

function getTransHistoryByUsername($username)
{
    $conn = create_connection();
    $sql = "SELECT * FROM transaction WHERE from_username=? || to_username=? ORDER BY ID DESC";
    $stm = $conn->prepare($sql);
    $stm->bind_param("ss", $username, $username);
    if (!$stm->execute()) {
        return "Can not query";
    }
    $result = $stm->get_result();
    $data = array();
    for ($i = 0; $i < $result->num_rows; $i++) {
        $row = $result->fetch_assoc();
        $data[] = $row;
    }
    return $data;
}

function getTransactionByID($id)
{
    $conn = create_connection();
    $sql = "SELECT * FROM transaction WHERE id=?";
    $stm = $conn->prepare($sql);
    $stm->bind_param("i", $id);
    if (!$stm->execute()) {
        return "Can not query";
    }
    $result = $stm->get_result();
    if ($result->num_rows == 0) {
        return "";
    }
    $data = $result->fetch_assoc();
    return $data;
}

function getAllTransactions()
{
    $sql = "SELECT * FROM transaction";
    $conn = create_connection();

    $result = $conn->query($sql);
    $data = array();

    for ($i = 0; $i < $result->num_rows; $i++) {
        $row = $result->fetch_assoc();
        $data[] = $row;
    }
    return $data;
}

function transaction($from_user, $to_user, $amount, $message, $status, $trans_cost)
{
    $conn = create_connection();
    $sql = "INSERT INTO transaction(from_username, to_username, amount, message, date, status, trans_cost) VALUES(?, ?, ?, ?, DATE(NOW()), ?, ?)";
    $stm = $conn->prepare($sql);
    $stm->bind_param("ssissi", $from_user, $to_user, $amount, $message, $status, $trans_cost);

    if (!$stm->execute()) {
        return "Can not execute command";
    }
    return true;
}

function acceptTransaction($id)
{
    $sql = "UPDATE transaction SET status = 'success' WHERE id = ?";
    $conn = create_connection();

    $stm = $conn->prepare($sql);
    $stm->bind_param("i", $id);
    if (!$stm->execute()) {
        return "Can not execute command";
    }
    return true;
}
