<?php
session_start();
require_once "../includes/database.php";


if (!isset($_SESSION["user"]) || !isset($_POST['comment_id'])) {
    header("Location: ../index.php");

    die();
}

$commentId = intval($_POST['comment_id']);
$currentUser = $_SESSION['username'];

// Get Current User ID
$uRes = $conn->query("SELECT id FROM users WHERE username='$currentUser'");
$currentUserId = $uRes->fetch_assoc()['id'];

// Fetch Comment Details (to check owner) and Post Details (to check post owner)
$sql = "SELECT c.user_id as comment_owner, p.user_id as post_owner 
        FROM comments c 
        JOIN post p ON c.post_id = p.id 
        WHERE c.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $commentId);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    // Check Permissions: Either I wrote the comment OR I own the post
    if ($currentUserId == $row['comment_owner'] || $currentUserId == $row['post_owner']) {
        // Safe to delete
        // If it's a parent comment, children might be orphaned. 
        // Option 1: Cascade delete (if DB supports it or manual).
        // Option 2: Delete children too.
        // Let's delete children manually just in case DB cascade isn't set up for parent_id self-ref.
        
        $del = $conn->prepare("DELETE FROM comments WHERE id = ? OR parent_id = ?");
        $del->bind_param("ii", $commentId, $commentId);
        $del->execute();
    }
}

// Redirect back
if(isset($_SERVER['HTTP_REFERER'])) {
    header("Location: " . $_SERVER['HTTP_REFERER']);
} else {
    header("Location: ../index.php");

}
die();
?>
