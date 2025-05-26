<?php
// get_services.php
include 'admin/config.php'; // adjust path if needed

header('Content-Type: application/json');

$result = mysqli_query($conn, "SELECT * FROM services ORDER BY id DESC");

$services = [];

while ($row = mysqli_fetch_assoc($result)) {
    // decode back_points before returning if you want it as array instead of json string
    $row['back_points'] = json_decode($row['back_points'], true);
    $services[] = $row;
}

echo json_encode($services);
