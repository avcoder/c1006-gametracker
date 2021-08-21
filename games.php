<?php
session_start();
require_once 'validations.php';

// connect to db
require_once 'database.php';
$conn = db_connect();
?>

<?php
$title_tag = "Games";
include_once 'shared/top.php';

// build a sql query
$sql = "SELECT * FROM games";

// will store the separate words that the user is searching
$word_list = [];

if (!empty($keywords)) {
    $sql .= " WHERE ";

    // split the multiple keywords into an array using php explode
    $word_list = explode(" " , $keywords);
    // ["out", "run"]
    // loop through the word list array, and add each word to the where clause
    foreach($word_list as $key => $word) {
        $word_list[$key] = "%" . $word . "%";


        // but for the first word, omit the word OR
        if ($key == 0) {
            $sql .= " title LIKE ?";
        } else {
            $sql .= " OR title like ?";
        }

    }

}

$sql .= " ORDER by title";

// GOAL: SELECT * FROM games WHERE title LIKE ___ OR title like ____;
$games = db_queryAll($sql, $conn, $word_list);
?>


<table class="sortable table table-secondary table-striped table-bordered border-secondary fs-5 mt-4">
    <thead>
        <tr>
            <th scope="col">Title</th>
            <th scope="col">Year</th>
            <th scope="col">Genre</th>
            <th scope="col" class="col-1 sorttable_nosort">Url</th>
            <?php if (is_logged_in()) { ?>
                <th scope="col" class="col-1 sorttable_nosort">Edit</th>
                <th scope="col" class="col-1 sorttable_nosort">Delete</th>
            <?php } ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($games as $game) { ?>
        <tr>
            <th scope="row"><?php echo $game['title']; ?></th>
            <td><?php echo $game['year']; ?></td>
            <td><?php echo $game['genre']; ?></td>
            <td><a class="btn btn-primary" target="_blank" href="<?php echo $game['url']; ?>"> Play <i
                        class="bi bi-box-arrow-up-right"></i></a>
            </td>
            <?php if (is_logged_in()) { ?>
                <td>
                    <a href="game-edit.php?game_id=<?php echo $game['game_id']; ?>" class="btn btn-secondary">Edit <i class="bi bi-pencil-square"></i></a>
                </td>            
                <td>
                    <a href="game-delete.php?game_id=<?php echo $game['game_id']; ?>" class="btn btn-warning"><span class="visually-hidden">Delete</span> <i class="bi bi-trash"></i></a>
                </td>
            <?php } ?>
        </tr>
        <?php } ?>
    </tbody>
</table>



<?php
$t = filter_var($_GET['t'] ?? '', FILTER_SANITIZE_STRING);
$msg = filter_var($_GET['msg'] ?? '', FILTER_SANITIZE_STRING);
display_toast($t, $msg);
include_once 'shared/footer.php';
?>