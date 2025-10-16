<?php
session_start();
if (isset($_SESSION["user"])) {
   header("Location: index.php");
}
?>

<?php
$username = $_SESSION["username"];
require_once "database.php";
$checkUserQue = "SELECT id FROM users WHERE username = '$username'";
$results = $conn->query($checkUserQue);
$uid = $results->fetch_assoc()['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $blogId = $_POST['blog_id'];

    $userId = $uid; // Replace with the actual user ID (assuming you have a logged-in user)
    $existingReaction = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id, reaction_type FROM reactions WHERE blog_id = $blogId AND user_id = $userId"));
    $reactionType = $_POST['reaction'];
    if ($existingReaction) {
        $existingReactionId = $existingReaction['id'];
        mysqli_query($conn, "DELETE FROM reactions WHERE id = $existingReactionId");
    } else {
        mysqli_query($conn, "INSERT INTO reactions (blog_id, user_id, reaction_type) VALUES ($blogId, $userId, '$reactionType')");
    }

    header("Location: index.php");
}

mysqli_close($conn);
?>
