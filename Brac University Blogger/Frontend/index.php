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
    
    <link rel="stylesheet" href="style2.css">
    <title>Brac University Blogger</title>
</head>
<div class="body-container">
        <div class="sidebar">
            <ul>
                <li>
                    <a href="index.php">
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
            <li>

                 <img src="uploads\default.png"class="profile-pic"> </li>
            <div class="user">
                <?php
                echo $_SESSION["fullname"]."<br>";
                echo "user: @".$_SESSION["username"];
                ?>
            </div>
        </div>            
        
        <div class="main-content flex-column">
            <div class="options">
                <a href="search.php" class="explore">
                    <h2>Explore</h2>
                </a>
                <a href="" class="explore">
                    <h2>Interest</h2>
                </a>
                <a href="follow.php" class="explore">
                    <h2>Follow</h2>
                </a>
                <!--<div class="options-search-button">
                    <input type="text" class="search-input" placeholder="Search">
                    *<button type="submit" class="search-button">
                        <i class="fa fa-search"></i>
                    </button>       
                </div> -->   
            </div>
            <div class="article-container flex-column">
                <article id="first" class="flex-column">
                    <div class="article-top flex-row">
                        <title>Post</title>
                    </div>
                    <div class="article-bottom">
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
            echo "<div class='post2'>";
            echo "<h7>{$row['fullname']}</h3>"."<br>";
            echo "Username: @"."<h7>{$row['username']}</h3>";
            echo "<h3>{$row['title']}</h5>"."<br>";
            echo "Description: "."<p>{$row['description']}</p>";
            echo "Category: "."<p>{$row['category']}</p>";
            echo "<small>{$row['timestamp_column']}</small>";
            echo "<form method='Post' action='handle_reaction.php'>";
            echo "<input type='hidden' name='blog_id' value={$row['id']}>";

            echo "<button type='submit' name='reaction' value='like'>";
            echo "Like ($likeCount)";
            echo "</button>";

            echo "<button type='submit' name='reaction' value='dislike'>";
            echo "Dislike ($dislikeCount)";
            echo "</button>";

            echo "</form>";
            echo "</div>";

        }
    } else {
        echo "No posts found.";
    }

    ?>
                    </div>
                    <div class="article-bottom">
                        <p></p>
                    </div>
                </article>

            </div>    
        </div>
        
        <div class="right-sidebar">
            <!-- <div class="container">
            </div> -->
            <div class="right-top-button-container">
                <button class="logout-button"><a href="logout.php">Sign Out</button></a>
              </div>
            <div class="postbox">
                <!-- <form class="search-form"> -->
                <form>
                    <div class="input">
                        <i class="fa fa-user" aria-hidden="true"></i>
                        <input type="text" placeholder="What's in your mind?">
                    </div>
                    <button class="Button">
                        Post
                    </button>
                </form>
            </div>
            
            <div class="notification">
                <h2>Calendar</h2>
                <iframe src="https://www.bracu.ac.bd/sites/default/files/academics/Year-planner/yp23.png" width="100%" height="600px" frameborder="0" allowfullscreen></iframe>
            </div>
        </div>
    </div>    

</body>


</html>