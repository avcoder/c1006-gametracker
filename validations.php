<?php

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function require_login() {
    if (!is_logged_in()) {
        header("Location:login.php");
        exit;
    }
}

function validate_game($game) {
    $errors = [];

    if (empty(trim($game['title']))) {
        $errors['title'] = "Please enter a title";
    }
    
    $year_regex = "/[0-9]{4}/";
    $year = $game['year'];
    if ($year < 0 || strlen($year) != 4 || !preg_match($year_regex, $year)) {
        $errors['year'] = "Please enter a valid year";
    }
    
    $url_regex = "/https?:\/\/.+\..+/";
    if (! preg_match($url_regex, $game['url'])) {
        $errors['url'] = "Please enter a valid url beginning with http:// or https://";
    }

    if ($game['filename']) {
        if ($game['filesize'] > 1000000) {
            $errors['pic'] = "Image must be less than 1MB";
        }
        
        if (!($game['filetype'] == 'image/jpeg' || $game['filetype'] == 'image/png')) {
            $errors['pic'] = "Image format must be .jpg or .png";
        }    
    }

    return $errors;
}

function validate_registration($user, $conn) {
    $errors = [];

        if (empty(trim($user['email']))) {
            $errors['email'] = "Email cannot be blank";
        }

        $email_regex = "/.+\@.+\..+/";
        if (!preg_match($email_regex, $user['email'])) {
            $errors['email'] = "Username must be a valid email address";
        }

        if (empty(trim($user['new-password']))) {
            $errors['password'] = "Password cannot be blank";
        }

        if (empty(trim($user['confirm-password']))) {
            $errors['confirm'] = "Confirmation password cannot be blank";
        }

        $sql = "SELECT * FROM users WHERE username=:username";
        $cmd = $conn -> prepare($sql);
        $cmd -> bindParam(":username", $user['email'], PDO::PARAM_STR, 50);
        $cmd -> execute();
        $found_username = $cmd -> fetch();

        if ($found_username) {
            $errors['email'] = 'Username already taken';
        }

    return $errors;
}

function display_toast($t, $msg) {
    if (!($t && $msg)) {
        return;
    }

    $msgs = [];
    $msgs['0'] = "Successfully Added";
    $msgs['1'] = "Successfully Deleted";
    $msgs['2'] = "Successfully Edited";

    echo <<<EOL
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div id="liveToast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header bg-dark text-light">
        
        <strong class="me-auto">$msgs[$t]</strong>
        <small>11 mins ago</small>
        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body bg-dark text-light">
        $msg
        </div>
    </div>
    </div>
    <script>
    window.addEventListener('DOMContentLoaded', () => {
        var toastElList = [].slice.call(document.querySelectorAll('.toast'))
        var toastList = toastElList.map(function (toastEl) {
            return new bootstrap.Toast(toastEl)
        });
        toastList.forEach(toast => toast.show())
    });
    </script>
    EOL;
}
