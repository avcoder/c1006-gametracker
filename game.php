<?php
session_start();

require_once 'validations.php';

require_login();

// connect to db
require_once 'database.php';
$conn = db_connect();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // get the form inputs
    $title = trim(filter_var($_POST['title'], FILTER_SANITIZE_STRING));
    $year = trim(filter_var($_POST['year'], FILTER_SANITIZE_NUMBER_INT));
    $genre = trim(filter_var($_POST['genre'], FILTER_SANITIZE_STRING));
    $url = trim(filter_var($_POST['url'], FILTER_SANITIZE_URL));

    // create an associative array on the user input
    $new_game = [];
    $new_game['title'] = $title;
    $new_game['year'] = $year;
    $new_game['genre'] = $genre;
    $new_game['url'] = $url;

    $new_game['filename'] = $_FILES['pic']['name'] ?? '';
    $new_game['filetmp_name'] = $_FILES['pic']['tmp_name'] ?? '';
    if ($new_game['filetmp_name']) {
        $new_game['filetype'] = mime_content_type($new_game['filetmp_name']);
    } else {
        $new_game['filetype'] = '';
    }
    $new_game['filesize'] = $_FILES['pic']['size'] ?? '';

    // validate the inputs
    $errors = validate_game($new_game);

    // if there are no errors, insert into db
    if (empty($errors)) {
        try {
            // move uploaded pic if there is one
            // and save new session_id+filename so we can bindParam it
            $unique_filename = '';
            if ($new_game['filetmp_name']) {
                $unique_filename = session_id() . $new_game['filename'];
                move_uploaded_file($new_game['filetmp_name'], "uploads/" . $unique_filename);
                // set up the SQL INSERT command
                $sql = "INSERT INTO games (title, year, genre, url, photo) VALUES (:title, :year, :genre, :url, :pic)";
            } else {
                // set up the SQL INSERT command
                $sql = "INSERT INTO games (title, year, genre, url) VALUES (:title, :year, :genre, :url)";
            }
            
    
            // create a command object and fill the parameters with the form values
            $cmd = $conn->prepare($sql);
            $cmd -> bindParam(':title', $title, PDO::PARAM_STR, 50);
            $cmd -> bindParam(':year', $year, PDO::PARAM_INT);
            $cmd -> bindParam(':genre', $genre, PDO::PARAM_STR, 32);
            $cmd -> bindParam(':url', $url, PDO::PARAM_STR, 100);
            if ($new_game['filetmp_name']) {
                $cmd -> bindParam(':pic', $unique_filename, PDO::PARAM_STR, 100);
            }

            // execute the command
            $cmd -> execute();
    
           header("Location: games.php");
           exit;
        } catch (Exception $e) {
            // header("Location: error.php");
            echo $e;
            exit;
        }
    }
    // else display errors
}
?>

<?php
$title_tag = "Add Game";
include_once 'shared/top.php';

?>

<h1 class="text-center mt-5">Add Game <i class="bi bi-joystick"></i></h1>

<div class="row mt-5 ms-1">
    <form class="row justify-content-center mb-5" action="game.php" method="POST" novalidate enctype="multipart/form-data">
        <div class="col-12 col-md-6">

            <div class="row mb-4">
                <div class="col">
                    <div class="form-floating mb-3">
                        <input autofocus required class="<?= (isset($errors['title']) ? 'is-invalid ' : ''); ?> form-control form-control-lg" type="text" name="title" placeholder="Title" value="<?= $title ?? ''; ?>">
                        <label class="col-2 col-form-label" for="title">Title</label>
                        <p class="text-danger"><?= $errors['title'] ?? ''; ?></p>
                    </div>
                </div>
            </div>

            <div class="row mb-4 ">
                <div class="col">
                    <div class="form-floating mb-3">
                        <input inputmode="numeric" pattern="[0-9]{4}" title="Enter a 4 digit year"
                            class="<?= (isset($errors['year']) ? 'is-invalid ' : ''); ?>form-control form-control-lg" type="text" name="year" placeholder="2021" value="<?= $year ?? ''; ?>">
                        <label class="col-2 col-form-label" for="year">Year</label>
                        <p class="text-danger"><?= $errors['year'] ?? ''; ?></p>
                    </div>
                </div>
            </div> 

            <div class="row mb-4">
                <div class="col">
                    <div class="form-floating">
                        <select name="genre" id="genre" class="form-select form-select-lg" style="height:70px;">
                            <?php 
                                $sql = "SELECT genre FROM genres ORDER BY genre";
                                $genres = db_queryAll($sql, $conn);
                                
                                foreach ($genres as $eachgenre) {
                                    echo "<option value=" . $eachgenre["genre"] . ">" . ucfirst($eachgenre["genre"]) . "</option>";
                                }
                            ?>
                        </select>
                        <label class="col-form-label" for="genre">Genre</label>
                    </div>
                </div>                    
            </div>


            <div class="row mb-4 ">
                <div class="col">
                
                    <div class="form-floating mb-3">
                        <input required pattern="https?:\/\/.+\..+"
                            title="Please enter a url beginning with http:// or https://" class="<?= (isset($errors['url']) ? 'is-invalid ' : ''); ?>form-control form-control-lg"
                            type="text" name="url" placeholder="https://" value="<?= $url ?? ''; ?>">
                        <label class="col-2 col-form-label" for="url">Url</label>
                        <p class="text-danger"><?= $errors['url'] ?? ''; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-3 mb-5">
            <div class="card">
                <img id="cover" src="https://dummyimage.com/300x225" class="card-img-top" alt="...">
                <div class="card-body">
                    <input id="choosefile" type="file" name="pic" class="form-control">
                </div>
                <p class="px-3 pb-2 text-danger"><?= $errors['pic'] ?? ''; ?></p>
                
            </div>
        </div>    

        <div class="row justify-content-center col-12 col-md-9">
            <button class="btn btn-success btn-lg">Submit</button>
        </div>
    </form>
</div>

<?php

include_once 'shared/footer.php';

?>