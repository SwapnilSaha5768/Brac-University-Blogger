<?php
    session_start();
    if (!isset($_SESSION["user1"])) {
        header("Location: login.php");
        
}
require_once "database.php";
$mysqli = $conn;

// follower count
function getFollowerCount($userId, $mysqli) {
    $query = "SELECT COUNT(*) AS count FROM follows WHERE following_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['count'];
}

// following count
function getFollowingCount($userId, $mysqli) {
    $query = "SELECT COUNT(*) AS count FROM follows WHERE follower_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['count'];
}

$username = $_SESSION["susername"];
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
    <link rel="stylesheet" href="profile-style.css">

    <title>Profile</title>
    
</head>
<body>
<div class="container">
  
  <div class="Profile">
    <img src="pic2.png" class="profile-pic">

    <h4>
        <?php
            echo "user: @".$_SESSION["susername"];
        ?>
    </h4>

    <!--<a href="follow.php" class="follow-btn">Follow</a>-->
    <a href="index.php" class="follow-btn">Home</a>
    <div class="row">
      <div>
        <p>Followers</p>
        <h2><?php echo $followerCount; ?></h2>
      </div>
      <div>
        <p>Following</p>
        <h2><?php echo $followingCount; ?></h2>
      </div>
    </div>
  </div>
</div>
</body>

</html>