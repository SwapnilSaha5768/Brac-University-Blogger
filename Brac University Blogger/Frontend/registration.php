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
            $fullName = $_POST["fullname"] ?? "";
            $username = $_POST["username"] ?? "";
            $iden = $_POST["iden"] ?? "";
            $dateofbirth = $_POST["dob"] ?? "";
            $email = $_POST["email"] ?? "";
            $password = $_POST["password"] ?? "";
            $passwordRepeat = $_POST["repeat_password"] ?? "";
            
            
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
            <form action="registration.php" method="post" enctype="multipart/form-data" id="regForm">
                
                <!-- Step 1: Personal Details -->
                <div id="step-1">
                    <h5 class="mb-6">Personal Details</h5>
                    <div class="form-group">
                        <input type="text" class="form-control" name="fullname" id="fullname" placeholder="Full Name:" required>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="username" id="username" placeholder="User Name:" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="display:block; margin-bottom:5px;">Date Of Birth</label>
                        <input type="date" class="form-control" name="dob" id="dob" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Identity</label>
                        <div class="identity-grid">
                            <label class="identity-option">
                                <input type="radio" name="iden" value="S" required>
                                <div class="identity-card">Student</div>
                            </label>
                            <label class="identity-option">
                                <input type="radio" name="iden" value="F">
                                <div class="identity-card">Faculty</div>
                            </label>
                            <label class="identity-option">
                                <input type="radio" name="iden" value="A">
                                <div class="identity-card">Alumni</div>
                            </label>
                            <label class="identity-option">
                                <input type="radio" name="iden" value="O">
                                <div class="identity-card">Outsider</div>
                            </label>
                        </div>
                    </div>
                    <div class="form-btn text-end">
                        <button type="button" class="btn btn-primary" onclick="nextStep()">Next <i class='bx bx-right-arrow-alt'></i></button>
                    </div>
                </div>

                <!-- Step 2: Account Details -->
                <div id="step-2" style="display:none;">
                    <h5 class="mb-6">Account Details</h5>
                    <div class="form-group">
                        <input type="email" class="form-control" name="email" id="email" placeholder="Email:" required>
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" name="password" id="password" placeholder="Password:" required>
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" name="repeat_password" id="repeat_password" placeholder="Repeat Password:" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Profile Picture</label>
                        <input type="file" class="form-control" name="pp">
                    </div>
                    <div class="form-btn d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" onclick="prevStep()"><i class='bx bx-left-arrow-alt'></i> Back</button>
                        <button type="submit" class="btn btn-primary" value="Register" name="submit">Register</button>
                    </div>
                </div>

            </form>

            <script>
                function nextStep() {
                    // Simple Validation for Step 1
                    var fullname = document.getElementById('fullname').value;
                    var username = document.getElementById('username').value;
                    var dob = document.getElementById('dob').value;
                    var iden = document.querySelector('input[name="iden"]:checked');

                    if(fullname === "" || username === "" || dob === "" || !iden) {
                        alert("Please fill in all fields in this step.");
                        return;
                    }

                    document.getElementById('step-1').style.display = 'none';
                    document.getElementById('step-2').style.display = 'block';
                }

                function prevStep() {
                    document.getElementById('step-2').style.display = 'none';
                    document.getElementById('step-1').style.display = 'block';
                }
            </script>
            <div class="text-center mt-4">
            <p>Already Registered??  <a href="login.php">Login Here</a></p>
        </div>
        </div>
    </div>
</body>
</html>