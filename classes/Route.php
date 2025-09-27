<?php
require_once "Database.php";

class Route {
    private $conn;

    public function __construct($connection = null) {
        $this->conn = $connection ?? Database::getConnection();
    }

    // Get all routes
    public function getAllRoutes() {
        $stmt = $this->conn->prepare("SELECT ID, Origin, Destination, Stops FROM Route ORDER BY ID DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get single route by ID
    public function getRoute($id) {
        $stmt = $this->conn->prepare("SELECT ID, Origin, Destination, Stops FROM Route WHERE ID = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Add new route
    public function addRoute($origin, $destination, $stops) {
        $stmt = $this->conn->prepare("INSERT INTO Route (Origin, Destination, Stops) VALUES (?, ?, ?)");
        return $stmt->execute([$origin, $destination, $stops]);
    }

    // Update route
    public function updateRoute($id, $origin, $destination, $stops) {
        $stmt = $this->conn->prepare("UPDATE Route SET Origin = ?, Destination = ?, Stops = ? WHERE ID = ?");
        return $stmt->execute([$origin, $destination, $stops, $id]);
    }

    // Delete route
    public function deleteRoute($id) {
        $stmt = $this->conn->prepare("DELETE FROM Route WHERE ID = ?");
        return $stmt->execute([$id]);
    }
}
?>
