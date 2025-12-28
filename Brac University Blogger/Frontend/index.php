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
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="assets/css/main.css">
    <title>Brac University Blogger</title>
</head>
<body>
<div class="dashboard-container">
        <div class="sidebar">
            <ul class="sidebar-menu">
                <li>
                    <a href="index.php" class="active">
                        <i class="bx bx-home"></i>
                        <span class="nav-item">Home</span>
                    </a>
                </li>
                <li>
                    <a href="profile.php">
                        <i class="bx bxs-face-mask"></i>
                        <span class="nav-item">Profile</span>
                    </a>
                </li>
                <li>
                    <a href="search.php">
                        <i class="bx bx-search"></i>
                        <span class="nav-item">Search</span>
                    </a>
                </li>
                <li>
                    <a href="create.php">
                        <i class="bx bx-pencil"></i>
                        <span class="nav-item">Write Post</span>
                    </a>
                </li>
                <li>
                    <a href="logout.php">
                        <i class="bx bx-log-out"></i>
                        <span class="nav-item">Sign Out</span>
                    </a>
                </li>
            </ul>
            
            <div class="user-info">
                 <img src="uploads/default.png" class="profile-pic-small">
                 <div>
                    <?php
                    echo "<strong>" . $_SESSION["fullname"] . "</strong><br>";
                    echo "<small>@" . $_SESSION["username"] . "</small>";
                    ?>
                 </div>
            </div>
        </div>            
        
        <div class="main-content">
            <div class="options-bar">
                <a href="search.php">Explore</a>
                <a href="#">Interest</a>
                <a href="follow.php">Follow</a>
            </div>
            
            <div class="posts-list">
    <?php
    $follower = $_SESSION["username"];
    require_once "database.php";

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
            echo "<h3>" . htmlspecialchars($row['fullname']) . " <small>(@" . htmlspecialchars($row['username']) . ")</small></h3>";
            echo "<small>" . $row['timestamp_column'] . "</small>";
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
                <form>
                    <div class="input">
                        <input type="text" placeholder="What's in your mind?">
                    </div>
                    <button>Post</button>
                    <div style="clear:both;"></div>
                </form>
            </div>
            
            <div class="calendar-widget">
                <iframe src="https://www.bracu.ac.bd/sites/default/files/academics/Year-planner/yp23.png" width="100%" height="600px" frameborder="0" allowfullscreen></iframe>
            </div>
        </div>
    </div>    

</body>
</html>