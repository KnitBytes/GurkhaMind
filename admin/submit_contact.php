<?php
include 'config.php';

// Sanitize and validate inputs
$name = htmlspecialchars(trim($_POST['name']), ENT_QUOTES, 'UTF-8');
$organization = htmlspecialchars(trim($_POST['organization']), ENT_QUOTES, 'UTF-8');
$email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
$phone = htmlspecialchars(trim($_POST['phone']), ENT_QUOTES, 'UTF-8');
$project_interest = htmlspecialchars(trim($_POST['project_interest']), ENT_QUOTES, 'UTF-8');
$message = htmlspecialchars(trim($_POST['message']), ENT_QUOTES, 'UTF-8');
$contact_method = htmlspecialchars(trim($_POST['contact_method']), ENT_QUOTES, 'UTF-8');
$consent = isset($_POST['consent']) ? 1 : 0;

// Validate required fields
if (empty($name) || empty($organization) || empty($email) || empty($project_interest) || empty($message) || empty($contact_method)) {
    echo "Please fill in all required fields.";
    exit();
}

// SQL insert with prepared statement
$sql = "INSERT INTO contact_messages 
(name, organization, email, phone, project_interest, message, contact_method, consent, submitted_at)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("sssssssi", $name, $organization, $email, $phone, $project_interest, $message, $contact_method, $consent);

if ($stmt->execute()) {
    // Redirect after success
    header("Location: ../index.html?success=1");
    exit();
} else {
    echo "Error submitting: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
