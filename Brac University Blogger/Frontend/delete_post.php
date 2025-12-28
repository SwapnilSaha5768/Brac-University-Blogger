<?php
session_start();
require_once "database.php";

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $postId = $_POST['id'];
    $currentUser = $_SESSION['username'];

    // Security Check: Ensure the user owns the post
    // First, get the user ID of the current logged-in user
    $userQuery = "SELECT id FROM users WHERE username = '$currentUser'";
    $userResult = $conn->query($userQuery);
    $userRow = $userResult->fetch_assoc();
    $userId = $userRow['id'];

    if ($userId) {
        // Check if the post belongs to this user
        $checkPostQuery = "SELECT * FROM post WHERE id = ? AND user_id = ?";
        $stmt = mysqli_stmt_init($conn);
        if (mysqli_stmt_prepare($stmt, $checkPostQuery)) {
            mysqli_stmt_bind_param($stmt, "ii", $postId, $userId);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($result->num_rows > 0) {
                // Post belongs to user, proceed to delete
                $deleteQuery = "DELETE FROM post WHERE id = ?";
                if (mysqli_stmt_prepare($stmt, $deleteQuery)) {
                    mysqli_stmt_bind_param($stmt, "i", $postId);
                    mysqli_stmt_execute($stmt);
                    
                    // Redirect back to the previous page (index or profile)
                    $previousPage = $_SERVER['HTTP_REFERER'] ?? 'index.php';
                    header("Location: $previousPage");
                    exit();
                } else {
                    echo "Error deleting post.";
                }
            } else {
                echo "Unauthorized action.";
            }
        }
    }
} else {
    header("Location: index.php");
    exit();
}
?>
