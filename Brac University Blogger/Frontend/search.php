<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="assets/css/main.css">
	<title>Search Bar</title>
</head>
<body>

<div class="auth-body">
    <div class="auth-container">
        <div class="auth-header">
            <h1>Search User</h1>
        </div>
        <form method="post">
            <div class="form-group">
                <label class="form-label" style="display:block; margin-bottom:5px;">Username</label>
                <input type="text" placeholder="Enter Username:" name="search" class="form-control" required>
            </div>
            <div class="form-group">
                <input type="submit" value="Search" name="submit" class="btn-primary">
            </div>
            <div class="text-center mt-4">
                <a href="index.php" style="color:var(--primary-color);">Back to Home</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>


<?php

require_once "database.php";
if (isset($_POST["submit"])) {
	$search_username = $_POST["search"];


$search_username = mysqli_real_escape_string($conn, $search_username);

$sql = "SELECT * FROM users WHERE username = '$search_username'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user_profile = $result->fetch_assoc();

	session_start();
	$_SESSION['susername'] = $search_username;
	$_SESSION["user1"] = "yes";
    header("Location: friend-profile.php?user_id=" . $user_profile['user_id']);
	die();
    exit();
} else {

    echo "User not found";
}

$conn->close();
}
?>