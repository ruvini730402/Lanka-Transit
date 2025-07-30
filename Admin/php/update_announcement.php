<?php
include('../dbcon.php');
include('Announcement.php');

$announcement = new Announcement($connection);

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $data = $announcement->getById($_GET['id']);
} elseif (isset($_POST['update_announcement'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $content = $_POST['content'];

    if ($announcement->update($id, $title, $content)) {
    header("Location: ../announcement_display.php?msg=Announcement updated successfully");
} else {
    header("Location: ../announcement_display.php?msg=Failed to update announcement");
}

}
?>

<!-- Update Form -->
<!DOCTYPE html>
<html>
<head>
    <title>Update Announcement</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h3>Update Announcement</h3>
    <form method="POST">
        <input type="hidden" name="id" value="<?= htmlspecialchars($data['id']) ?>">
        <div class="mb-3">
            <label>Title</label>
            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($data['title']) ?>" required>
        </div>
        <div class="mb-3">
            <label>Content</label>
            <textarea name="content" class="form-control" rows="4" required><?= htmlspecialchars($data['content']) ?></textarea>
        </div>
        <button type="submit" name="update_announcement" class="btn" style="background-color: maroon; color: white;">Update</button>

    </form>
</body>
</html>
