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

$transactions = getTransHistoryByUsername($_SESSION['user']);
$error = "";
$message = "";
$card_number = "";
$card_date = "";
$amount_deposit = "";
$status = "";
// Deposit Money
if (isset($_POST['card-number']) && isset($_POST['card-date']) && isset($_POST['cvv']) && isset($_POST['amount-deposit'])) {
    $card_number = $_POST['card-number'];
    $card_date = $_POST['card-date'];
    $cvv = $_POST['cvv'];
    $amount_deposit = $_POST['amount-deposit'];

    $data = getCredit($card_number);

    $first_date = strtotime(date('Y-m-d'));
    $second_date = strtotime($card_date);

    if ($data == "") {
        $error = "This card is not supported";
    } else if ($card_date != $data['expiration']) {
        $error = "Wrong Expiration";
    } else if ($cvv != $data['cvv']) {
        $error = "Wrong Card Verification Value";
    } else if ($amount_deposit < 0) {
        $error = "Please enter a valid amount.";
    } else if (floor($second_date - $first_date) <= 0) {
        $error = "Expired credit card";
    } else {
        $status = "success";
        $message = "Deposit into account successfully";
        updateBalance($_SESSION['user'], $amount_deposit, 0);
        transaction($data_user["username"], $data_user["username"], $amount_deposit, $message, $status, 0);
        header("Location: my_wallet.php");
    }
}

// Transfer money 
$sender = "";
$beneficiary = "";
$amount = "";
$trans_cost = "";
if (isset($_POST['sender']) && isset($_POST['beneficiary']) && isset($_POST['amount']) && isset($_POST['message']) && isset($_POST['trans-cost-bearer'])) {
    $sender = $_POST['sender'];
    $beneficiary = $_POST['beneficiary'];
    $amount = $_POST['amount'];
    $message = $_POST['message'];
    $trans_cost = $_POST['trans-cost'];

    $data_beneficiary = getAccount($beneficiary);
    if ($sender != $data_user["username"]) {
        $error = "Please enter your account number correctly.";
    } else if (!is_array($data_beneficiary)) {
        $error = "Beneficiary does not exist";
    } else if (($data_user['balance'] - $amount) < 0) {
        $error = "Your account balance is not enough to make the transaction.";
    } else if ($amount < 0) {
        $error = "Please enter a valid amount.";
    } else if ($amount >= 5000000) {
        $status = 'pending approval';
        if ($_POST['trans-cost-bearer'] == 'you') {
            updateBalance($sender, 0, -$trans_cost);
            transaction($sender, $beneficiary, $amount, $message, $status, $trans_cost);
            header("Location: my_wallet.php");
        } else {
            updateBalance($sender, 0, 0);
            transaction($sender, $beneficiary, $amount, $message, $status, $trans_cost);
            header("Location: my_wallet.php");
        }
    } else {
        if ($amount >= 5000000) {
            $status = 'pending approval';
            if ($_POST['trans-cost-bearer'] == 'you') {
                updateBalance($sender, 0, -$trans_cost);
                transaction($sender, $beneficiary, $amount, $message, $status, $trans_cost);
                header("Location: my_wallet.php");
            } else {
                updateBalance($sender, 0, 0);
                transaction($sender, $beneficiary, $amount, $message, $status, $trans_cost);
                header("Location: my_wallet.php");
            }
        } else {
            $status = 'success';
            if ($_POST['trans-cost-bearer'] == 'you') {
                updateBalance($beneficiary, $amount, 0);
                updateBalance($sender, -$amount, -$trans_cost);
                transaction($sender, $beneficiary, $amount, $message, $status, $trans_cost);
                header("Location: my_wallet.php");
            } else {
                updateBalance($beneficiary, $amount, -$trans_cost);
                updateBalance($sender, -$amount, 0);
                transaction($sender, $beneficiary, $amount, $message, $status, $trans_cost);
                header("Location: my_wallet.php");
            }
        }
    }
}

// Buy phone card
$card_code = '';
$phone_card = '';
$phone_value = '';
$quantity = 0;
if (isset($_POST['phone-card']) && isset($_POST['phone-value']) && isset($_POST['quantity'])) {
    $phone_card = $_POST['phone-card'];
    $phone_value = $_POST['phone-value'];
    $quantity = $_POST['quantity'];

    $card_data = getPhoneCard($phone_card);
    if ($card_data == "") {
        $error = "Please choose your Mobile Network Operator";
    } else if (($data_user['balance'] - $phone_value * $quantity) < 0) {
        $error = "Your account balance is not enough to make the transaction.";
    } else {
        for ($i = 0; $i < $quantity; $i++) {
            $card_code = $card_data['code'] . generateRandomNumber(5);
            $message = $message . '-' . $_POST['phone-card'] . ' card with a face value of ' .  number_format($phone_value) . ' VND. Code: ' . $card_code . '<br>';
        }
        $status = 'success';
        updateBalance($data_user['username'], -$phone_value * $quantity, 0);
        transaction($data_user['username'], $phone_card, $phone_value * $quantity, $message, $status, $trans_cost);
        header("Location: my_wallet.php");
    }
}

// Withdraw money
$sender_withdraw = '';
$card_number_withdraw = '';
$expiration_withdraw = '';
$cvv_withdraw = '';
$amount_withdraw = '';
$trans_cost_withdraw = '';
if (isset($_POST['sender-withdraw']) && isset($_POST['card-number-withdraw']) && isset($_POST['expiration-withdraw']) && isset($_POST['cvv-withdraw']) && isset($_POST['amount-withdraw']) && isset($_POST['trans-cost-withdraw'])) {
    $sender_withdraw = $_POST['sender-withdraw'];
    $card_number_withdraw = $_POST['card-number-withdraw'];
    $expiration_withdraw = $_POST['expiration-withdraw'];
    $cvv_withdraw = $_POST['cvv-withdraw'];
    $amount_withdraw = $_POST['amount-withdraw'];
    $trans_cost_withdraw = $_POST['trans-cost-withdraw'];

    $data_user = getAccount($sender_withdraw);
    $data_credit = getCredit($card_number_withdraw);
    if ($sender_withdraw = '') {
        $error = 'Please enter your ID number';
    } else if ($data_credit == '') {
        $error = 'Invalid card information';
    } else if ($card_number_withdraw != '111111') {
        $error = 'This card is not supported for withdraw';
    } else if ($expiration_withdraw != $data_credit['expiration']) {
        $error = 'Wrong Expiration';
    } else if ($cvv_withdraw != $data_credit['cvv']) {
        $error = 'Wrong Card Verification Value';
    } else {
        if ($amount_withdraw >= 5000000) {
            $status = 'pending approval';
            $message = '-Card number: 111111' . '<br>' . '-Expiration: 10/10/2022' . '<br>' . '-CVV: 411' . '<br>' . '-Note: ';
            updateCredit($card_number_withdraw, $amount_withdraw);
            transaction($data_user["username"], "Your Credit Card", $amount_withdraw, $message, $status, 0);
            header("Location: my_wallet.php");
        } else {
            $status = 'success';
            $message = '-Card number: 111111' . '<br>' . '-Expiration: 10/10/2022' . '<br>' . '-CVV: 411' . '<br>' . '-Note: ';
            updateCredit($card_number_withdraw, $amount_withdraw);
            updateBalance($data_user["username"], -$amount_withdraw, 0);
            transaction($data_user["username"], "Your Credit Card", $amount_withdraw, $message, $status, 0);
            header("Location: my_wallet.php");
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="icon" type="image/x-icon" href="../images/icon-logo.png">
    <link rel="stylesheet" href="../style.css" />
    <title>MyWallet</title>
</head>

<body onload="<?php
                if (!empty($error)) {
                    echo "showErrorToast()";
                }
                ?>">
    <div id="toast">
    </div>

    <!-- Toast -->
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
                            <a class="dropdown-item" href="./confirm_password.php?user=<?= $data_user["username"] ?>"><i class="fa-solid fa-unlock-keyhole"></i>Reset password</a>
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
            <!-- Account balance -->
            <section class="balance">
                <div class="container">
                    <div class="balance-container">
                        <div class="balance-item">
                            <div class="balance-item-icon">
                                <i class="fa-solid fa-wallet"></i>
                                <span>Total balance</span>
                            </div>
                            <div class="balance-item-info">
                                <p><?= number_format($data_user['balance']) ?></p>
                                <p>VND</p>
                            </div>
                        </div>

                        <div class="balance-btn">
                            <button id="deposit" href="#" class="btn btn-rounded">
                                Deposit money <i class="fa-solid fa-sack-dollar"></i>
                            </button>
                            <button id="send" href="#" class="btn btn-rounded">
                                Transfer money
                                <i class="fa-solid fa-arrow-right-arrow-left"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </section>
            <!-- End Account balance -->

            <!-- Service -->
            <section class="service">
                <div class="container">
                    <div class="service-container">
                        <button type="button" class="service-item btn btn-rounded" id="buycard">
                            <div class="service-item-icon">
                                <i class="fa-solid fa-mobile"></i>
                            </div>
                            <span>Buy phone card</span>
                        </button>

                        <button class="service-item btn btn-rounded" id="withdraw">
                            <div class="service-item-icon">
                                <i class="fa-solid fa-money-bill-transfer"></i>
                            </div>
                            <span>Withdraw money</span>
                        </button>

                        <button class="service-item btn btn-rounded" href="#">
                            <div class="service-item-icon">
                                <i class="fa-solid fa-file-invoice-dollar"></i>
                            </div>
                            <span>Bill Payment</span>
                        </button>
                    </div>
                </div>
            </section>
            <!-- End Service -->

            <!-- Transaction history -->
            <section class="history" id="history">
                <div class="container">
                    <div class="history-container">
                        <div class="history-header">
                            <h1>Transaction History</h1>
                        </div>
                        <?php
                        foreach ($transactions as $trans) {
                            // if ($trans['status'] != "pending approval") {
                        ?>
                            <div class="history-item">
                                <a href="./invoice.php?id=<?= $trans["id"] ?>&name=<?= $data_user["fullname"] ?>" class="history-item-link">
                                    <div class="history-item-date">
                                        <p><?= $trans["date"] ?></p>
                                        <div class="history-item-info">
                                            <p>From: <?= getAccount($trans["from_username"])["fullname"] ?></p>
                                            <p>To: <?php
                                                    if (getAccount($trans["to_username"]) != "Username does not exist") {
                                                        echo getAccount($trans["to_username"])["fullname"];
                                                    } else {
                                                        echo $trans["to_username"];
                                                    }
                                                    ?></p>
                                            <p>Status: <?= $trans["status"] ?></p>
                                        </div>
                                    </div>
                                    <div class="history-item-content">
                                        <div class="history-item-content-message">
                                            <p>
                                                <?= $trans["message"] ?>
                                            </p>
                                        </div>

                                        <div class="history-item-content-money 
                                            <?php if ($trans["to_username"] == $data_user["username"]) {
                                                echo "receive";
                                            } else {
                                                echo "send";
                                            }
                                            ?>
                                            ">
                                            <p>
                                                <?php
                                                if ($trans["to_username"] == $data_user["username"]) {
                                                    echo "+" . number_format($trans['amount']);
                                                } else {
                                                    echo "-" . number_format($trans['amount']);
                                                }
                                                ?>
                                            </p>
                                            <p>VND</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php
                            // }
                        }
                        ?>

                    </div>
                </div>
            </section>
            <!-- End Transaction history -->
        </main>
        <!-- End header -->

        <!-- Modal popup -->
        <div id="modal" class="modal">
            <div class="modal-overlay"></div>

            <div class="modal-body">
                <!-- Transfer money -->
                <form class="modal-form" id="form-send" method="POST">
                    <h2>Transfer Money</h2>
                    <div class="modal-content">
                        <div class="modal-info">
                            <i class="fa-solid fa-credit-card"></i>
                            <span>Sender Information</span>
                            <div class="modal-input">
                                <input type="text" name="sender" id="sender" value="<?= $data_user['username'] ?>" />
                                <i class="fa-solid fa-user"></i>
                            </div>
                        </div>

                        <div class="modal-info">
                            <i class="fa-solid fa-money-bill-transfer"></i>
                            <span>Beneficiary Information</span>
                            <div class="modal-input">
                                <input type="text" name="beneficiary" id="beneficiary" value="" />
                                <i class="fa-solid fa-user-group"></i>
                            </div>
                        </div>

                        <div class="modal-info">
                            <i class="fa-solid fa-money-bill-transfer"></i>
                            <span>Amount Of Money</span>
                            <div class="modal-input">
                                <input type="number" name="amount" id="amount" />
                                <span>VND</span>
                            </div>
                        </div>

                        <div class="modal-info">
                            <i class="fa-solid fa-envelope"></i>
                            <span>Message</span>
                            <div class="modal-message">
                                <textarea rows="4" cols="10" name="message" id="message"></textarea>
                            </div>
                        </div>

                        <div class="modal-info">
                            <i class="fa-solid fa-money-bill-transfer"></i>
                            <span>Transaction cost</span>
                            <div class="modal-input">
                                <input type="number" name="trans-cost" id="trans-cost" />
                                <span>VND</span>
                            </div>
                        </div>

                        <div class="modal-info">
                            <i class="fa-solid fa-money-bill-transfer"></i>
                            <span>Transaction cost bearer</span>
                            <div class="modal-radio">
                                <div class="modal-radio-option">
                                    <input type="radio" id="you" name="trans-cost-bearer" value="you" /><span>You</span>
                                </div>
                                <div class="modal-radio-option">
                                    <input type="radio" id="bearer" name="trans-cost-bearer" value="beneficiary" /><span>Beneficiary</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-btn">
                        <button type="button" id="cancelSend" class="btn btn-rounded-border">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-rounded">Confirm</button>
                    </div>

                </form>

                <!-- Deposit money -->
                <form class="modal-form" id="form-deposit" method="POST">
                    <h2>Deposit Money</h2>
                    <div class="modal-content">
                        <div class="modal-info">
                            <i class="fa-solid fa-credit-card"></i>
                            <span>CreditCard Number</span>
                            <div class="modal-input">
                                <input type="number" id="card-number" name="card-number" value="<?= $card_number ?>" />
                            </div>
                        </div>

                        <div class="modal-info">
                            <i class="fa-solid fa-credit-card"></i>
                            <span>Card Expiration Date</span>
                            <div class="modal-input">
                                <input type="date" id="card-date" name="card-date" value="<?= $card_date ?>" />
                            </div>
                        </div>

                        <div class="modal-info">
                            <i class="fa-solid fa-credit-card"></i>
                            <span>Card Verification Value</span>
                            <div class="modal-input">
                                <input type="password" id="cvv" name="cvv" />
                            </div>
                        </div>

                        <div class="modal-info">
                            <i class="fa-solid fa-money-bill-transfer"></i>
                            <span>Amount Of Money</span>
                            <div class="modal-input">
                                <input type="number" placeholder="" id="amount-deposit" name="amount-deposit" value="<?= $amount_deposit ?>" />
                                <span>VND</span>
                            </div>
                        </div>
                    </div>

                    <div class="modal-btn">
                        <button type="button" id="cancelDeposit" class="btn btn-rounded-border">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-rounded">Confirm</button>
                    </div>
                </form>

                <!-- Buy Phone Card -->
                <form class="modal-form" id="form-buycard" method="POST">
                    <h2>Buy Phone Card</h2>
                    <div class="modal-content">
                        <div class="modal-info">
                            <i class="fa-solid fa-credit-card"></i>
                            <span>Mobile Network Operator</span>
                            <div class="modal-input">
                                <select id="phone-card" name="phone-card">
                                    <option value=""></option>
                                    <option value="Viettel">Viettel</option>
                                    <option value="Mobifone">Mobifone</option>
                                    <option value="Vinaphone">Vinaphone</option>
                                </select>
                            </div>
                        </div>

                        <div class="modal-info">
                            <i class="fa-solid fa-credit-card"></i>
                            <span>Denominations</span>
                            <div class="modal-input">
                                <select id="phone-value" name="phone-value">
                                    <option value=""></option>
                                    <option value="10000">10,000 VND</option>
                                    <option value="20000">20,000 VND</option>
                                    <option value="50000">50,000 VND</option>
                                    <option value="100000">100,000 VND</option>
                                </select>
                            </div>
                        </div>

                        <div class="modal-info">
                            <i class="fa-solid fa-money-bill-transfer"></i>
                            <span>Quantity</span>
                            <div class="modal-radio">
                                <div class="modal-radio-option">
                                    <input type="radio" id="quantity1" name="quantity" value="1" /><span>1</span>
                                </div>
                                <div class="modal-radio-option">
                                    <input type="radio" id="quantity2" name="quantity" value="2" /><span>2</span>
                                </div>
                                <div class="modal-radio-option">
                                    <input type="radio" id="quantity3" name="quantity" value="3" /><span>3</span>
                                </div>
                                <div class="modal-radio-option">
                                    <input type="radio" id="quantity4" name="quantity" value="4" /><span>4</span>
                                </div>
                                <div class="modal-radio-option">
                                    <input type="radio" id="quantity5" name="quantity" value="5" /><span>5</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-btn">
                        <button type="button" id="cancelBuyCard" class="btn btn-rounded-border">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-rounded">Confirm</button>
                    </div>
                </form>

                <!-- Withdraw money -->
                <form class="modal-form" id="form-withdraw" method="POST">
                    <h2>Withdraw Money</h2>
                    <div class="modal-info">
                        <i class="fa-solid fa-credit-card"></i>
                        <span>Sender Information</span>
                        <div class="modal-input">
                            <input type="text" name="sender-withdraw" id="sender-withdraw" value="<?= $data_user['username'] ?>" />
                            <i class="fa-solid fa-user"></i>
                        </div>
                    </div>

                    <div class="modal-content">
                        <div class="modal-info">
                            <i class="fa-solid fa-credit-card"></i>
                            <span>CreditCard Number</span>
                            <div class="modal-input">
                                <input type="number" id="card-number-withdraw" name="card-number-withdraw" value="<?= $card_number ?>" />
                            </div>
                        </div>

                        <div class="modal-info">
                            <i class="fa-solid fa-credit-card"></i>
                            <span>Card Expiration Date</span>
                            <div class="modal-input">
                                <input type="date" id="expiration-withdraw" name="expiration-withdraw" value="<?= $card_date ?>" />
                            </div>
                        </div>

                        <div class="modal-info">
                            <i class="fa-solid fa-credit-card"></i>
                            <span>Card Verification Value</span>
                            <div class="modal-input">
                                <input type="password" id="cvv-withdraw" name="cvv-withdraw" />
                            </div>
                        </div>

                        <div class="modal-info">
                            <i class="fa-solid fa-money-bill-transfer"></i>
                            <span>Amount Of Money</span>
                            <div class="modal-input">
                                <input type="number" name="amount-withdraw" id="amount-withdraw" />
                                <span>VND</span>
                            </div>
                        </div>

                        <div class="modal-info">
                            <i class="fa-solid fa-money-bill-transfer"></i>
                            <span>Withdraw cost</span>
                            <div class="modal-input">
                                <input type="number" name="trans-cost-withdraw" id="trans-cost-withdraw" />
                                <span>VND</span>
                            </div>
                        </div>
                    </div>

                    <div class="modal-btn">
                        <button type="button" id="cancelWithdraw" class="btn btn-rounded-border">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-rounded">Confirm</button>
                    </div>

                </form>
            </div>
        </div>
        <!-- End modal popup-->

        <!-- Footer -->
        <footer class="footer">
            <p class="footer-copy">
                Copyright ©
                <script>
                    document.write(new Date().getFullYear());
                </script>
                Soft by Phạm Thanh Sang.
            </p>
        </footer>
        <!-- End Footer -->
    </div>

    <script>
        function showErrorToast() {
            toast({
                title: 'Fail!',
                message: '<?= $error ?>',
                type: 'error',
                duration: 5000
            });
        }

        function showSuccessToast() {
            toast({
                title: "Success!",
                message: "",
                type: "success",
                duration: 5000
            });
        }
    </script>
    <script src="../main.js"></script>
</body>

</html>