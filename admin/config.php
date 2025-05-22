<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


$host = "localhost";
$user = "root";
$pass = "";
$dbname = "gurkha"; 

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
