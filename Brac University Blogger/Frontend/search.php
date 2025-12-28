<?php
session_start();
if (isset($_POST["submit"])) {
    require_once "database.php";
	$search_username = $_POST["search"] ?? "";

    $search_username = mysqli_real_escape_string($conn, $search_username);

    $sql = "SELECT * FROM users WHERE username = '$search_username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user_profile = $result->fetch_assoc();

        $_SESSION['susername'] = $search_username;
        $_SESSION["user1"] = "yes";
        header("Location: friend-profile.php?user_id=" . $user_profile['id']);
        die();
    } else {
        $errorMsg = "User not found";
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search User</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>

<div class="dashboard-container">
    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>            
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="options-bar">
            <a href="explore.php">Explore</a>
            <a href="#">Interest</a>
        </div>

        <div class="auth-container" style="max-width: 600px; margin: 50px auto;">
            <div class="auth-header">
                <h1>Search User</h1>
            </div>
            <form method="post">
                <?php
                if (isset($errorMsg)) {
                    echo "<div class='alert alert-danger'>$errorMsg</div>";
                }
                ?>
                <div class="form-group">
                    <label class="form-label" style="display:block; margin-bottom:5px;">Username</label>
                    <input type="text" placeholder="Enter Username:" name="search" class="form-control" required>
                </div>
                <div class="form-group">
                    <input type="submit" value="Search" name="submit" class="btn-primary">
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>