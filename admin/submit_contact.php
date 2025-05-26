<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'gurkha'; // ⚠️ Replace with your DB name

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get POST data
$name = $_POST['name'];
$organization = $_POST['organization'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$project_interest = $_POST['project_interest'];
$message = $_POST['message'];
$contact_method = $_POST['contact_method'];
$consent = isset($_POST['consent']) ? 1 : 0;

// SQL insert
$sql = "INSERT INTO contact_messages 
(name, organization, email, phone, project_interest, message, contact_method, consent, submitted_at)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("sssssssi", $name, $organization, $email, $phone, $project_interest, $message, $contact_method, $consent);

if ($stmt->execute()) {
    echo "Message submitted successfully!";
    // Optionally redirect to thank-you page
    header("Location: ../index.html?success=1");
} else {
    echo "Error submitting: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
