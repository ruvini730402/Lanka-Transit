<?php
session_start();
require_once('../classes/Database.php');
// Create database connection
$connection = Database::getConnection();
include('../classes/announcement.php');

$announcement = new Announcement($connection);

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    try {
        $data = $announcement->getById($_GET['id']);
        if (!$data) {
            $_SESSION['error_msg'] = "Announcement not found";
            header("Location: ../pages/announcement_display.php");
            exit();
        }
    } catch (Exception $e) {
        $_SESSION['error_msg'] = "Error fetching announcement: " . $e->getMessage();
        header("Location: ../pages/announcement_display.php");
        exit();
    }
} elseif (isset($_POST['update_announcement'])) {
    $id = $_POST['id'];
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    // Validate inputs
    if (empty($title) || empty($content)) {
        $_SESSION['error_msg'] = "Title and content are required";
        $_SESSION['form_data'] = $_POST;
        header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $id);
        exit();
    }

    try {
        if ($announcement->update($id, $title, $content)) {
            $_SESSION['success_msg'] = "Announcement updated successfully!";
            header("Location: ../pages/announcement_display.php");
        } else {
            throw new Exception("Failed to update announcement");
        }
    } catch (Exception $e) {
        $_SESSION['error_msg'] = $e->getMessage();
        $_SESSION['form_data'] = $_POST;
        header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $id);
    }
    exit();
}
?>

<!-- Update Form -->
<!DOCTYPE html>
<html>
<head>
    <title>Update Announcement</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin-style.css">
    <?php include('../includes/toast_styles.php'); ?>
</head>
<body class="container mt-5">
    <!-- Back Button -->
    <a href="../pages/announcement_display.php" class="btn btn-maroon-outline back-btn mb-3">&larr; Back</a>

    <div class="card">
        <div class="card-header">
            <h3 class="mb-0">Update Announcement</h3>
        </div>
        <div class="card-body">
            <?php include('../includes/toast_messages.php'); ?>
            
            <form method="POST">
                <input type="hidden" name="id" value="<?= htmlspecialchars($data['ID']) ?>">
                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" 
                           value="<?= htmlspecialchars(isset($_SESSION['form_data']['title']) ? $_SESSION['form_data']['title'] : $data['title']) ?>" 
                           required minlength="3" maxlength="200">
                </div>
                <div class="mb-3">
                    <label class="form-label">Message</label>
                    <textarea name="content" class="form-control" rows="4" 
                              required minlength="5" maxlength="1000"><?= htmlspecialchars(isset($_SESSION['form_data']['content']) ? $_SESSION['form_data']['content'] : $data['message']) ?></textarea>
                </div>
                <div class="d-grid">
                    <button type="submit" name="update_announcement" class="btn btn-maroon">Update Announcement</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
// Clear form data after displaying
unset($_SESSION['form_data']);
?>
