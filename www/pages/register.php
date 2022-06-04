<?php
session_start();
require_once("../admin/account_db.php");

$error = '';
$full_name = '';
$email = '';
$birth_day = '';
$phone_number = '';
$address = '';
$citizen_card_front = '';
$citizen_card_back = '';


if (isset($_POST['fullname']) && isset($_POST['email']) && isset($_POST['birthday']) && isset($_POST['phone']) && isset($_POST['address'])) {
    $full_name = $_POST['fullname'];
    $email = $_POST['email'];
    $birth_day = $_POST['birthday'];
    $phone_number = $_POST['phone'];
    $address = $_POST['address'];
    $citizen_card_front = $_FILES['frontCitizen']['name'];
    $citizen_card_back = $_FILES['backCitizen']['name'];

    // upload file
    $final_path_front = "../uploads/" . basename(
        $citizen_card_front
    );
    $final_path_back = "../uploads/" . basename(
        $citizen_card_back
    );
    $supported_files = array("jpg", "png");

    if (!in_array(pathinfo($citizen_card_front, PATHINFO_EXTENSION), $supported_files) || !in_array(pathinfo($citizen_card_back, PATHINFO_EXTENSION), $supported_files)) {
        $error = "The file you choosed is not supported by the Server. The server only supports .JPG and .PNG formats.";
    } else {
        move_uploaded_file($_FILES['frontCitizen']['tmp_name'], $final_path_front);
        move_uploaded_file($_FILES['backCitizen']['tmp_name'], $final_path_back);

        $result = register($full_name, $email, $birth_day, $phone_number, $address, $citizen_card_front, $citizen_card_back);
        if (gettype($result) === "boolean") {
            $_SESSION["success"] = "Please check your Email to see your Username and Password.";
            header("Location: login.php");
        } else {
            $error = $result;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
    <link rel="icon" type="image/x-icon" href="../images/icon-logo.png">
    <title>MyWallet - Register</title>
</head>

<body>
    <section class="h-100">
        <div class="container h-100">
            <div class="row justify-content-sm-center h-100">
                <div class="col-xxl-6 col-xl-6 col-lg-7 col-md-7 col-sm-9">
                    <div class="text-center my-3">
                        <img src="../images/logo.png" alt="logo" width="110">
                        <h1 class="fs-4 card-title fw-bold" style="color: #7162ad;">My Wallet</h1>
                    </div>
                    <div class="card shadow-lg">
                        <div class="card-body p-5">
                            <h1 class="fs-4 card-title fw-bold mb-4 text-center">Register</h1>
                            <form method="POST" class="needs-validation" novalidate="" autocomplete="off" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label class="mb-2 text-muted" for="fullname">Full Name</label>
                                    <input id="fullname" type="text" class="form-control" name="fullname" value="<?= $full_name ?>" required>
                                    <div class="invalid-feedback">
                                        Full Name is required
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="mb-2 text-muted" for="email">E-Mail</label>
                                    <input id="email" type="email" class="form-control" name="email" value="<?= $email ?>" required>
                                    <div class="invalid-feedback">
                                        Email is invalid
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="mb-2 text-muted" for="birthday">Date Of Birth</label>
                                    <input id="birthday" type="date" class="form-control" name="birthday" value="<?= $birth_day ?>" required>
                                    <div class="invalid-feedback">
                                        Date Of Birth is required
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="mb-2 text-muted" for="phone">Phone Number</label>
                                    <input id="phone" type="text" class="form-control" name="phone" value="<?= $phone_number ?>" required>
                                    <div class="invalid-feedback">
                                        Phone number is required
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="mb-2 text-muted" for="address">Address</label>
                                    <input id="address" type="text" class="form-control" name="address" value="<?= $address ?>" required>
                                    <div class="invalid-feedback">
                                        Address is required
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col mb-3">
                                        <label class="mb-2 text-muted" for="frontCitizen">Citizen identity card (Front)</label>
                                        <input class="form-control" type="file" id="frontCitizen" name="frontCitizen" value="" required>
                                    </div>
                                    <div class="col mb-3">
                                        <label class="mb-2 text-muted" for="backCitizen">Citizen identity card (Back)</label>
                                        <input class="form-control" type="file" id="backCitizen" name="backCitizen" value="" required>
                                    </div>
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

                                <div class="align-items-center d-flex">
                                    <div class="text-center">
                                        Already have an account? <a href="./login.php" class="text-primary" style="color: #7162ad;">Login</a>
                                    </div>
                                    <button class="btn ms-auto w-25 text-white" style="background-color: #7162ad;">
                                        Register
                                    </button>
                                </div>
                            </form>
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