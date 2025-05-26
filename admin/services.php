<?php
include 'config.php';

// Handle Add or Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $icon_class = mysqli_real_escape_string($conn, $_POST['icon_class']);
    $short_description = mysqli_real_escape_string($conn, $_POST['short_description']);
    $back_title = mysqli_real_escape_string($conn, $_POST['back_title']);
    $back_case = mysqli_real_escape_string($conn, $_POST['back_case']);
    $back_points = json_encode(array_filter($_POST['back_points'] ?? []));
    $id = $_POST['id'] ?? null;

    if ($id) {
        $query = "UPDATE services SET icon_class='$icon_class', title='$title', short_description='$short_description',
                  back_title='$back_title', back_points='$back_points', back_case='$back_case' WHERE id=$id";
    } else {
        $query = "INSERT INTO services (icon_class, title, short_description, back_title, back_points, back_case)
                  VALUES ('$icon_class', '$title', '$short_description', '$back_title', '$back_points', '$back_case')";
    }
    mysqli_query($conn, $query);
    header("Location: dashboard.php?page=services");
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM services WHERE id=$id");
    header("Location: dashboard.php?page=services");
    exit;
}

// Fetch for edit
$editData = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $res = mysqli_query($conn, "SELECT * FROM services WHERE id=$id");
    $editData = mysqli_fetch_assoc($res);
    $editData['back_points'] = json_decode($editData['back_points'], true);
}

// Fetch all
$services = mysqli_query($conn, "SELECT * FROM services ORDER BY id DESC");
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container my-5">
    <h2 class="mb-4">Manage Services</h2>

    <form method="post" class="row g-3 mb-4">
        <input type="hidden" name="id" value="<?= $editData['id'] ?? '' ?>">
       <div class="col-md-4">
    <label class="form-label">Icon Class</label>
    <input type="text" name="icon_class" value="<?= $editData['icon_class'] ?? '' ?>" class="form-control" required>
    <small class="form-text text-muted">
        Example: <code>fas fa-code</code> or <code>fa-solid fa-robot</code>
    </small>
</div>

        <div class="col-md-4">
            <label class="form-label">Title</label>
            <input type="text" name="title" value="<?= $editData['title'] ?? '' ?>" class="form-control" required>
        </div>
        <div class="col-md-12">
            <label class="form-label">Short Description</label>
            <textarea name="short_description" class="form-control" rows="2" required><?= $editData['short_description'] ?? '' ?></textarea>
        </div>
        <div class="col-md-12">
            <label class="form-label">Back Title</label>
            <input type="text" name="back_title" value="<?= $editData['back_title'] ?? '' ?>" class="form-control" required>
        </div>
        <div class="col-md-12">
            <label class="form-label">Back Points</label>
            <?php for ($i = 0; $i < 5; $i++): ?>
                <input type="text" name="back_points[]" class="form-control mb-1" placeholder="Point <?= $i + 1 ?>"
                    value="<?= $editData['back_points'][$i] ?? '' ?>">
            <?php endfor; ?>
        </div>
        <div class="col-md-12">
            <label class="form-label">Back Case</label>
            <textarea name="back_case" class="form-control" rows="2"><?= $editData['back_case'] ?? '' ?></textarea>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary"><?= $editData ? 'Update' : 'Add' ?> Service</button>
        </div>
    </form>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Icon</th>
                <th>Title</th>
                <th>Short Description</th>
                <th>Back Title & Points</th>
                <th>Back Case</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = mysqli_fetch_assoc($services)): ?>
            <?php $points = json_decode($row['back_points'], true); ?>
            <tr>
                <td><i class="<?= htmlspecialchars($row['icon_class']) ?>"></i></td>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= htmlspecialchars($row['short_description']) ?></td>
                <td>
                    <strong><?= htmlspecialchars($row['back_title']) ?></strong>
                    <ul class="mb-0">
                        <?php foreach ($points as $point): ?>
                            <li><?= htmlspecialchars($point) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </td>
                <td><?= htmlspecialchars($row['back_case']) ?></td>
                <td>
                    <a href="dashboard.php?page=services&edit=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="dashboard.php?page=services&delete=<?= $row['id'] ?>" onclick="return confirm('Are you sure?')" class="btn btn-sm btn-danger">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
