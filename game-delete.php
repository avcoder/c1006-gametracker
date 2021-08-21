<?php
session_start();

require_once 'validations.php';

require_login();

// connect to db
require_once 'database.php';
$conn = db_connect();

// IF this page is fetched via a GET
// then display record with confirmation button

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $id = filter_var($_GET['game_id'], FILTER_SANITIZE_NUMBER_INT);

    $sql = "SELECT * FROM games WHERE game_id=" . $id;
    $game = db_queryOne($sql, $conn);

    $title = $game['title'];
    $year = $game['year'];
    $genre = $game['genre'];
    $url = $game['url'];
    
    $title_tag = "Delete Game";
    include_once 'shared/top.php';
?>  
    <h1 class="text-center mt-5 display-1 text-warning"><i class="bi bi-x-circle"></i></h1>
    <h1 class="text-center mt-5">Are you sure you want to delete this?</h1>

<div class="row mt-5 justify-content-center">
    <form class="col-6 mb-5" method="POST">
        <div class="row mb-4">
            <label class="col-2 col-form-label fs-4" for="title">Title</label>
            <div class="col-10">
            <input readonly class="form-control form-control-lg"
                    type="text" name="title" value="<?php echo $title; ?>">
            </div>
        </div>

        <div class="row mb-4 ">
            <label class="col-2 col-form-label fs-4" for="year">Year</label>
            <div class="col-10">
            <input readonly class="form-control form-control-lg"
                    type="text" name="year" value="<?php echo $year; ?>">
            </div>
        </div>

        <div class="row mb-4 ">
            <label class="col-2 col-form-label fs-4" for="title">Genre</label>
            <div class="col-10">
            <input readonly class="form-control form-control-lg"
                    type="text" name="genre" value="<?php echo $genre; ?>">
            </div>
        </div>

        <div class="row mb-4 ">
            <label class="col-2 col-form-label fs-4" for="url">Url</label>
            <div class="col-10">
                <input readonly class="form-control form-control-lg"
                    type="text" name="url" value="<?php echo $url; ?>">
            </div>
        </div>
        <div class="d-grid">
        <input readonly class="form-control form-control-lg"
                    type="hidden" name="game_id" value="<?php echo $id; ?>">
            <button class="btn btn-danger btn-lg">Delete forever</button>
        </div>
    </form>
</div>
<?php
}  else if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $id = filter_var($_POST['game_id'], FILTER_SANITIZE_NUMBER_INT);

    echo "id is $id";
// IF this page is fetched via a POST
// then go ahead and actually delete the record

    $sql = "DELETE FROM games WHERE game_id=" . $id;

    $cmd = $conn->prepare($sql);
    $cmd -> execute();

    header("Location: games.php?t=1&msg=Tetris");
}





?>