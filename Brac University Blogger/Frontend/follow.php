<?php
    session_start();
    if (!isset($_SESSION["user"])) {
        header("Location: login.php");
        
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Follow/Unfollow Users</title>
    <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>
    <div class="auth-body">
        <div class="auth-container">
            <div class="auth-header">
                <h2>Follow/Unfollow Users</h2>
            </div>

            <form action="follow.php" method="post">
                <div class="form-group">
                    <label for="following" class="form-label" style="margin-bottom:10px; display:block;">Username to Follow/Unfollow:</label>
                    <input type="text" id="following" name="following" class="form-control" required placeholder="Enter username here...">
                </div>

                <div class="form-group">
                    <button type="submit" name="follow" class="btn-primary" style="margin-bottom:10px;">Follow</button>
                    <button type="submit" name="unfollow" class="btn-primary" style="background-color: #dc3545; margin-bottom:10px;">Unfollow</button>
                </div>
                <div class="text-center">
                    <a href="index.php" style="color:var(--primary-color);">Back to Home</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

<?php

$follower = $_SESSION["username"];
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $following = $_POST["following"];


 
require_once "database.php";

    $checkUserQuery = "SELECT id FROM users WHERE username = '$following'";
    $result = $conn->query($checkUserQuery);

    $checkUserQue = "SELECT id FROM users WHERE username = '$follower'";
    $results = $conn->query($checkUserQue);
    $followerId = $results->fetch_assoc()['id'];

    if ($result->num_rows == 1) {

        $checkFollowQuery = "SELECT * FROM follows WHERE follower_id = $followerId AND following_id =
         (SELECT id FROM users WHERE username = '$following')";
        $resultFollow = $conn->query($checkFollowQuery);

        if (isset($_POST["follow"])) {
            if ($resultFollow->num_rows == 0) {
                $followQuery = "INSERT INTO follows (follower_id, following_id) VALUES
                 ($followerId, (SELECT id FROM users WHERE username = '$following'))";
                $conn->query($followQuery);
                echo "You are now following $following.";
            } else {
                echo "You are already following $following.";
            }
        } elseif (isset($_POST["unfollow"])) {
            if ($resultFollow->num_rows > 0) {
                $unfollowQuery = "DELETE FROM follows WHERE 
                follower_id = $followerId AND following_id = (SELECT id FROM users WHERE username = '$following')";
                $conn->query($unfollowQuery);
                echo "You have unfollowed $following.";
            } else {
                echo "You are not following $following.";
            }
        }
    } else {
        echo "Invalid usernames.";
    }

    $conn->close();
}
?>

