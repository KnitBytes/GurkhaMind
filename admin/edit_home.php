<?php
include 'config.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $site_name = mysqli_real_escape_string($conn, $_POST['site_name']);
    $hero_heading = mysqli_real_escape_string($conn, $_POST['hero_heading']);
    $hero_paragraph = mysqli_real_escape_string($conn, $_POST['hero_paragraph']);

    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $hero_imgs = [];

    if (isset($_FILES['hero_img']) && is_array($_FILES['hero_img']['tmp_name'])) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        foreach ($_FILES['hero_img']['tmp_name'] as $key => $tmp_name) {
            $error = $_FILES['hero_img']['error'][$key];
            $type = $_FILES['hero_img']['type'][$key];
            $name = $_FILES['hero_img']['name'][$key];

            if ($error === 0 && in_array($type, $allowed_types)) {
                $file_ext = pathinfo($name, PATHINFO_EXTENSION);
                $new_filename = 'hero_img_' . time() . '_' . $key . '.' . $file_ext;
                $target_file = $upload_dir . $new_filename;

                if (move_uploaded_file($tmp_name, $target_file)) {
                    $hero_imgs[] = $target_file;
                } else {
                    $message = "Error uploading file: $name";
                }
            }
        }
    }

    // If no new images uploaded, keep existing
    if (empty($hero_imgs)) {
        $result = mysqli_query($conn, "SELECT hero_img FROM homepage_content LIMIT 1");
        $row = mysqli_fetch_assoc($result);
        $hero_imgs = $row['hero_img'] ? json_decode($row['hero_img'], true) : [];
    }

    $hero_img_json = json_encode($hero_imgs);

    $check_sql = "SELECT id FROM homepage_content LIMIT 1";
    $check_result = mysqli_query($conn, $check_sql);

    if (mysqli_num_rows($check_result) > 0) {
        $update_sql = "UPDATE homepage_content SET
            site_name = '$site_name',
            hero_heading = '$hero_heading',
            hero_paragraph = '$hero_paragraph',
            hero_img = '" . mysqli_real_escape_string($conn, $hero_img_json) . "'
            WHERE id = (SELECT id FROM homepage_content LIMIT 1)";
        if (mysqli_query($conn, $update_sql)) {
            header("Location: dashboard.php?page=edit_home");
            exit;
        } else {
            $message = "Update error: " . mysqli_error($conn);
        }
    } else {
        $insert_sql = "INSERT INTO homepage_content (site_name, hero_heading, hero_paragraph, hero_img)
                       VALUES (
                           '$site_name',
                           '$hero_heading',
                           '$hero_paragraph',
                           '" . mysqli_real_escape_string($conn, $hero_img_json) . "')";
        if (mysqli_query($conn, $insert_sql)) {
            header("Location: dashboard.php?page=edit_home");
            exit;
        } else {
            $message = "Insert error: " . mysqli_error($conn);
        }
    }
}

// Fetch current content
$result = mysqli_query($conn, "SELECT * FROM homepage_content LIMIT 1");
$content = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Homepage Content</title>
    <style>
        label { font-weight: bold; }
        input[type=text], textarea { width: 100%; padding: 8px; margin-bottom: 15px; }
        textarea { resize: vertical; }
        button { padding: 10px 20px; background-color: #0d6efd; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background-color: #084cd9; }
        .message { margin-bottom: 15px; font-weight: bold; color: green; }
        .container { max-width: 700px; margin: 30px auto; font-family: Arial, sans-serif; }
        img { max-width: 150px; margin: 10px 10px 10px 0; border-radius: 8px; }
        .image-preview { display: flex; flex-wrap: wrap; }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit System Information</h2>

    <?php if ($message): ?>
        <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="post" action="edit_home.php" enctype="multipart/form-data">
        <label for="site_name">Site Name:</label>
        <input type="text" id="site_name" name="site_name" value="<?php echo htmlspecialchars($content['site_name'] ?? ''); ?>" required>

        <label for="hero_heading">Hero Heading:</label>
        <textarea id="hero_heading" name="hero_heading" rows="3" required><?php echo htmlspecialchars($content['hero_heading'] ?? ''); ?></textarea>

        <label for="hero_paragraph">Hero Paragraph:</label>
        <textarea id="hero_paragraph" name="hero_paragraph" rows="4" required><?php echo htmlspecialchars($content['hero_paragraph'] ?? ''); ?></textarea>

        <label for="hero_img">Hero Images (You can select multiple):</label>
        <input type="file" id="hero_img" name="hero_img[]" accept="image/*" multiple>

        <div class="image-preview">
            <?php
            if (!empty($content['hero_img'])):
                $images = json_decode($content['hero_img'], true);
                if ($images && is_array($images)):
                    foreach ($images as $img): ?>
                        <img src="<?php echo htmlspecialchars($img); ?>" alt="Hero Image">
                    <?php endforeach;
                endif;
            endif;
            ?>
        </div>

        <button type="submit">Save Homepage Content</button>
    </form>
</div>

</body>
</html>
