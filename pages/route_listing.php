<?php
require_once('../classes/Database.php');
include('../classes/announcement.php');

$connection = Database::getConnection();

class Route {
    private $conn;
    public function __construct($connection) {
        $this->conn = $connection;
    }
    public function getAllRoutes() {
        $stmt = $this->conn->prepare("SELECT * FROM Route ORDER BY ID DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

$routeObj = new Route($connection);
$routes = $routeObj->getAllRoutes();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Route List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin-style.css">
</head>
<body>
<div class="container mt-4">
    <a href="admin.php" class="btn btn-maroon-outline back-btn">&larr; Back</a>
    <h1 class="text-center mb-4">Route List</h1>
    <table class="table table-bordered table-hover">
        <thead class="table-dark">
        <tr>
            <th>ID</th>            
            <th>Origin</th>
            <th>Destination</th>
            <th>Stops</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($routes)): ?>
            <?php foreach ($routes as $route): ?>
                <tr>
                    <td><?= htmlspecialchars($route['ID']) ?></td>
                    <td><?= htmlspecialchars($route['Origin'] ?? '') ?></td>
                    <td><?= htmlspecialchars($route['Destination'] ?? '') ?></td>
                    <td><?= htmlspecialchars($route['Stops'] ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="4" class="text-center">No routes found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
