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
    <title>Registration Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>
    <div class="auth-body">
        <div class="auth-container">
            <div class="auth-header">
                <h1>Register</h1>
                <p>Create your new account</p>
            </div>
            <?php
            if (isset($_POST["submit"])) {
            $fullName = $_POST["fullname"];
            $username = $_POST["username"];
            $iden = $_POST["iden"];
            $dateofbirth = $_POST["dob"];
            $email = $_POST["email"];
            $password = $_POST["password"];
            $passwordRepeat = $_POST["repeat_password"];
            
            
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $errors = array();
            
            if (empty($fullName) OR empty($username) OR empty($iden) OR empty($dateofbirth) OR empty($email) OR empty($password) OR empty($passwordRepeat)) {
                array_push($errors,"All fields are required");
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                array_push($errors, "Email is not valid");
            }
            if (strlen($password)<6) {
                array_push($errors,"Password must be at least 6 charactes long");
            }
            if ($password!==$passwordRepeat) {
                array_push($errors,"Password does not match");
            }
            require_once "database.php";
            $sql = "SELECT * FROM users WHERE email = '$email'";
            $result = mysqli_query($conn, $sql);
            $rowCount = mysqli_num_rows($result);
            if ($rowCount>0) {
                array_push($errors,"Email already exists!");
            }
            require_once "database.php";
            $sql = "SELECT * FROM users WHERE username = '$username'";
            $result = mysqli_query($conn, $sql);
            $rowCount = mysqli_num_rows($result);
            if ($rowCount>0) {
                array_push($errors,"User Name already exists!");
            }
            if (count($errors)>0) {
                foreach ($errors as  $error) {
                    echo "<div class='alert alert-danger'>$error</div>";
                }
            }else{
                
                $sql = "INSERT INTO users (fullname, username, iden, DateOfBirth, email, password) VALUES ( ?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_stmt_init($conn);
                $prepareStmt = mysqli_stmt_prepare($stmt,$sql);
                if ($prepareStmt) {
                    mysqli_stmt_bind_param($stmt,"ssssss",$fullName, $username, $iden, $dateofbirth, $email, $passwordHash);
                    mysqli_stmt_execute($stmt);
                    echo "<div class='alert alert-success'>You are registered successfully.</div>";
                }else{
                    die("Something went wrong");
                }
            }
            }
            ?>
            <form action="registration.php" method="post">
                <div class="form-group">
                    <input type="text" class="form-control" name="fullname" placeholder="Full Name:">
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" name="username" placeholder="User Name:">
                </div>
                <div class="form-group">
                    <p> <h5> Date Of Birth </h5> </p>
                    <input type="date" class="form-control" name="dob">
                </div>
                <div class="form-group">
                    <div>
                        <label for="Student" class="ratio-inline"><input type="radio" name="iden" value="S" id="Student">Student</label>
                        <label></label>
                        <label for="Faculty" class="ratio-inline"><input type="radio" name="iden" value="F" id="Faculty">Faculty</label>
                        <label></label>
                        <label for="Alumni" class="ratio-inline"><input type="radio" name="iden" value="A" id="Alumni">Alumni</label>
                        <label></label>
                        <label for="Outsiders" class="ratio-inline"><input type="radio" name="iden" value="O" id="Outsiders">Outsider</label>
                    </div>
                </div>
                <div class="form-group">
                    <input type="email" class="form-control" name="email" placeholder="Email:">
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" name="password" placeholder="Password:">
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" name="repeat_password" placeholder="Repeat Password:">
                </div>
                <div class="form-group">
                <label class="form-label">Profile Picture</label>
                <input type="file" 
                    class="form-control"
                    name="pp">
            </div>
            <div class="form-btn">
                    <input type="submit" class="btn btn-primary" value="Register" name="submit">
                </div>
            </form>
            <div class="text-center mt-4">
            <p>Already Registered??  <a href="login.php">Login Here</a></p>
        </div>
        </div>
    </div>
</body>
</html>