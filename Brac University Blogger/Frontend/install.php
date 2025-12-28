<?php
// This script runs the setup SQL to create tables in the connected database.
// Usage: Deploy this file, then visit https://your-app.onrender.com/install.php

require_once "database.php";

$sql = "
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(255) NOT NULL,
    username VARCHAR(255) NOT NULL UNIQUE,
    iden VARCHAR(50) NOT NULL,
    DateOfBirth DATE NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS post (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    category VARCHAR(100) NOT NULL,
    timestamp_column TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS reactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    blog_id INT NOT NULL,
    user_id INT NOT NULL,
    reaction_type VARCHAR(20) NOT NULL, -- 'like' or 'dislike'
    FOREIGN KEY (blog_id) REFERENCES post(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS follows (
    follower_id INT NOT NULL,
    following_id INT NOT NULL,
    PRIMARY KEY (follower_id, following_id),
    FOREIGN KEY (follower_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (following_id) REFERENCES users(id) ON DELETE CASCADE
);
";

echo "<h1>Database Installation</h1>";
echo "<p>Connecting to database...</p>";

if ($conn->connect_error) {
    die("<p style='color:red'>Connection failed: " . $conn->connect_error . "</p>");
} else {
    echo "<p style='color:green'>Connected successfully.</p>";
}

echo "<p>Running table creation...</p>";

// Execute multi_query
if ($conn->multi_query($sql)) {
    do {
        /* store first result set */
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->next_result());
    
    echo "<h2 style='color:green'>Tables created successfully!</h2>";
    echo "<p>You can now <a href='index.php'>Go to Home</a></p>";
    echo "<p><em>Note: For security, you should delete this file from your repository after use.</em></p>";
} else {
    echo "<h2 style='color:red'>Error creating tables: " . $conn->error . "</h2>";
}

$conn->close();
?>
