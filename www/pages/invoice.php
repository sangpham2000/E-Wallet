<?php
require_once("../admin/account_db.php");
require_once("../admin/wallet_db.php");
if (isset($_GET["id"]) && isset($_GET["name"])) {
    $data = getTransactionByID($_GET["id"]);
    $dataSendUser = getAccount($data["from_username"]);
    $dataBeneficiaryUser = getAccount($data["to_username"]);
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
    <title>Document</title>
</head>

<body>
    <div class="wrapper">
        <!-- Header -->
        <header class="header">
            <div class="container">
                <div class="header-container">
                    <div class="header-logo">
                        <img srcset="../images/logo.png 2x" alt="" />
                        <a href="./my_wallet.php">MyWallet</a>
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
                        <span><?= $_GET["name"] ?></span>
                        <img src="../images/avata.png" alt="" />
                        <div class="dropdown" id="dropdown-menu">
                            <a class="dropdown-item" href="#">
                                <i class="fa-solid fa-gear"></i>Setting
                            </a>
                            <a class="dropdown-item" href="./confirm_password.html"><i class="fa-solid fa-unlock-keyhole"></i>Reset password</a>
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
            <div class="container">
                <div class="invoice">
                    <h1>Invoice Details</h1>
                    <div class="invoice-info">
                        <h3>Sender Information:</h3>
                        <div class="invoice-info-name">
                            <p><?= $dataSendUser["fullname"] ?> (<?= $dataSendUser["username"] ?>)</p>
                            <p><?= $dataSendUser["phone_number"] ?></p>
                        </div>
                        <div class="invoice-info-content">
                            <p>
                                <?= $dataSendUser["address"] ?>
                            </p>
                        </div>
                    </div>
                    <div class="invoice-info">
                        <h3>Beneficiary information:</h3>
                        <div class="invoice-info-name">
                            <p><?php
                                if ($dataBeneficiaryUser != "Username does not exist") {
                                    echo $dataBeneficiaryUser["fullname"];
                                } else {
                                    echo $data["to_username"];
                                }
                                ?> (<?php
                                    if ($dataBeneficiaryUser != "Username does not exist") {
                                        echo $dataBeneficiaryUser["fullname"];
                                    } else {
                                        echo $data["to_username"];
                                    }
                                    ?>)</p>
                            <p><?php
                                if ($dataBeneficiaryUser != "Username does not exist") {
                                    echo $dataBeneficiaryUser["phone_number"];
                                } else {
                                    echo $data["to_username"];
                                }
                                ?></p>
                        </div>
                        <div class="invoice-info-content">
                            <p>
                                <?php
                                if ($dataBeneficiaryUser != "Username does not exist") {
                                    echo $dataBeneficiaryUser["address"];
                                } else {
                                    echo $data["to_username"];
                                }
                                ?>
                            </p>
                        </div>
                    </div>
                    <div class="invoice-money">
                        <div class="invoice-money-info">
                            <h3>Amount:</h3>
                            <div class="invoice-money-content">
                                <p><?= number_format($data["amount"]) ?> VND</p>
                            </div>
                        </div>
                        <div class="invoice-money-info">
                            <h3>Transaction cost:</h3>
                            <div class="invoice-money-content">
                                <p><?= number_format($data["trans_cost"]) ?> VND</p>
                            </div>
                        </div>
                    </div>
                    <div class="invoice-info">
                        <h3>Message:</h3>
                        <div class="invoice-info-content">
                            <p>
                                <?= $data["message"] ?>
                            </p>
                        </div>
                    </div>
                    <div class="invoice-info">
                        <h3>Date:</h3>
                        <div class="invoice-info-content">
                            <p><?= $data["date"] ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>

</html>