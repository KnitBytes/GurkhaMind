<?php
header('Content-Type: application/json');
include 'admin/config.php'; // Make sure path is correct

// Run your SQL query
$sql = "SELECT site_name, hero_heading, hero_paragraph, hero_img FROM homepage_content LIMIT 1";
$result = mysqli_query($conn, $sql);

// Check if query worked
if (!$result) {
    // Query failed, send error info
    echo json_encode(['error' => 'Query failed: ' . mysqli_error($conn)]);
    exit;
}

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    echo json_encode($row);
} else {
    echo json_encode(['error' => 'No data found']);
}
?>
