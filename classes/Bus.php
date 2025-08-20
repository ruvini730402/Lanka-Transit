<?php
require_once "Database.php";

class Bus {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    // Get all buses
    public function getAllBuses() {
        $stmt = $this->conn->prepare("
            SELECT b.ID, b.BusNumber, b.Capacity, b.LastUpdate, 
                    r.ID AS RouteID,
                    a.ID AS AdminID
            FROM Bus b
            LEFT JOIN Route r ON b.RouteId = r.ID
            LEFT JOIN Admin a ON b.AdminId = a.ID
            ORDER BY b.ID DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get single bus
    public function getBus($id) {
        $stmt = $this->conn->prepare("
            SELECT b.ID, b.BusNumber, b.Capacity, b.LastUpdate, 
                   b.RouteId, b.AdminId
            FROM Bus b
            LEFT JOIN Route r ON b.RouteId = r.ID
            LEFT JOIN Admin a ON b.AdminId = a.ID
            WHERE b.ID = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Add new bus
    public function addBus($routeId, $adminId, $busNumber, $capacity, $lastUpdate) {
        $stmt = $this->conn->prepare("
            INSERT INTO Bus (RouteId, AdminId, BusNumber, Capacity, LastUpdate) 
            VALUES (?, ?, ?, ?, ?)
        ");
        return $stmt->execute([$routeId, $adminId, $busNumber, $capacity, $lastUpdate]);
    }

    // Update bus
    public function updateBus($id, $routeId, $adminId, $busNumber, $capacity, $lastUpdate) {
        $stmt = $this->conn->prepare("
            UPDATE Bus 
            SET RouteId = ?, AdminId = ?, BusNumber = ?, Capacity = ?, LastUpdate = ?
            WHERE ID = ?
        ");
        return $stmt->execute([$routeId, $adminId, $busNumber, $capacity, $lastUpdate, $id]);
    }

    // Delete bus
    public function deleteBus($id) {
        $stmt = $this->conn->prepare("DELETE FROM Bus WHERE ID = ?");
        return $stmt->execute([$id]);
    }
}
?>
