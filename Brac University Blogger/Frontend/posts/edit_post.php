<?php
session_start();
require_once "../includes/database.php";


if (!isset($_SESSION["user"])) {
    header("Location: ../auth/login.php");

    exit();
}

$postId = $_GET['id'] ?? null;
$errorMsg = "";
$successMsg = "";

if (!$postId) {
    header("Location: ../index.php");

    exit();
}

// Fetch Post Data and Verify Ownership
$currentUser = $_SESSION['username'];
$userQuery = "SELECT id FROM users WHERE username = '$currentUser'";
$userResult = $conn->query($userQuery);
$userRow = $userResult->fetch_assoc();
$userId = $userRow['id'];

$sql = "SELECT * FROM post WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $postId, $userId);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();

if (!$post) {
    die("Post not found or unauthorized.");
}

// Handle Form Submission
if (isset($_POST["update"])) {
    $title = $_POST["title"] ?? "";
    $description = $_POST["description"] ?? "";
    $category = $_POST["category"] ?? "";

    if (!empty($title) && !empty($description) && !empty($category)) {
        $updateSql = "UPDATE post SET title = ?, description = ?, category = ? WHERE id = ? AND user_id = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("sssii", $title, $description, $category, $postId, $userId);
        
        if ($updateStmt->execute()) {
            $successMsg = "Post updated successfully!";
            // Refresh post data
            $post['title'] = $title;
            $post['description'] = $description;
            $post['category'] = $category;
        } else {
            $errorMsg = "Something went wrong.";
        }
    } else {
        $errorMsg = "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/main.css">

</head>

<body>
<div class="auth-body">
    <div class="auth-container">
        <div class="auth-header">
            <h1>Edit Post</h1>
        </div>
        
        <?php if ($successMsg): ?>
            <div class='alert alert-success'><?php echo $successMsg; ?> <a href='../index.php'>Go Home</a></div>

        <?php endif; ?>
        
        <?php if ($errorMsg): ?>
            <div class='alert alert-danger'><?php echo $errorMsg; ?></div>
        <?php endif; ?>

      <form method="post">
        <div class="form-group">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($post['title']); ?>" required>
        </div>
        <div class="form-group">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="5" required style="resize:vertical;"><?php echo htmlspecialchars($post['description']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="category" class="form-label">Category</label>
            <select name="category" class="form-control">
                <?php
                $categories = ["Food", "Travel", "Entertainment", "Sports", "Education", "Others"];
                foreach ($categories as $cat) {
                    $selected = ($post['category'] == $cat) ? "selected" : "";
                    echo "<option value='$cat' $selected>$cat</option>";
                }
                ?>
            </select>
        </div>
        
        <div class="form-btn">
            <input type="submit" value="Update Post" name="update" class="btn-primary">
        </div>
        <div class="text-center mt-4">
            <a href="../index.php" style="color:var(--primary-color);">Cancel</a>

        </div>
      </form>
    </div>
</div>
</body>
</html>
