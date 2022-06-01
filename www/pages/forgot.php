<?php
require_once("../admin/account_db.php");
require_once("../admin/wallet_db.php");
session_start();

$error = '';
$email = '';
if (isset($_POST['email'])) {
    $email = $_POST['email'];
    $dataUser = getAccountByEmail($email);
    if (gettype($dataUser)  == "boolean") {
        $error = "Email does not exist";
    } else {
        $_SESSION['email'] = $dataUser['email'];
        header("Location: reset.php");
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous" />
    <title>MyWallet - Forgot password</title>
</head>

<body>
    <section class="h-100">
        <div class="container h-100">
            <div class="row justify-content-sm-center h-100">
                <div class="col-xxl-6 col-xl-6 col-lg-7 col-md-7 col-sm-9">
                    <div class="text-center my-3">
                        <img src="../images/logo.png" alt="logo" width="110" />
                        <h1 class="fs-4 card-title fw-bold" style="color: #7162ad">
                            My Wallet
                        </h1>
                    </div>
                    <div class="card shadow-lg">
                        <div class="card-body p-5">
                            <h1 class="fs-4 card-title fw-bold mb-4 text-center">
                                Forgot Password
                            </h1>
                            <form method="POST" class="needs-validation" novalidate="" autocomplete="off">
                                <div class="mb-3">
                                    <label class="mb-2 text-muted" for="email">E-Mail Address</label>
                                    <input id="email" type="email" class="form-control" name="email" value="" required autofocus />
                                    <div class="invalid-feedback">Email is invalid</div>
                                </div>

                                <?php
                                if (!empty($error)) {
                                ?>
                                    <div class="alert alert-danger text-center">
                                        <strong>Fail!</strong> <?= $error ?>
                                    </div>
                                <?php
                                }
                                ?>

                                <div class="d-flex align-items-center">
                                    <button type="submit" class="btn ms-auto text-white" style="background-color: #7162ad">
                                        Send OTP code
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="card-footer py-3 border-0">
                            <div class="text-center">
                                Remember your password?
                                <a href="./login.php" class="text-primary">Login</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- START FOOTER -->
    <footer class="footer py-5">
        <div class="container">
            <div class="row">
                <div class="col-8 mx-auto text-center">
                    <p class="text-secondary">
                        Copyright ©
                        <script>
                            document.write(new Date().getFullYear());
                        </script>
                        Soft by Phạm Thanh Sang.
                    </p>
                </div>
            </div>
        </div>
    </footer>
    <!-- END FOOTER -->

    <script>
        function showErrorToast() {
            toast({
                title: 'Fail!',
                message: '<?= $error ?>',
                type: 'error',
                duration: 5000
            });
        }
    </script>

    <script src="../main.js"></script>
</body>

</html>