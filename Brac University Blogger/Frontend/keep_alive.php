<?php
// keep_alive.php
require_once 'includes/database.php';

// Disable caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

$start = microtime(true);

try {
    if (isset($conn) && $conn instanceof mysqli) {
        // Perform a lightweight query
        $result = $conn->query("SELECT 1");
        if ($result) {
            $duration = round((microtime(true) - $start) * 1000, 2);
            echo "Status: Database is Active. (Response in {$duration}ms)";
        } else {
            http_response_code(500);
            echo "Status: Query Failed. " . $conn->error;
        }
    } else {
        http_response_code(500);
        echo "Status: Database Connection Failed.";
    }
} catch (Exception $e) {
    http_response_code(500);
    echo "Status: Error - " . $e->getMessage();
}
?>
