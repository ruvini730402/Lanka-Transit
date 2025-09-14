<?php
session_start();
require_once('../classes/Database.php');
require_once('../classes/Route.php');

$connection = Database::getConnection();
$route = new Route($connection);

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    try {
        $data = $route->getRoute($_GET['id']);
        if (!$data) {
            $_SESSION['error_msg'] = "Route not found";
            header("Location: ../pages/route_listing.php");
            exit();
        }
    } catch (Exception $e) {
        $_SESSION['error_msg'] = "Error fetching route: " . $e->getMessage();
        header("Location: ../pages/route_listing.php");
        exit();
    }
} elseif (isset($_POST['update_route'])) {
    $id = $_POST['id'];
    $origin = trim($_POST['origin']);
    $destination = trim($_POST['destination']);
    $stops = trim($_POST['stops']);

    // Validate inputs
    if (empty($origin) || empty($destination)) {
        $_SESSION['error_msg'] = "Origin and destination are required";
        $_SESSION['form_data'] = $_POST;
        header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $id);
        exit();
    }

    try {
        // Check if another route with same origin/destination exists (excluding current route)
        $checkStmt = $connection->prepare("SELECT ID FROM Route WHERE Origin = ? AND Destination = ? AND ID != ?");
        $checkStmt->execute([$origin, $destination, $id]);
        
        if ($checkStmt->fetch()) {
            $_SESSION['error_msg'] = "Another route from $origin to $destination already exists";
            $_SESSION['form_data'] = $_POST;
            header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $id);
            exit();
        }

        if ($route->updateRoute($id, $origin, $destination, $stops)) {
            $_SESSION['success_msg'] = "Route updated successfully!";
            header("Location: ../pages/route_listing.php");
        } else {
            throw new Exception("Failed to update route");
        }
    } catch (Exception $e) {
        $_SESSION['error_msg'] = $e->getMessage();
        $_SESSION['form_data'] = $_POST;
        header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $id);
    }
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Route</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin-style.css">
    <?php include('../includes/toast_styles.php'); ?>
</head>
<body class="container mt-5">
    <!-- Back Button -->
    <a href="../pages/route_listing.php" class="btn btn-maroon-outline back-btn mb-3">&larr; Back</a>

    <div class="card">
        <div class="card-header">
            <h3 class="mb-0">Update Route</h3>
        </div>
        <div class="card-body">
            <?php include('../includes/toast_messages.php'); ?>
            
            <form method="POST">
                <input type="hidden" name="id" value="<?= htmlspecialchars($data['ID']) ?>">
                <div class="mb-3">
                    <label class="form-label">Origin</label>
                    <input type="text" name="origin" class="form-control" 
                           value="<?= htmlspecialchars(isset($_SESSION['form_data']['origin']) ? $_SESSION['form_data']['origin'] : $data['Origin']) ?>" 
                           required minlength="2" maxlength="100">
                </div>
                <div class="mb-3">
                    <label class="form-label">Destination</label>
                    <input type="text" name="destination" class="form-control" 
                           value="<?= htmlspecialchars(isset($_SESSION['form_data']['destination']) ? $_SESSION['form_data']['destination'] : $data['Destination']) ?>" 
                           required minlength="2" maxlength="100">
                </div>
                <div class="mb-3">
                    <label class="form-label">Stops (comma separated)</label>
                    <textarea name="stops" class="form-control" rows="3" maxlength="500"
                              placeholder="Stop 1, Stop 2, Stop 3..."><?= htmlspecialchars(isset($_SESSION['form_data']['stops']) ? $_SESSION['form_data']['stops'] : $data['Stops']) ?></textarea>
                </div>
                <div class="d-grid">
                    <button type="submit" name="update_route" class="btn btn-maroon">Update Route</button>
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