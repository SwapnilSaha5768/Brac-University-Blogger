<?php
    session_start();
    if (!isset($_SESSION["user"])) {
        header("Location: login.php");
        
}
$mysqli = new mysqli("localhost", "root", "", "bracuniversityblogger");

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
    <link rel="stylesheet" href="profile-style.css">

    <title>Profile</title>
    
</head>
<body>
<div class="container">
  
  <div class="Profile">
    <img src="uploads\default.png" class="profile-pic">
    <h1>
        <?php
            echo $_SESSION["fullname"]."<br>";
        ?>
    </h1>
    <h4>
        <?php
            echo "user: @".$_SESSION["username"];
        ?>
    </h4>

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


