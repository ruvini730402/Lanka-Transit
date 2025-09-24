<?php
require_once __DIR__ . '/../../classes/Database.php';
include('announcement.php');

// Initialize variables
$announcement_data = null;
$error_message = '';

// Get announcement data for editing
if (isset($_GET['id'])) {
    try {
        $database = new Database();
        $connection = $database->getConnection();
        $announcementObj = new Announcement($connection);
        $announcement_data = $announcementObj->getById($_GET['id']);
        
        if (!$announcement_data) {
            header("Location: ../announcement_display.php?msg=Announcement not found");
            exit();
        }
    } catch (PDOException $e) {
        $error_message = "Error fetching announcement: " . $e->getMessage();
    }
}

// Handle form submission
if (isset($_POST['update_announcement']) && isset($_POST['id'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $content = $_POST['content'];

    try {
        $database = new Database();
        $connection = $database->getConnection();
        $announcementObj = new Announcement($connection);
        
        if ($announcementObj->update($id, $title, $content)) {
            header("Location: ../announcement_display.php?msg=Announcement updated successfully");
            exit();
        } else {
            $error_message = "Failed to update announcement";
        }
    } catch (PDOException $e) {
        $error_message = "Error updating announcement: " . $e->getMessage();
    }
}

// If no announcement data and no error, redirect back
if (!$announcement_data && !$error_message) {
    header("Location: ../announcement_display.php?msg=Invalid announcement ID");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Announcement</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="container mt-4">
    <!-- Back Button -->
    <a href="../announcement_display.php" class="btn btn-maroon-outline back-btn">&larr; Back to Announcements</a>
    
    <h2 class="mb-4">Update Announcement</h2>

    <!-- Error Message -->
    <?php if ($error_message): ?>
        <div class="alert alert-danger" role="alert">
            <?= htmlspecialchars($error_message) ?>
        </div>
    <?php endif; ?>

    <?php if ($announcement_data): ?>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <form method="POST" action="">
                <input type="hidden" name="id" value="<?= htmlspecialchars($announcement_data['ID']) ?>">

                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" 
                           id="title" 
                           name="title" 
                           class="form-control" 
                           value="<?= htmlspecialchars($announcement_data['title']) ?>" 
                           required 
                           minlength="3" 
                           maxlength="200">
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label">Message</label>
                    <textarea id="content" 
                              name="content" 
                              class="form-control" 
                              rows="6" 
                              required 
                              minlength="5" 
                              maxlength="1000"><?= htmlspecialchars($announcement_data['message']) ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Posted On</label>
                    <input type="text" 
                           class="form-control" 
                           value="<?= htmlspecialchars($announcement_data['created_at']) ?>" 
                           readonly>
                    <div class="form-text">Creation date cannot be changed</div>
                </div>

                <button type="submit" name="update_announcement" class="btn btn-maroon w-100">Update Announcement</button>

            </form>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const title = document.getElementById('title').value.trim();
    const content = document.getElementById('content').value.trim();
    
    if (title.length < 3) {
        e.preventDefault();
        alert('Title must be at least 3 characters long.');
        return false;
    }
    
    if (content.length < 5) {
        e.preventDefault();
        alert('Message must be at least 5 characters long.');
        return false;
    }
});
</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
