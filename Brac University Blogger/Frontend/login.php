<?php
session_start();
if (isset($_SESSION["user"])) {
   header("Location: index.php");
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
            if (isset($_POST["login"])) {
            $username = $_POST["username"];
            $fullname = $_POST["fullname"];
            $password = $_POST["password"];
                require_once "database.php";
                $sql = "SELECT * FROM users WHERE username = '$username' && fullname = '$fullname'";
                $result = mysqli_query($conn, $sql);
                $total = mysqli_num_rows($result);
                $user = mysqli_fetch_array($result, MYSQLI_ASSOC);
                if ($user) {
                    if (password_verify($password, $user["password"])) {
                        session_start();
                        $_SESSION['username'] = $username;
                        $_SESSION['fullname'] = $fullname;
                        $_SESSION["user"] = "yes";
                        header("Location: index.php");
                        die();
                    }else{
                        echo "<div class='alert alert-danger'>Password does not match</div>";
                    }
                }else{
                    echo "<div class='alert alert-danger'>User Name and FullName does not match</div>";
                }
            }
            ?>
        <form action="login.php" method="post">
            <div class="form-group">
                <input type="text" placeholder="Enter User Name:" name="username" class="form-control">
            </div>
            <div class="form-group">
                <input type="text" placeholder="Enter Full Name:" name="fullname" class="form-control">
            </div>
            <div class="form-group">
                <input type="password" placeholder="Enter Password:" name="password" class="form-control">
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