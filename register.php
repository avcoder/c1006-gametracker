<?php

require_once 'database.php';
$conn = db_connect();

require_once 'validations.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // get the form inputs
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $new_password = filter_var($_POST['new-password'], FILTER_SANITIZE_EMAIL);
    $confirm_password = filter_var($_POST['confirm-password'], FILTER_SANITIZE_EMAIL);
    
    // create an associative array on the form
    $user = [];
    $user['email'] = $email;
    $user['new-password'] = $new_password;
    $user['confirm-password'] = $confirm_password;

    // validate the inputs
    $errors = validate_registration($user, $conn);
    
    // if there are no errors, hash password
    if (empty($errors)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        // set up our sql and execute the insert
        $sql = "INSERT INTO users (username, hashed_password) ";
        $sql .= "VALUES (:username, :password)";

        $cmd = $conn -> prepare($sql);
        $cmd -> bindParam(':username', $email, PDO::PARAM_STR, 50);
        $cmd -> bindParam(':password', $hashed_password, PDO::PARAM_STR, 255);
        $cmd -> execute();

        // disconnect
        $conn = null;

        // redirect to login page
        header("Location: login.php");
        exit;
    }

} else if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $errors = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
    html { 
        background: url(img/register.jpg) no-repeat center center fixed; 
        -webkit-background-size: cover;
        -moz-background-size: cover;
        -o-background-size: cover;
        background-size: cover;
    }
    </style>
</head>
<body>
    <div class="container">
    
        <div class="card position-absolute top-50 start-50 translate-middle" style="width: 700px;">
            <h1 class="card-title fs-5 mt-4 text-center">CREATE ACCOUNT</h1>

            <div class="row justify-content-center">
                <form novalidate class="p-5" method="POST">
                    <div class="form-floating mb-4">
                        <input type="email" required name="email" class="<?= (isset($errors['email']) ? 'is-invalid' : '') ?> rounded-0 form-control" id="email" placeholder="name@example.com" value="<?= $email ?? ''; ?>">
                        <label for="email">Email Address</label>
                        <p class="text-danger"><?= $errors['email'] ?? ''; ?></p>
                    </div>
                
                    <div class="form-floating mb-4">
                        <input type="password" required name="new-password" class="<?= (isset($errors['password']) ? 'is-invalid' : '') ?> rounded-0 form-control" id="new-password" placeholder="Password" value="<?= $new_password ?? ''; ?>">
                        <label for="new-password">New Password</label>
                        <p class="text-danger"><?= $errors['password'] ?? ''; ?></p>
                    </div>

                    <div class="form-floating mb-4">
                        <input type="password" required name="confirm-password" class="<?= (isset($errors['confirm']) ? 'is-invalid' : '') ?> rounded-0 form-control" id="confirm-password" placeholder="Confirm Password" value="<?= $confirm_password ?? ''; ?>">
                        <label for="confirm-password">Confirm Password</label>
                        <p class="text-danger"><?= $errors['confirm'] ?? ''; ?></p>
                    </div>                    

                    <div class="d-grid">
                        <button class="btn btn-success btn-lg mb-5">
                            Sign Up
                        </button>
                    </div>
                </form>
                <p class="text-center">Already have an account? <a href="login.php" class="text-dark"><strong>Login here</strong></a></p>
            </div>
        </div>

    </div>

    
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>