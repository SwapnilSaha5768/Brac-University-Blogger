<?php
session_start();
if (isset($_SESSION["user"])) {
   header("Location: index.php");
   die();
}

$errorMsg = "";
if (isset($_POST["login"])) {
    $input_login = $_POST["username"] ?? ""; // Accepts username or email
    $password = $_POST["password"] ?? "";
    
    require_once "database.php";
    // Sanitize input
    $input_login = mysqli_real_escape_string($conn, $input_login);
    
    $sql = "SELECT * FROM users WHERE username = '$input_login' OR email = '$input_login'";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_array($result, MYSQLI_ASSOC);
    
    if ($user) {
        if (password_verify($password, $user["password"])) {
             // Session is already started at the top
            $_SESSION['username'] = $user['username']; // Set from DB
            $_SESSION['fullname'] = $user['fullname']; // Set from DB
            $_SESSION["user"] = "yes";
            header("Location: index.php");
            die();
        } else {
            $errorMsg = "<div class='alert alert-danger'>Password does not match</div>";
        }
    } else {
        $errorMsg = "<div class='alert alert-danger'>User not found</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/main.css">
</head>

<body>
<div class="auth-body">
    <div style="width: 100%; max-width: 500px;">
        <div class="auth-header">
            <h1>Brac University Blogger</h1>
            <p>Welcome!</p>
        </div>
        <div class="auth-container">
            <?php
            if (!empty($errorMsg)) {
                echo $errorMsg;
            }
            ?>
        <form action="login.php" method="post">
            <div class="form-group">
                <input type="text" placeholder="Enter Username or Email:" name="username" class="form-control" required>
            </div>
            <div class="form-group">
                <input type="password" placeholder="Enter Password:" name="password" class="form-control" required>
            </div>
            <div class="form-btn">
                <input type="submit" value="Login" name="login" class="btn btn-primary">
            </div>
            <div class="text-center mt-4"><p>Not registered yet??  <a href="registration.php">Register Here</a></p></div>
        </form>
        </div>
    </div>
</div>
</body>
</html>