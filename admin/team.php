<?php
include 'config.php';

// Handle Add or Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $designation = mysqli_real_escape_string($conn, $_POST['designation']);
    $facebook = $_POST['facebook'];
    $twitter = $_POST['twitter'];
    $instagram = $_POST['instagram'];
    $linkedin = $_POST['linkedin'];
    $id = $_POST['id'] ?? null;

    $imagePath = '';
    if (!empty($_FILES['photo']['name'])) {
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $imagePath = 'uploads/team_' . time() . '.' . $ext;
        move_uploaded_file($_FILES['photo']['tmp_name'], $imagePath);
    }

    if ($id) {
        // Update
        $query = "UPDATE team SET name='$name', designation='$designation', facebook='$facebook', twitter='$twitter', instagram='$instagram', linkedin='$linkedin'";
        if ($imagePath) $query .= ", photo='$imagePath'";
        $query .= " WHERE id=$id";
    } else {
        // Insert
        $query = "INSERT INTO team (name, designation, photo, facebook, twitter, instagram, linkedin)
                  VALUES ('$name', '$designation', '$imagePath', '$facebook', '$twitter', '$instagram', '$linkedin')";
    }
    mysqli_query($conn, $query);
    header("Location: dashboard.php?page=team");
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM team WHERE id=$id");
    header("Location: dashboard.php?page=team");
    exit;
}

// Fetch for edit
$editData = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $res = mysqli_query($conn, "SELECT * FROM team WHERE id=$id");
    $editData = mysqli_fetch_assoc($res);
}

// Fetch all
$members = mysqli_query($conn, "SELECT * FROM team ORDER BY id DESC");
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container my-5">
    <h2 class="mb-4">Manage Team Members</h2>

    <form method="post" enctype="multipart/form-data" class="row g-3 mb-4">
        <input type="hidden" name="id" value="<?= $editData['id'] ?? '' ?>">
        <div class="col-md-4">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" value="<?= $editData['name'] ?? '' ?>" class="form-control" required>
        </div>
        <div class="col-md-12">
            <label class="form-label">Designation</label>
            <textarea name="designation" class="form-control" rows="3" required><?= $editData['designation'] ?? '' ?></textarea>
        </div>

        <div class="col-md-4">
            <label class="form-label">Photo</label>
            <input type="file" name="photo" class="form-control">
            <?php if (!empty($editData['photo'])): ?>
                <img src="<?= $editData['photo'] ?>" class="mt-2 rounded" height="60">
            <?php endif; ?>
        </div>
        <div class="col-md-3">
            <input type="url" name="facebook" value="<?= $editData['facebook'] ?? '' ?>" placeholder="Facebook URL" class="form-control">
        </div>
        <div class="col-md-3">
            <input type="url" name="twitter" value="<?= $editData['twitter'] ?? '' ?>" placeholder="Twitter URL" class="form-control">
        </div>
        <div class="col-md-3">
            <input type="url" name="instagram" value="<?= $editData['instagram'] ?? '' ?>" placeholder="Instagram URL" class="form-control">
        </div>
        <div class="col-md-3">
            <input type="url" name="linkedin" value="<?= $editData['linkedin'] ?? '' ?>" placeholder="LinkedIn URL" class="form-control">
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary"><?= $editData ? 'Update' : 'Add' ?> Member</button>
        </div>
    </form>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Photo</th>
                <th>Name</th>
                <th>Designation</th>
                <th>Links</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = mysqli_fetch_assoc($members)): ?>
            <tr>
                <td><img src="<?= $row['photo'] ?>" width="60" class="rounded-circle"></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['designation']) ?></td>
                <td>
                    <?php if ($row['facebook']): ?><a href="<?= $row['facebook'] ?>" target="_blank">FB</a> <?php endif; ?>
                    <?php if ($row['twitter']): ?><a href="<?= $row['twitter'] ?>" target="_blank">TW</a> <?php endif; ?>
                    <?php if ($row['instagram']): ?><a href="<?= $row['instagram'] ?>" target="_blank">IG</a> <?php endif; ?>
                    <?php if ($row['linkedin']): ?><a href="<?= $row['linkedin'] ?>" target="_blank">IN</a> <?php endif; ?>
                </td>
                <td>
                    <a href="dashboard.php?page=team&edit=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="dashboard.php?page=team&delete=<?= $row['id'] ?>" onclick="return confirm('Are you sure?')" class="btn btn-sm btn-danger">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
