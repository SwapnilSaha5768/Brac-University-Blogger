<?php
require_once "database.php";

$sql = "
CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES post(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
";

echo "<h1>Updating Database...</h1>";

if ($conn->query($sql) === TRUE) {
    echo "<h2 style='color:green'>Table 'comments' created successfully!</h2>";
    echo "<p>You can now <a href='index.php'>Go to Home</a></p>";
} else {
    echo "<h2 style='color:red'>Error creating table: " . $conn->error . "</h2>";
}

$conn->close();
?>
