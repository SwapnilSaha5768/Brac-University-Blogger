<?php
    session_start();
    if (!isset($_SESSION["user"])) {
        header("Location: login.php");
        
}
require_once "database.php";
$mysqli = $conn;

function getFollowerCount($userId, $mysqli) {
    $query = "SELECT COUNT(*) AS count FROM follows WHERE following_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['count'];
}

function getFollowingCount($userId, $mysqli) {
    $query = "SELECT COUNT(*) AS count FROM follows WHERE follower_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['count'];
}

$username = $_SESSION["username"];
$queryUser = "SELECT id FROM users WHERE username = '$username'";
$resultUser = $mysqli->query($queryUser);
$userRow = $resultUser->fetch_assoc();

if ($userRow) {
  $userId = $userRow['id'];
  $followerCount = getFollowerCount($userId, $mysqli);
  $followingCount = getFollowingCount($userId, $mysqli);
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="assets/css/main.css">
    <title>Profile</title>
</head>
<body>

<div class="dashboard-container">
    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
            
            <!-- Left Column: User's Posts -->
            <div class="profile-posts" style="width: 100%; max-width: none;">
                <div class="options-bar">
                    <a href="profile.php" class="active">My Posts</a>
                    <a href="#">Archived</a>
                </div>

                <?php
                $userId = $userRow['id'];
                $sql = "SELECT post.*, users.fullname, users.username 
                        FROM post 
                        JOIN users ON post.user_id = users.id 
                        WHERE post.user_id = $userId 
                        ORDER BY post.timestamp_column DESC";
                $result = $mysqli->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $blogId = $row['id'];
                        // Fetch reaction counts
                        $likeCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS count FROM reactions WHERE blog_id = $blogId AND reaction_type = 'like'"))['count'];
                        $dislikeCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS count FROM reactions WHERE blog_id = $blogId AND reaction_type = 'dislike'"))['count'];

                        // Post Card
                        echo "<div class='post-card'>";
                        
                        // Header
                        echo "<div class='post-header'>";
                        echo "<div>";
                        echo "<h3>" . htmlspecialchars($row['fullname']) . " <small>(@" . htmlspecialchars($row['username']) . ")</small></h3>";
                        echo "<small>" . $row['timestamp_column'] . "</small>";
                        echo "</div>";

                        // Edit/Delete Options (Since this is profile.php, user is always owner)
                        echo "<div class='post-options' style='display:flex; gap:10px;'>";
                        echo "<a href='edit_post.php?id=$blogId' class='btn-sm btn-outline-primary' style='border:1px solid #ddd; padding:5px 10px; border-radius:5px; font-size:0.8rem;'><i class='bx bx-edit'></i> Edit</a>";
                        
                        echo "<form action='delete_post.php' method='POST' style='display:inline;' onsubmit='return confirm(\"Are you sure you want to delete this post?\");'>";
                        echo "<input type='hidden' name='id' value='$blogId'>";
                        echo "<button type='submit' class='btn-sm btn-outline-danger' style='border:1px solid #ddd; background:none; color:var(--danger-color); padding:5px 10px; border-radius:5px; font-size:0.8rem; cursor:pointer;'><i class='bx bx-trash'></i> Delete</button>";
                        echo "</form>";
                        echo "</div>";

                        echo "</div>";

                        // Content
                        echo "<div class='post-content'>";
                        echo "<h5>" . htmlspecialchars($row['title']) . "</h5>";
                        // Category tag
                        echo "<span class='category'>" . htmlspecialchars($row['category']) . "</span>";
                        echo "<p>" . nl2br(htmlspecialchars($row['description'])) . "</p>";
                        echo "</div>";

                        // Actions
                        echo "<div class='post-actions'>";
                        echo "<form method='Post' action='handle_reaction.php' style='display:flex; gap:10px;'>";
                        echo "<input type='hidden' name='blog_id' value=" . $row['id'] . ">";
                        echo "<button type='submit' name='reaction' value='like' class='action-btn'><i class='bx bx-like'></i> Like ($likeCount)</button>";
                        echo "<button type='submit' name='reaction' value='dislike' class='action-btn'><i class='bx bx-dislike'></i> Dislike ($dislikeCount)</button>";
                        echo "</form>";
                        echo "</div>";

                        // Comments Section (View Only)
                        echo "<div class='comment-section'>";
                        echo "<h6>Comments</h6>";
                        
                        $commentSql = "SELECT comments.*, users.fullname, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE post_id = $blogId ORDER BY created_at ASC";
                        $commentResult = mysqli_query($conn, $commentSql);
                        
                        if (mysqli_num_rows($commentResult) > 0) {
                            echo "<div class='comment-list'>";
                            while ($comment = mysqli_fetch_assoc($commentResult)) {
                                $cName = htmlspecialchars($comment['fullname']);
                                $cBody = htmlspecialchars($comment['comment']);
                                
                                echo "<div class='comment-item'>";
                                echo "<strong>$cName</strong> <span> $cBody </span>";
                                echo "</div>";
                            }
                            echo "</div>";
                        } else {
                            echo "<p class='text-muted' style='font-size:0.85rem;'>No comments yet.</p>";
                        }
                        echo "</div>"; // End Comments

                        echo "</div>"; // End Post Card
                    }
                } else {
                    echo "<div class='alert alert-info'>You haven't posted anything yet. <a href='create.php'>Create a post</a></div>";
                }
                ?>
            </div>
    </div>

    <!-- Right Sidebar (Profile Card) -->
    <div class="right-sidebar profile-sidebar">
             <div class="profile-card" style="width: 100%; box-shadow: none; border: none; background: transparent;">
               <div class="profile-header-banner" style="border-radius: 12px 12px 0 0;"></div>
               <div style="position: relative;">
                   <img src="uploads/default.png" class="profile-pic" style="margin-top: -50px;">
               </div>
               <h1 style="margin-top: 10px; font-size: 1.3rem;">
                   <?php
                       echo $_SESSION["fullname"];
                   ?>
               </h1>
               <h4 style="font-size: 0.9rem;">
                   <?php
                       echo "@".$_SESSION["username"];
                   ?>
               </h4>

               <div class="profile-stats" style="margin-top: 20px; padding-top: 20px;">
                 <div class="stat-item">
                   <p>Followers</p>
                   <h2><?php echo $followerCount; ?></h2>
                 </div>
                 <div class="stat-item">
                   <p>Following</p>
                   <h2><?php echo $followingCount; ?></h2>
                 </div>
               </div>
             </div>
    </div>

</body>
</html>
