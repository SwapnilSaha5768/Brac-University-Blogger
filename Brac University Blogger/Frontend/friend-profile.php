<?php
    session_start();
    require_once "database.php";
    $mysqli = $conn;

    if (!isset($_SESSION["user"])) {
        header("Location: login.php");
        die();
    }

    $current_user_id = 0;
    // Get current logged in user ID
    $currUser = $_SESSION['username'];
    $currQuery = "SELECT id FROM users WHERE username = '$currUser'";
    $currRes = $mysqli->query($currQuery);
    if($currRow = $currRes->fetch_assoc()) {
        $current_user_id = $currRow['id'];
    }

    $userId = 0;
    $username = "";
    $fullname = "";
    
    // Determine which profile to show
    if (isset($_GET['user_id'])) {
        $userId = intval($_GET['user_id']);
        $queryUser = "SELECT * FROM users WHERE id = $userId";
    } elseif (isset($_SESSION["susername"])) {
        $username = $_SESSION["susername"];
        $queryUser = "SELECT * FROM users WHERE username = '$username'";
    } else {
        // Fallback or error
        header("Location: search.php");
        die();
    }

    $resultUser = $mysqli->query($queryUser);
    $userRow = $resultUser->fetch_assoc();

    if ($userRow) {
      $userId = $userRow['id'];
      $username = $userRow['username'];
      $fullname = $userRow['fullname'];
    } else {
        echo "User not found.";
        die();
    }

    // Follow Logic Check
    $isFollowing = false;
    $checkFollow = "SELECT * FROM follows WHERE follower_id = $current_user_id AND following_id = $userId";
    $followRes = $mysqli->query($checkFollow);
    if ($followRes->num_rows > 0) {
        $isFollowing = true;
    }

    // Handle Follow/Unfollow Action
    if (isset($_POST['toggle_follow'])) {
        if ($isFollowing) {
            $del = "DELETE FROM follows WHERE follower_id = $current_user_id AND following_id = $userId";
            $mysqli->query($del);
            $isFollowing = false;
        } else {
            $ins = "INSERT INTO follows (follower_id, following_id) VALUES ($current_user_id, $userId)";
            $mysqli->query($ins);
            $isFollowing = true;
        }
        // Refresh to reflect changes
        header("Location: friend-profile.php?user_id=$userId");
        die();
    }

    // Counts
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

    $followerCount = getFollowerCount($userId, $mysqli);
    $followingCount = getFollowingCount($userId, $mysqli);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($fullname); ?>'s Profile</title>
    <link rel="stylesheet" href="assets/css/main.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>

<div class="profile-page-container">
  
  <!-- Left Column: User's Posts -->
  <div class="profile-posts">
      <?php
      $sql = "SELECT post.*, users.fullname, users.username 
              FROM post 
              JOIN users ON post.user_id = users.id 
              WHERE post.user_id = $userId 
              ORDER BY post.timestamp_column DESC";
      $result = $mysqli->query($sql);

      if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
              $blogId = $row['id'];
              $likeCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS count FROM reactions WHERE blog_id = $blogId AND reaction_type = 'like'"))['count'];
              $dislikeCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS count FROM reactions WHERE blog_id = $blogId AND reaction_type = 'dislike'"))['count'];

              echo "<div class='post-card'>";
              
              // Header
              echo "<div class='post-header'>";
              echo "<div>";
              echo "<h3>" . htmlspecialchars($row['fullname']) . " <small>(@" . htmlspecialchars($row['username']) . ")</small></h3>";
              echo "<small>" . $row['timestamp_column'] . "</small>";
              echo "</div>";
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
              echo "<input type='hidden' name='redirect' value='friend-profile.php?user_id=$userId'>";
              echo "<button type='submit' name='reaction' value='like' class='action-btn'><i class='bx bx-like'></i> Like ($likeCount)</button>";
              echo "<button type='submit' name='reaction' value='dislike' class='action-btn'><i class='bx bx-dislike'></i> Dislike ($dislikeCount)</button>";
              echo "</form>";
              echo "</div>";

              // Comments (View Only)
              echo "<div class='comment-section'>";
              echo "<h6>Comments</h6>";
              $commentSql = "SELECT comments.*, users.fullname, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE post_id = $blogId ORDER BY created_at ASC";
              $commentResult = mysqli_query($conn, $commentSql);
              if (mysqli_num_rows($commentResult) > 0) {
                  echo "<div class='comment-list'>";
                  while ($comment = mysqli_fetch_assoc($commentResult)) {
                      echo "<div class='comment-item'>";
                      echo "<strong>" . htmlspecialchars($comment['fullname']) . "</strong> <span> " . htmlspecialchars($comment['comment']) . " </span>";
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
          echo "<div class='alert alert-info'>This user hasn't posted anything yet.</div>";
      }
      ?>
  </div>

  <!-- Right Column: Profile Card -->
  <div class="profile-card">
    <img src="uploads/default.png" class="profile-pic">
    
    <h1><?php echo htmlspecialchars($fullname); ?></h1>
    <h4><?php echo "user: @" . htmlspecialchars($username); ?></h4>

    <!-- Follow / Unfollow Button -->
    <?php if ($userId != $current_user_id): ?>
        <form method="post">
            <button type="submit" name="toggle_follow" class="follow-btn" style="background:<?php echo $isFollowing ? '#fff' : 'var(--primary-color)'; ?>; color:<?php echo $isFollowing ? 'var(--primary-color)' : '#fff'; ?>; cursor:pointer;">
                <?php echo $isFollowing ? "Unfollow" : "Follow"; ?>
            </button>
        </form>
    <?php endif; ?>
    
    <div class="mt-4">
        <a href="index.php" style="color:var(--text-light); text-decoration:underline;">Back to Home</a>
    </div>

    <div class="profile-stats">
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