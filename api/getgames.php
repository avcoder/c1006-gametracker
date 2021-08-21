<?php
header("Access-Control-Allow-Origin: *");
// setcookie('cookie_name', 'cookie_value', [
//     'samesite' => 'strict', // Allowed values: "Lax" or "Strict"
//     'expires' => time() + 60,
//   ]); 

// connect to db
require_once '../database.php';
$conn = db_connect();

// $limit = filter_var($_GET['limit'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
// $offset = filter_var($_GET['offset'] ?? 0, FILTER_SANITIZE_NUMBER_INT);

$sql = "SELECT * FROM games ORDER BY title";
// if ($limit > 0) {
//     $sql .= " LIMIT " . $limit;

//     if ($offset > 0) {
//         $sql .= " OFFSET " . $offset;
//     }
// }

$cmd = $conn->prepare($sql);
$cmd -> execute();
$games = $cmd->fetchAll(PDO::FETCH_ASSOC);

function insert_img_urls($object) {
    if (isset($object['photo'])) {
        $object['photo'] = "https://lamp.computerstudi.es/~Albert2/comp1006/week12/uploads/" . $object['photo'];
    } else {
        $object['photo'] = null;
    }
    return $object;
}

// get data
$games2 = array_map('insert_img_urls', $games);


// // get total
// $sql = "SELECT COUNT(*) as Total FROM games";
// $cmd = $conn->prepare($sql);
// $cmd -> execute();
// $count = $cmd->fetch(PDO::FETCH_ASSOC);


// // create associative array to set data and total into
// $data = [];
// $data['total'] = $count['Total'];
// $data['remaining'] = (int) $count['Total'] - (int) $offset - (int) $limit;
// $data['games'] = $games2;

echo json_encode($games2);

$conn = null;
