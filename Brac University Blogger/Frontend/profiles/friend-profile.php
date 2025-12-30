<?php
    session_start();
    header("Cache-Control: no-cache, no-store, must-revalidate");
    header("Pragma: no-cache");
    header("Expires: 0");
    require_once "../includes/database.php";

    $mysqli = $conn;

    if (!isset($_SESSION["user"])) {
        header("Location: ../auth/login.php");

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
        header("Location: ../search.php");

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../assets/css/main.css">

    
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
</head>
<body>

<div class="dashboard-container">
    <!-- Sidebar -->
    <?php 
    $basePath = "../";
    include '../includes/sidebar.php'; 
    ?>


    <!-- Main Content -->
    <div class="main-content">
          
          <!-- Left Column: User's Posts -->
          <div class="profile-posts" style="width: 100%; max-width: none;">
              
              <div class="options-bar">
                  <span class="active">Posts</span>
              </div>

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
                      echo "<form method='Post' action='../posts/handle_reaction.php' style='display:flex; gap:10px;'>";

                      echo "<input type='hidden' name='blog_id' value=" . $row['id'] . ">";
                      echo "<input type='hidden' name='redirect' value='../profiles/friend-profile.php?user_id=$userId'>";

                      echo "<button type='submit' name='reaction' value='like' class='action-btn'><i class='bx bx-like'></i> ($likeCount)</button>";
                      echo "<button type='submit' name='reaction' value='dislike' class='action-btn'><i class='bx bx-dislike'></i> ($dislikeCount)</button>";
                      echo "</form>";
                      echo "</div>";

                      // Comments (View Only + Reply)
                      echo "<div class='comment-section'>";
                      echo "<h6>Comments</h6>";
                      
                      // Fetch all comments (including replies)
                      $commentSql = "SELECT comments.*, users.fullname, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE post_id = $blogId ORDER BY created_at ASC";
                      $commentResult = mysqli_query($conn, $commentSql);
                      $allComments = [];
                      if (mysqli_num_rows($commentResult) > 0) {
                          while ($c = mysqli_fetch_assoc($commentResult)) {
                              $allComments[] = $c;
                          }
                      }
                      
                      if (!empty($allComments)) {
                          echo "<div class='comment-list'>";
                          // Helper function for rendering
                           // Simplified logic since function definition inside loop is bad.
                           // Helper function for rendering
                          foreach ($allComments as $comment) {
                              if (empty($comment['parent_id'])) {
                                  // Parent
                                  $cName = htmlspecialchars($comment['fullname']);
                                  $cBody = htmlspecialchars($comment['comment']);
                                  $cId = $comment['id'];
                                  
                                  echo "<div class='comment-item' style='position: relative; padding-right: 30px;'>";
                                  echo "<strong>$cName</strong> <span> $cBody </span>";
                                  
                                  // Delete Button
                                  if ($current_user_id == $comment['user_id'] || $current_user_id == $row['user_id']) {
                                      echo "<form action='../posts/delete_comment.php' method='POST' style='display:inline; position:absolute; top:8px; right:8px;' onsubmit='return confirm(\"Delete this comment?\");'>";

                                      echo "<input type='hidden' name='comment_id' value='$cId'>";
                                      echo "<button type='submit' style='border:none; background:none; color:#ff6b6b; cursor:pointer; font-size:1.1rem;' title='Delete Comment'><i class='bx bx-trash'></i></button>";
                                      echo "</form>";
                                  }

                                  echo "</div>";

                                  // Children
                                  foreach ($allComments as $reply) {
                                      if ($reply['parent_id'] == $comment['id']) {
                                          $rName = htmlspecialchars($reply['fullname']);
                                          $rBody = htmlspecialchars($reply['comment']);
                                          $rId = $reply['id'];
                                          
                                          echo "<div class='comment-item nested-reply' style='position: relative; padding-right: 30px;'>";
                                          echo "<strong>$rName</strong> <span> $rBody </span>";
                                          
                                          // Delete Button
                                          if ($current_user_id == $reply['user_id'] || $current_user_id == $row['user_id']) {
                                              echo "<form action='../posts/delete_comment.php' method='POST' style='display:inline; position:absolute; top:8px; right:8px;' onsubmit='return confirm(\"Delete this comment?\");'>";

                                              echo "<input type='hidden' name='comment_id' value='$rId'>";
                                              echo "<button type='submit' style='border:none; background:none; color:#ff6b6b; cursor:pointer; font-size:1.1rem;' title='Delete Comment'><i class='bx bx-trash'></i></button>";
                                              echo "</form>";
                                          }
                                          
                                          echo "</div>";
                                      }
                                  }
                              }
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
    </div>
    
    <!-- Right Sidebar (Profile Card) -->
    <div class="right-sidebar profile-sidebar">
          <div class="profile-card" style="width: 100%; box-shadow: none; border: none; background: transparent;">
            <div class="profile-header-banner" style="border-radius: 12px 12px 0 0;"></div>
            <div style="position: relative;">
                <img src="uploads/default.png" class="profile-pic" style="margin-top: -50px;">
            </div>
            
            <h1 style="margin-top: 10px; font-size: 1.3rem;"><?php echo htmlspecialchars($fullname); ?></h1>
            <h4 style="font-size: 0.9rem;"><?php echo "@" . htmlspecialchars($username); ?></h4>

            <!-- Follow / Unfollow Button -->
            <?php if ($userId != $current_user_id): ?>
                <form method="post" style="margin-top: 15px;">
                    <button type="submit" name="toggle_follow" class="follow-btn" style="
                        background: <?php echo $isFollowing ? '#fff' : 'var(--primary-color)'; ?>; 
                        color: <?php echo $isFollowing ? 'var(--primary-color)' : '#fff'; ?>; 
                        border: 1px solid var(--primary-color);
                        padding: 6px 20px;
                        border-radius: 20px;
                        font-weight: 600;
                        cursor: pointer;
                        font-size: 0.9rem;
                        transition: all 0.3s ease;
                    ">
                        <?php echo $isFollowing ? "Unfollow" : "Follow"; ?>
                    </button>
                </form>
            <?php endif; ?>

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
</div>

</body>
</html>