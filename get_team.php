<?php
// get_team.php
include 'admin/config.php'; // path may differ depending on your structure

header('Content-Type: application/json');

$result = mysqli_query($conn, "SELECT * FROM team ORDER BY id DESC");

$team = [];

while ($row = mysqli_fetch_assoc($result)) {
    $team[] = $row;
}

echo json_encode($team);
