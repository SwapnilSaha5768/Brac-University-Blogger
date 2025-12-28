<?php
session_start();
if (!isset($_SESSION["user"])) {
   header("Location: login.php");
   die();
}

$username = $_SESSION["username"];
require_once "database.php";
$checkUserQue = "SELECT id FROM users WHERE username = '$username'";
$results = $conn->query($checkUserQue);
$uid = $results->fetch_assoc()['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $blogId = intval($_POST['blog_id']); // Sanitize

    $userId = $uid; 
    $existingReaction = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id, reaction_type FROM reactions WHERE blog_id = $blogId AND user_id = $userId"));
    $reactionType = $_POST['reaction']; // 'like' or 'dislike'
    
    if ($existingReaction) {
        $existingReactionId = $existingReaction['id'];
        mysqli_query($conn, "DELETE FROM reactions WHERE id = $existingReactionId");
    } else {
        mysqli_query($conn, "INSERT INTO reactions (blog_id, user_id, reaction_type) VALUES ($blogId, $userId, '$reactionType')");
    }

    // Dynamic Redirect
    $redirect = $_POST['redirect'] ?? 'index.php';
    header("Location: $redirect");
    exit();
}
?>

mysqli_close($conn);
?>
