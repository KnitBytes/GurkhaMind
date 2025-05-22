<?php
include 'config.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $site_name = mysqli_real_escape_string($conn, $_POST['site_name']);
    $hero_heading = mysqli_real_escape_string($conn, $_POST['hero_heading']);
    $hero_paragraph = mysqli_real_escape_string($conn, $_POST['hero_paragraph']);

    // Handle image upload
    $upload_dir = 'uploads/';  // Make sure this directory exists and is writable
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $hero_img = null; // filename to save in DB

    if (isset($_FILES['hero_img']) && $_FILES['hero_img']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (in_array($_FILES['hero_img']['type'], $allowed_types)) {
            $file_ext = pathinfo($_FILES['hero_img']['name'], PATHINFO_EXTENSION);
            $new_filename = 'hero_img_' . time() . '.' . $file_ext;
            $target_file = $upload_dir . $new_filename;

            if (move_uploaded_file($_FILES['hero_img']['tmp_name'], $target_file)) {
                $hero_img = $target_file;
            } else {
                $message = "Error uploading image file.";
            }
        } else {
            $message = "Invalid image type. Allowed types: JPEG, PNG, GIF, WEBP.";
        }
    }

    // If no new image uploaded, keep existing image
    if (!$hero_img) {
        $result = mysqli_query($conn, "SELECT hero_img FROM homepage_content LIMIT 1");
        $row = mysqli_fetch_assoc($result);
        $hero_img = $row['hero_img'] ?? '';
    }

    if (!$message) { // no error yet
        $check_sql = "SELECT id FROM homepage_content LIMIT 1";
        $check_result = mysqli_query($conn, $check_sql);

        if (mysqli_num_rows($check_result) > 0) {
            $update_sql = "UPDATE homepage_content SET
                site_name='$site_name',
                hero_heading='$hero_heading',
                hero_paragraph='$hero_paragraph',
                hero_img='$hero_img'
                WHERE id = (SELECT id FROM homepage_content LIMIT 1)";

            if (mysqli_query($conn, $update_sql)) {
            header("Location: dashboard.php?page=edit_home");
             exit;
} else {
    $message = "Error updating homepage content: " . mysqli_error($conn);
}

        } else {
            $insert_sql = "INSERT INTO homepage_content (site_name, hero_heading, hero_paragraph, hero_img)
                           VALUES ('$site_name', '$hero_heading', '$hero_paragraph', '$hero_img')";
                  if (mysqli_query($conn, $insert_sql)) {
            header("Location: dashboard.php?page=edit_home");
            exit;
} else {
    $message = "Error adding homepage content: " . mysqli_error($conn);
}

        }
    }
}

$result = mysqli_query($conn, "SELECT * FROM homepage_content LIMIT 1");
$content = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Edit Homepage Content</title>
<style>
  label { font-weight: bold; }
  input[type=text], textarea { width: 100%; padding: 8px; margin-bottom: 15px; }
  textarea { resize: vertical; }
  button { padding: 10px 20px; background-color: #0d6efd; color: white; border: none; border-radius: 5px; cursor: pointer; }
  button:hover { background-color: #084cd9; }
  .message { margin-bottom: 15px; font-weight: bold; color: green; }
  .container { max-width: 700px; margin: 30px auto; font-family: Arial, sans-serif; }
  img { max-width: 200px; margin-top: 10px; border-radius: 8px; }
</style>
</head>
<body>

<div class="container">
  <h2>Edit System Information</h2>

  <?php if ($message): ?>
    <div class="message"><?php echo $message; ?></div>
  <?php endif; ?>

  <form method="post" action="edit_home.php" enctype="multipart/form-data">
    <label for="site_name">Site Name:</label>
    <input type="text" id="site_name" name="site_name" value="<?php echo htmlspecialchars($content['site_name'] ?? ''); ?>" required>

    <label for="hero_heading">Hero Heading:</label>
    <textarea id="hero_heading" name="hero_heading" rows="3" required><?php echo htmlspecialchars($content['hero_heading'] ?? ''); ?></textarea>

    <label for="hero_paragraph">Hero Paragraph:</label>
    <textarea id="hero_paragraph" name="hero_paragraph" rows="4" required><?php echo htmlspecialchars($content['hero_paragraph'] ?? ''); ?></textarea>

    <label for="hero_img">Hero Image:</label>
    <input type="file" id="hero_img" name="hero_img" accept="image/*">

    <?php if (!empty($content['hero_img'])): ?>
      <img src="<?php echo htmlspecialchars($content['hero_img']); ?>" alt="Current Hero Image">
    <?php endif; ?>

    <button type="submit">Save Homepage Content</button>
  </form>
</div>

</body>
</html>
