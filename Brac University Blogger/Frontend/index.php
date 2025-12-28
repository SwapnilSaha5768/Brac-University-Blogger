<?php
    session_start();
    if (!isset($_SESSION["user"])) {
        header("Location: login.php");   
    }
    
    require_once "database.php";
    $msg = "";

    // Check and update database schema for replies
    $checkCol = $conn->query("SHOW COLUMNS FROM comments LIKE 'parent_id'");
    if($checkCol->num_rows == 0) {
        $conn->query("ALTER TABLE comments ADD COLUMN parent_id INT DEFAULT NULL");
    }
    
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
        parent_id INT DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (post_id) REFERENCES post(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    // Handle Comment Submission
    if (isset($_POST['submit_comment'])) {
        $comment_content = $_POST['comment_content'] ?? "";
        $post_id = $_POST['post_id'] ?? "";
        $parent_id = !empty($_POST['parent_id']) ? $_POST['parent_id'] : NULL;
        
        if (!empty(trim($comment_content)) && !empty($post_id)) {
            $follower = $_SESSION["username"];
            $checkUserQue = "SELECT id FROM users WHERE username = '$follower'";
            $results = $conn->query($checkUserQue);
            $userID = $results->fetch_assoc()['id'];
            
            $sql = "INSERT INTO comments (post_id, user_id, comment, parent_id) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_stmt_init($conn);
            $prepareStmt = mysqli_stmt_prepare($stmt, $sql);
            if ($prepareStmt) {
                mysqli_stmt_bind_param($stmt, "iisi", $post_id, $userID, $comment_content, $parent_id);
                mysqli_stmt_execute($stmt);
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
            echo "<p>" . nl2br(htmlspecialchars($row['description'])) . "</p>";
            echo "<span class='category'>" . htmlspecialchars($row['category']) . "</span>";
            echo "</div>";

            // Actions
            echo "<div class='post-actions'>";
            echo "<form method='Post' action='handle_reaction.php' style='display:flex; gap:10px;'>";
            echo "<input type='hidden' name='blog_id' value=" . $row['id'] . ">";

            echo "<button type='submit' name='reaction' value='like' class='action-btn'>";
            echo "<i class='bx bx-like'></i> ($likeCount)";
            echo "</button>";

            echo "<button type='submit' name='reaction' value='dislike' class='action-btn'>";
            echo "<i class='bx bx-dislike'></i> ($dislikeCount)";
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
            
            $allComments = [];
            if (mysqli_num_rows($commentResult) > 0) {
                while ($c = mysqli_fetch_assoc($commentResult)) {
                    $allComments[] = $c;
                }
            }

            // Function to render a single comment item
            function renderCommentItem($comment, $isReply = false, $postOwnerId = 0, $currentUserId = 0) {
                $cName = htmlspecialchars($comment['fullname']);
                $cBody = htmlspecialchars($comment['comment']);
                $cId = $comment['id'];
                $styleClass = $isReply ? 'comment-item nested-reply' : 'comment-item';
                
                echo "<div class='$styleClass' style='position: relative; padding-right: 30px;'>";
                echo "<strong>$cName</strong> <span> $cBody </span>";
                
                // Delete Button Permission Check
                // User can delete if they own the comment OR they own the post
                if ($currentUserId == $comment['user_id'] || $currentUserId == $postOwnerId) {
                    echo "<form action='delete_comment.php' method='POST' style='display:inline; position:absolute; top:8px; right:8px;' onsubmit='return confirm(\"Delete this comment?\");'>";
                    echo "<input type='hidden' name='comment_id' value='$cId'>";
                    echo "<button type='submit' style='border:none; background:none; color:#ff6b6b; cursor:pointer; font-size:1.1rem;' title='Delete Comment'><i class='bx bx-trash'></i></button>";
                    echo "</form>";
                }

                if (!$isReply) { // Only allow 1 level of nesting for simplicity
                     echo "<span class='reply-link' onclick='toggleReply($cId)'>Reply</span>";
                     // Hidden Reply Form
                     echo "<form id='reply-form-$cId' action='index.php' method='post' style='display:none; margin-top:10px;'>";
                     echo "<input type='hidden' name='post_id' value='".$comment['post_id']."'>";
                     echo "<input type='hidden' name='parent_id' value='$cId'>";
                     echo "<div class='comment-box' style='margin-bottom:0;'>";
                     echo "<input type='text' name='comment_content' placeholder='Write a reply...' required>";
                     echo "<button type='submit' name='submit_comment'><i class='bx bx-send'></i></button>";
                     echo "</div>";
                     echo "</form>";
                }
                echo "</div>";
            }

            // Filter parent comments
            foreach ($allComments as $comment) {
                if (empty($comment['parent_id'])) {
                    renderCommentItem($comment, false, $row['user_id'], $ui);
                    // Find and render replies
                    foreach ($allComments as $reply) {
                        if ($reply['parent_id'] == $comment['id']) {
                            renderCommentItem($reply, true, $row['user_id'], $ui);
                        }
                    }
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
            
<script>
function toggleReply(id) {
    var form = document.getElementById('reply-form-' + id);
    if (form.style.display === 'none') {
        form.style.display = 'block';
    } else {
        form.style.display = 'none';
    }
}
</script>

            <!-- Who to Follow Widget -->
            <div class="post-card" style="padding: 20px;">
                <h5 style="margin-bottom: 20px; font-weight: 700; color: var(--text-color);">Who to Follow</h5>
                <div class="follow-list">
                    <?php
                    // Get list of users NOT followed by current user
                    $follower = $_SESSION["username"];
                    $uRes = $conn->query("SELECT id FROM users WHERE username='$follower'");
                    $me = $uRes->fetch_assoc()['id'];
                    $followSql = "SELECT * FROM users 
                                  WHERE id != $me 
                                  AND id NOT IN (SELECT following_id FROM follows WHERE follower_id = $me) 
                                  ORDER BY RAND() LIMIT 3";
                    $followResult = mysqli_query($conn, $followSql);

                    if (mysqli_num_rows($followResult) > 0) {
                        while ($fRow = mysqli_fetch_assoc($followResult)) {
                            // Using standard profile pic if none exists (simplified logic)
                            // Assuming all have a reachable profile pic or default
                            ?>
                            <div class="follow-item" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <img src="uploads/default.png" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                    <div>
                                        <h6 style="margin: 0; font-size: 0.95rem; font-weight: 600;"><?php echo htmlspecialchars($fRow['fullname']); ?></h6>
                                        <small style="color: var(--text-light); font-size: 0.8rem;">@<?php echo htmlspecialchars($fRow['username']); ?></small>
                                    </div>
                                </div>
                                <a href="friend-profile.php?user_id=<?php echo $fRow['id']; ?>" class="btn-sm" style="border: 1px solid var(--primary-color); color: var(--primary-color); border-radius: 20px; padding: 5px 15px; font-size: 0.8rem; font-weight: 600;">View</a>
                            </div>
                            <?php
                        }
                    } else {
                        echo "<p style='color: var(--text-light); font-size: 0.9rem;'>No new suggestions.</p>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>    

</body>
</html>