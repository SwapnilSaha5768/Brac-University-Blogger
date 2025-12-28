<?php
    session_start();
    if (!isset($_SESSION["user"])) {
        header("Location: login.php");   
    }
    
    require_once "database.php";
    $msg = "";
    
    // Handle Quick Post
    if (isset($_POST['quick_publish'])) {
        $description = $_POST['quick_post_content'] ?? "";
        if (!empty(trim($description))) {
            $follower = $_SESSION["username"];
            $checkUserQue = "SELECT id FROM users WHERE username = '$follower'";
            $results = $conn->query($checkUserQue);
            $userID = $results->fetch_assoc()['id'];
            
            $title = "Status Update";
            $category = "Others";
            
            $sql = "INSERT INTO post (user_id, title, description, category) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_stmt_init($conn);
            $prepareStmt = mysqli_stmt_prepare($stmt, $sql);
            if ($prepareStmt) {
                mysqli_stmt_bind_param($stmt, "isss", $userID, $title, $description, $category);
                mysqli_stmt_execute($stmt);
                $msg = "<div class='alert alert-success' style='padding: 10px; margin-bottom: 10px; font-size: 0.9rem;'>Posted successfully!</div>";
            } else {
                $msg = "<div class='alert alert-danger' style='padding: 10px; margin-bottom: 10px; font-size: 0.9rem;'>Something went wrong.</div>";
            }
        }
    }

    // Create comments table if not exists (Safety check)
    $conn->query("CREATE TABLE IF NOT EXISTS comments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        post_id INT NOT NULL,
        user_id INT NOT NULL,
        comment TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (post_id) REFERENCES post(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    // Handle Comment Submission
    if (isset($_POST['submit_comment'])) {
        $comment_content = $_POST['comment_content'] ?? "";
        $post_id = $_POST['post_id'] ?? "";
        
        if (!empty(trim($comment_content)) && !empty($post_id)) {
            $follower = $_SESSION["username"];
            $checkUserQue = "SELECT id FROM users WHERE username = '$follower'";
            $results = $conn->query($checkUserQue);
            $userID = $results->fetch_assoc()['id'];
            
            $sql = "INSERT INTO comments (post_id, user_id, comment) VALUES (?, ?, ?)";
            $stmt = mysqli_stmt_init($conn);
            $prepareStmt = mysqli_stmt_prepare($stmt, $sql);
            if ($prepareStmt) {
                mysqli_stmt_bind_param($stmt, "iis", $post_id, $userID, $comment_content);
                mysqli_stmt_execute($stmt);
                // Redirect to avoid resubmission
                header("Location: index.php"); 
                die();
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="assets/css/main.css">
    <title>Brac University Blogger</title>
</head>
<body>
<div class="dashboard-container">
        <?php include 'sidebar.php'; ?>            
        
        <div class="main-content">
            <div class="options-bar">
                <a href="explore.php">Explore</a>
                <a href="#">Interest</a>
            </div>
            
            <div class="posts-list">
    <?php
    $follower = $_SESSION["username"];
    $checkUserQue = "SELECT id FROM users WHERE username = '$follower'";
    $results = $conn->query($checkUserQue);
    $ui = $results->fetch_assoc()['id'];

    $sql = "SELECT post.*, users.fullname , users.username 
    FROM post 
    JOIN users ON post.user_id = users.id 
    WHERE post.user_id = $ui OR post.user_id IN (
        SELECT following_id FROM follows WHERE follower_id = $ui
    )
    ORDER BY post.timestamp_column DESC";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $blogId = $row['id'];
            $likeCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS count FROM reactions WHERE
             blog_id = $blogId AND reaction_type = 'like'"))['count'];
            $dislikeCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS count FROM 
            reactions WHERE blog_id = $blogId AND reaction_type = 'dislike'"))['count'];
            
            // Post Card Structure
            echo "<div class='post-card'>";
            
            // Header
            echo "<div class='post-header'>";
            echo "<div>";
            echo "<h3>" . htmlspecialchars($row['fullname']) . " <small>(@" . htmlspecialchars($row['username']) . ")</small></h3>";
            echo "<small>" . $row['timestamp_column'] . "</small>";
            echo "</div>";
            
            // Edit/Delete Options for Owner
            if ($_SESSION['username'] === $row['username']) {
                echo "<div class='post-options' style='display:flex; gap:10px;'>";
                // Edit Button
                echo "<a href='edit_post.php?id=$blogId' class='btn-sm btn-outline-primary' style='border:1px solid #ddd; padding:5px 10px; border-radius:5px; font-size:0.8rem;'><i class='bx bx-edit'></i> Edit</a>";
                
                // Delete Button (Form for safety)
                echo "<form action='delete_post.php' method='POST' style='display:inline;' onsubmit='return confirm(\"Are you sure you want to delete this post?\");'>";
                echo "<input type='hidden' name='id' value='$blogId'>";
                echo "<button type='submit' class='btn-sm btn-outline-danger' style='border:1px solid #ddd; background:none; color:var(--danger-color); padding:5px 10px; border-radius:5px; font-size:0.8rem; cursor:pointer;'><i class='bx bx-trash'></i> Delete</button>";
                echo "</form>";
                echo "</div>";
            }
            echo "</div>";

            // Content
            echo "<div class='post-content'>";
            echo "<h5>" . htmlspecialchars($row['title']) . "</h5>";
            echo "<span class='category'>" . htmlspecialchars($row['category']) . "</span>";
            echo "<p>" . nl2br(htmlspecialchars($row['description'])) . "</p>";
            echo "</div>";

            // Actions
            echo "<div class='post-actions'>";
            echo "<form method='Post' action='handle_reaction.php' style='display:flex; gap:10px;'>";
            echo "<input type='hidden' name='blog_id' value=" . $row['id'] . ">";

            echo "<button type='submit' name='reaction' value='like' class='action-btn'>";
            echo "<i class='bx bx-like'></i> Like ($likeCount)";
            echo "</button>";

            echo "<button type='submit' name='reaction' value='dislike' class='action-btn'>";
            echo "<i class='bx bx-dislike'></i> Dislike ($dislikeCount)";
            echo "</button>";

            echo "</form>";
            echo "</div>"; // End Actions
            
            // --- Comments Section ---
            echo "<div class='comment-section'>";
            
            // Input
            echo "<form action='index.php' method='post'>";
            echo "<div class='comment-box'>";
            echo "<input type='hidden' name='post_id' value='$blogId'>";
            echo "<input type='text' name='comment_content' placeholder='Write a comment...' required>";
            echo "<button type='submit' name='submit_comment'><i class='bx bx-send'></i></button>";
            echo "</div>";
            echo "</form>";

            // List Comments
            echo "<div class='comment-list'>";
            $commentSql = "SELECT comments.*, users.fullname, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE post_id = $blogId ORDER BY created_at ASC";
            $commentResult = mysqli_query($conn, $commentSql);
            if (mysqli_num_rows($commentResult) > 0) {
                while ($comment = mysqli_fetch_assoc($commentResult)) {
                    $cName = htmlspecialchars($comment['fullname']);
                    $cUser = htmlspecialchars($comment['username']);
                    $cBody = htmlspecialchars($comment['comment']);
                    
                    echo "<div class='comment-item'>";
                    echo "<strong>$cName</strong> ($cUser)";
                    echo "<span> $cBody </span>";
                    echo "</div>";
                }
            }
            echo "</div>"; // End List
            
            echo "</div>"; // End Comment Section

            echo "</div>"; // End Post Card

        }
    } else {
        echo "<p>No posts found.</p>";
    }

    ?>
            </div>    
        </div>
        
        <div class="right-sidebar">
            <div class="post-box">
                <?php echo $msg; ?>
                <form action="index.php" method="post">
                    <div class="input">
                        <input type="text" name="quick_post_content" placeholder="What's in your mind?" required>
                    </div>
                    <button type="submit" name="quick_publish">Post</button>
                    <div style="clear:both;"></div>
                </form>
            </div>
            
            <div class="calendar-widget">
                <iframe src="https://www.bracu.ac.bd/sites/default/files/uploads/2025/10/21/Year%20Planner%202025-Version-Final-summer25v2.jpg" width="100%" height="600px" frameborder="0" allowfullscreen></iframe>
            </div>
        </div>
    </div>    

</body>
</html>