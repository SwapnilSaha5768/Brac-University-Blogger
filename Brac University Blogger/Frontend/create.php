<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Post</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="assets/css/main.css">
</head>

<body>
<div class="dashboard-container">
    <?php include 'sidebar.php'; ?>
    
    <div class="main-content">
        <div class="options-bar">
            <a href="explore.php">Explore</a>
            <a href="#">Interest</a>
        </div>

        <div class="auth-container" style="max-width: 800px; margin: 30px auto;">
            <div class="auth-header">
                <h1>Create Post</h1>
            </div>
            <?php
            if (isset($_POST["publish"])) {
                $title = $_POST["title"] ?? "";
                $description = $_POST["description"] ?? "";
                $category = $_POST["category"] ?? "";
                $errors = array();
                require_once "database.php";

                $follower = $_SESSION["username"];
                $checkUserQue = "SELECT id FROM users WHERE username = '$follower'";
                $results = $conn->query($checkUserQue);
                $userID = $results->fetch_assoc()['id'];

                $sql = "INSERT INTO post (user_id, title, description, category) VALUES (?, ?, ?, ?)";
                $stmt = mysqli_stmt_init($conn);
                $prepareStmt = mysqli_stmt_prepare($stmt,$sql);
                if ($prepareStmt) {
                    mysqli_stmt_bind_param($stmt,"isss", $userID, $title, $description, $category);
                    mysqli_stmt_execute($stmt);
                    echo "<div class='alert alert-success'>You have added successfully. <a href='index.php'>View Post</a></div>";
                }else{
                    die("Something went wrong");
                }
            }
            ?>

          <form action="create.php" method="post">
            <div class="form-group">
                <input type="text" placeholder="Title:" name="title" class="form-control" required>
            </div>
            <div class="form-group">
                <textarea placeholder="Description:" name="description" class="form-control" rows="10" required style="resize:vertical;"></textarea>
            </div>
            <div class="form-group">
                <label for="category" class="form-label" style="display:block; margin-bottom:5px;">Category</label>
                <select name="category" class="form-control">
                    <option>Select</option>
                    <option value="Food">Food</option>
                    <option value="Travel">Travel</option>
                    <option value="Entertainment">Entertainment</option>
                    <option value="Sports">Sports</option>
                    <option value="Education">Education</option>
                    <option value="Others">Others</option>
                </select>
            </div>
            
            <div class="form-btn">
                <input type="submit" value="Create Post" name="publish" class="btn-primary">
            </div>
            <div class="text-center mt-4">
                <a href="index.php" style="color:var(--primary-color);">Cancel</a>
            </div>
          </form>
        </div>
    </div>
</div>
</body>
</html>