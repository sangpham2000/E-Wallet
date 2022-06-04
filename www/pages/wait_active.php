<?php
require_once("../admin/account_db.php");
require_once("../admin/wallet_db.php");
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
} else {
    $data_user = getAccount($_SESSION['user']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../style.css" />
    <link rel="icon" type="image/x-icon" href="../images/icon-logo.png">
    <title>MyWallet</title>
</head>

<body>
    <div class="wrapper">
        <!-- Header -->
        <header class="header">
            <div class="container">
                <div class="header-container">
                    <div class="header-logo">
                        <img srcset="../images/logo.png 2x" alt="" />
                        <a href="#">MyWallet</a>
                    </div>
                    <ul class="menu">
                        <li class="menu-item">
                            <a href="#" class="menu-link">About</a>
                        </li>
                        <li class="menu-item">
                            <a href="#" class="menu-link">Product</a>
                        </li>
                        <li class="menu-item">
                            <a href="#" class="menu-link">Company</a>
                        </li>
                        <li class="menu-item">
                            <a href="#history" class="menu-link">Transaction history</a>
                        </li>
                    </ul>

                    <div class="header-user" id="header-menu">
                        <span><?= $data_user["fullname"] ?></span>
                        <img src="../images/avata.png" alt="" />
                        <div class="dropdown" id="dropdown-menu">
                            <a class="dropdown-item" href="#">
                                <i class="fa-solid fa-gear"></i>Setting
                            </a>
                            <a class="dropdown-item" href="./confirm_password.php"><i class="fa-solid fa-unlock-keyhole"></i>Reset password</a>
                            <a class="dropdown-item" href="./logout.php"><i class="fa-solid fa-arrow-right-from-bracket"></i>Log out</a>
                        </div>
                    </div>

                    <div class="menu-bars">
                        <i class="fa-solid fa-bars"></i>
                    </div>
                </div>
            </div>
        </header>

        <main>
            <section class="history" id="history">
                <div class="container">
                    <div class="history-container">
                        <div class="history-header">
                            <h1>Please wait for the administrator to activate your account</h1>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
</body>

</html>