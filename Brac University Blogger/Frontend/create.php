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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <style>
        header {
            color: #333;
            padding: 1em;
            text-align: center;
        }


    </style>
</head>

<body>
<header>
    <h1>Create Post</h1>
    </header>
    <div class="container">
        <?php
  
        if (isset($_POST["publish"])) {
            $title = $_POST["title"];
            $description = $_POST["description"];
            $category = $_POST["category"];
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
                echo "<div class='alert alert-success'>You have added successfully.</div>";
            }else{
                die("Something went wrong");
            }
        }
    
        ?>

      <form action="create.php" method="post">
        <div class="form-group">
            <input type="text" placeholder="title:" name="title" class="form-control">
        </div>
        <div class="form-group">
            <input type="text" placeholder="Description:" name="description" class="form-control">
        </div>
        <div class="form-group">
            <label for="category"> Category </label>
            <select name="category">
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
            <input type="submit" value="Create Post" name="publish" class="follow-btn">
            <a href="index.php" class="follow-btn">Home</a>
        </div>
      </form>
    </div>
</body>
</html>