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
    <link rel="stylesheet" href="styles.css">
    <title>Follow/Unfollow Users</title>
    <link rel="stylesheet" href="follow.css">
</head>
<body>
    <div class="container">
        <h2>Follow/Unfollow Users</h2>

        <form action="follow.php" method="post">
            <label for="following">Username to Follow/Unfollow:</label>
            <input type="text" id="following" name="following" required>

            <button type="submit" name="follow">Follow</button><br>
            <button type="submit" name="unfollow">Unfollow</button><br>
            <a href="index.php" class="follow-btn">Home</a>
        </form>
    </div>
</body>
</html>

<?php

$follower = $_SESSION["username"];
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $following = $_POST["following"];


 
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "bracuniversityblogger";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

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

