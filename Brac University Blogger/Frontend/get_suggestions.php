<?php
require_once "database.php";

if (isset($_POST['query'])) {
    $searchText = $_POST['query'];
    // Prevent SQL injection (basic)
    $searchText = mysqli_real_escape_string($conn, $searchText);

    if (strlen($searchText) > 0) {
        // Fetch up to 4 users matching username or fullname
        $sql = "SELECT id, fullname, username FROM users WHERE username LIKE '%$searchText%' OR fullname LIKE '%$searchText%' LIMIT 4";
        $result = mysqli_query($conn, $sql);
        
        $suggestions = array();
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $suggestions[] = $row;
            }
        }
        echo json_encode($suggestions);
    } else {
        echo json_encode([]);
    }
}
?>
