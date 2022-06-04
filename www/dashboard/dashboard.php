<?php
require_once("../admin/account_db.php");
require_once("../admin/wallet_db.php");
session_start();
if (!isset($_SESSION['useradmin'])) {
    header('Location: login_admin.php');
    exit();
} else {
    $data_user = getAccount($_SESSION['useradmin']);
}

$data_users = getAllAccount();
$transactions = array_reverse(getAllTransactions());
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="preconnect" href="https://fonts.gstatic.com" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet" />

    <!-- <link rel="stylesheet" href="./css/dashboard.css" /> -->
    <link rel="stylesheet" href="../style.css" />
    <link rel="icon" type="image/x-icon" href="../images/icon-logo.png">

    <title>Dashboard</title>
</head>

<body>
    <div class="wrapper">
        <!-- Tab items -->
        <div class="tabs">
            <div class="tab-item active">
                <i class="tab-icon fa-solid fa-user-shield"></i>
                User Acount
            </div>
            <div class="tab-item">
                <i class="tab-icon fa-solid fa-user-clock"></i>
                Account waiting for verification
            </div>
            <div class="tab-item">
                <i class="tab-icon fa-solid fa-arrow-down-up-across-line"></i>
                Pending transactions
            </div>
            <div class="tab-item">
                <i class="tab-icon fa-solid fa-money-bill-transfer"></i>
                All transactions
            </div>
            <div class="line"></div>
        </div>

        <!-- Tab content -->
        <div class="tab-content">
            <div class="tab-pane active">
                <h2>User Acount</h2>
                <table>
                    <tr>
                        <th>User Name</th>
                        <th>Full name</th>
                        <th>Email</th>
                        <th>Birthday</th>
                        <th>Balance</th>
                        <th>Phone number</th>
                        <th>Address</th>
                        <th>Status</th>
                    </tr>
                    <?php
                    foreach ($data_users as $user) {
                        if ($user['status'] != 'pending verification') {
                    ?>
                            <tr>
                                <td><?= $user['username'] ?></td>
                                <td><?= $user['fullname'] ?></td>
                                <td><?= $user['email'] ?></td>
                                <td><?= $user['birthday'] ?></td>
                                <td><?= number_format($user['balance']) ?> VND</td>
                                <td><?= $user['phone_number'] ?></td>
                                <td><?= $user['address'] ?></td>
                                <td><?= $user['status'] ?></td>
                            </tr>
                    <?php
                        }
                    }
                    ?>
                </table>
            </div>
            <div class="tab-pane">
                <h2>Account waiting for verification</h2>
                <table>
                    <tr>
                        <th>User Name</th>
                        <th>Full name</th>
                        <th>Email</th>
                        <th>Birthday</th>
                        <th>Citizen identity card (Front)</th>
                        <th>Citizen identity card (Back)</th>
                        <th>Phone number</th>
                        <th>Address</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                    <?php
                    foreach ($data_users as $user) {
                        if ($user['status'] == 'pending verification') {
                    ?>
                            <tr>
                                <td><?= $user['username'] ?></td>
                                <td><?= $user['fullname'] ?></td>
                                <td><?= $user['email'] ?></td>
                                <td><?= $user['birthday'] ?></td>
                                <td><img src="../uploads/<?= $user['citizen_card_front'] ?>" width="400" height="110"></td>
                                <td><img src="../uploads/<?= $user['citizen_card_back'] ?>" width="400" height="110"></td>
                                <td><?= $user['phone_number'] ?></td>
                                <td><?= $user['address'] ?></td>
                                <td><?= $user['status'] ?></td>
                                <td style="text-align: center">
                                    <button class="btn btn-rounded " onclick="verify(<?= $user['username'] ?>)">
                                        Verify
                                    </button>
                                </td>
                            </tr>
                    <?php
                        }
                    }
                    ?>
                </table>
            </div>
            <div class="tab-pane">
                <h2>Pending transactions</h2>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Amount</th>
                        <th>Transaction cost</th>
                        <th>Message</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                    <?php
                    foreach ($transactions as $trans) {
                        if ($trans['status'] == 'pending approval') {
                    ?>
                            <tr>
                                <td><?= $trans['id'] ?></td>
                                <td><?= $trans['from_username'] ?></td>
                                <td><?= $trans['to_username'] ?></td>
                                <td><?= number_format($trans['amount']) ?> VND</td>
                                <td><?= number_format($trans['trans_cost']) ?> VND</td>
                                <td><?= $trans['message'] ?></td>
                                <td><?= $trans['date'] ?></td>
                                <td><?= $trans['status'] ?></td>
                                <td style="text-align: center">
                                    <button class="btn btn-rounded" onclick="accept(<?= $trans['id'] ?>)">Accept</button>
                                </td>
                            </tr>
                    <?php
                        }
                    }
                    ?>
                </table>
            </div>
            <div class="tab-pane">
                <h2>All transactions</h2>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Amount</th>
                        <th>Transaction cost</th>
                        <th>Message</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                    <?php
                    foreach ($transactions as $trans) {
                        if ($trans['status'] != 'pending approval') {
                    ?>
                            <tr>
                                <td><?= $trans['id'] ?></td>
                                <td><?= $trans['from_username'] ?></td>
                                <td><?= $trans['to_username'] ?></td>
                                <td><?= number_format($trans['amount']) ?> VND</td>
                                <td><?= number_format($trans['trans_cost']) ?> VND</td>
                                <td><?= $trans['message'] ?></td>
                                <td><?= $trans['date'] ?></td>
                                <td><?= $trans['status'] ?></td>
                            </tr>
                    <?php
                        }
                    }
                    ?>
                </table>
            </div>
        </div>
    </div>

    <script>
        function verify(username) {
            if (confirm('Are you sure you want to activate this account?') == true) {
                window.location.href = `active_account.php?username=${username}`;
            }
        }

        function accept(id) {
            if (confirm('Are you sure you want to accept this transaction?') == true) {
                window.location.href = `accept_transaction.php?id=${id}`;
            }
        }
    </script>
    <script src="../main.js"></script>
</body>

</html>