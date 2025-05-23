<?php
header('Content-Type: application/json');
include 'admin/config.php'; // Adjust path as needed

// Run your SQL query
$sql = "SELECT site_name, hero_heading, hero_paragraph, hero_img FROM homepage_content LIMIT 1";
$result = mysqli_query($conn, $sql);

// Check if query worked
if (!$result) {
    echo json_encode(['error' => 'Query failed: ' . mysqli_error($conn)]);
    exit;
}

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);

    // Decode hero_img JSON string into PHP array
    $row['hero_img'] = json_decode($row['hero_img'], true);

    // If decoding fails, fallback to empty array
    if (!is_array($row['hero_img'])) {
        $row['hero_img'] = [];
    }

    echo json_encode($row);
} else {
    echo json_encode(['error' => 'No data found']);
}
