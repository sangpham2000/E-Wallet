<?php

require_once("../admin/account_db.php");

$data = getAccount("sangpham");
// $hashed = $data['password'];

print_r($data);