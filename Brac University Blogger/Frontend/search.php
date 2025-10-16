<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="style.css">
	<title>Search Bar</title>
</head>
<body>

<form method="post">
<label>Search</label>
		<div class="form-group">
            <input type="text" placeholder="Enter Username:" name="search">
        </div>
        <div class="form-btn">
            <input type="submit" value="submit" name="submit">
			<a href="index.php">Home</a>
        </div>
	
</form>

</body>
</html>


<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bracuniversityblogger";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
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