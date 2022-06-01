<?php
session_start();
require_once("../admin/account_db.php");
if (isset($_SESSION['user'])) {
    header('Location: my_wallet.php');
    exit();
}
$error = '';
$username = '';
$password = '';

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $result = login($username, $password);
    $dataUser = getAccount($username);
    if (gettype($result) == "boolean") {
        if ($dataUser["first_login"] == 1) {
            $_SESSION['user'] = $username;
            header("Location: reset_password.php");
        } else {
            if ($dataUser['status'] == 'pending verification') {
                $_SESSION['user'] = $username;
                $_SESSION['name'] = $dataUser["fullname"];
                header("Location: wait_active.php");
                exit();
            } else {
                $_SESSION['user'] = $username;
                $_SESSION['name'] = $dataUser["fullname"];
                header("Location: my_wallet.php");
                exit();
            }
        }
    } else {
        $error =  $result;
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
    <title>MyWallet - Login</title>
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
                            <h1 class="fs-4 card-title fw-bold mb-4 text-center">Login</h1>
                            <form method="POST" class="needs-validation" novalidate="" autocomplete="off">
                                <div class="mb-3">
                                    <label class="mb-2 text-muted" for="username">Username</label>
                                    <input id="username" type="text" class="form-control" name="username" value="<?= $username ?>" required autofocus />
                                    <div class="invalid-feedback">Username is required</div>
                                </div>

                                <div class="mb-3">
                                    <div class="mb-2 w-100">
                                        <label class="text-muted" for="password">Password</label>
                                        <a href="forgot.php" class="float-end">
                                            Forgot Password?
                                        </a>
                                    </div>
                                    <input id="password" type="password" class="form-control" name="password" required value="<?= $password ?>" />
                                    <div class="invalid-feedback">Password is required</div>
                                </div>
                                <?php
                                if (isset($_SESSION['success'])) {
                                ?>
                                    <div class="alert alert-success text-success">
                                        <strong>Success!</strong> <?= $_SESSION['success'] ?>
                                    </div>
                                <?php
                                } else if (!empty($error)) {
                                ?>
                                    <div class="alert alert-danger text-danger">
                                        <strong>Error!</strong> <?= $error ?>
                                    </div>
                                <?php
                                }
                                ?>

                                <div class="d-flex align-items-center">
                                    <div class="form-check">
                                        <input type="checkbox" name="remember" id="remember" class="form-check-input" />
                                        <label for="remember" class="form-check-label">Remember Me</label>
                                    </div>
                                    <button type="submit" class="btn ms-auto w-25 text-white" style="background-color: #7162ad">
                                        Login
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="card-footer py-3 border-0">
                            <div class="text-center">
                                Don't have an account?
                                <a href="./register.php" class="text-primary">Create Account</a>
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

    <script src="../main.js"></script>
</body>

</html>