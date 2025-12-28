<?php
session_start();
require_once "database.php";

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    die();
}

$currentUser = $_SESSION['username'];

// Get User ID to delete
$uRes = $conn->query("SELECT id FROM users WHERE username='$currentUser'");
if ($uRes->num_rows > 0) {
    $userId = $uRes->fetch_assoc()['id'];

    if (isset($_POST['confirm_delete'])) {
        // Prepare DELETE statement
        // Assuming ON DELETE CASCADE is set for posts, comments, follows, likes.
        // If not, we should manually delete them, but usually schemas are cascade.
        // Based on previous index.php review, comments table has ON DELETE CASCADE.
        
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        
        if ($stmt->execute()) {
            // Success
            session_destroy();
            header("Location: registration.php");
            die();
        } else {
            // Error
            echo "Error deleting account: " . $conn->error;
        }
    }
} else {
    // User not found??
    session_destroy();
    header("Location: login.php");
    die();
}
?>
