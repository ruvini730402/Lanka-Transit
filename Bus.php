<?php
require_once "Database.php";

class Bus {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    // Validate bus number format (NB-####)
    private function validateBusNumber($busNumber) {
        return preg_match('/^NB-\d{4}$/', $busNumber);
    }

    // Validate bus capacity (must be 54 or 49)
    private function validateCapacity($capacity) {
        return in_array($capacity, [49, 54]);
    }

    // Validate if route exists
    private function validateRoute($routeId) {
        $stmt = $this->conn->prepare("SELECT ID FROM Route WHERE ID = ?");
        $stmt->execute([$routeId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }

    // Check if bus number already exists
    private function isBusNumberDuplicate($busNumber, $excludeId = null) {
        if ($excludeId) {
            $stmt = $this->conn->prepare("SELECT ID FROM Bus WHERE BusNumber = ? AND ID != ?");
            $stmt->execute([$busNumber, $excludeId]);
        } else {
            $stmt = $this->conn->prepare("SELECT ID FROM Bus WHERE BusNumber = ?");
            $stmt->execute([$busNumber]);
        }
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }

    // Get all buses
    public function getAllBuses() {
        $stmt = $this->conn->prepare("
            SELECT b.ID, b.BusNumber, b.Capacity, b.LastUpdate, 
                   r.ID AS RouteID, r.Origin, r.Destination,
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
                   b.RouteId, b.AdminId,
                   r.Origin, r.Destination
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
        // Validate bus number format
        if (!$this->validateBusNumber($busNumber)) {
            throw new Exception("Invalid bus number format. Must be NB-#### (four numbers)");
        }

        // Check for duplicate bus number
        if ($this->isBusNumberDuplicate($busNumber)) {
            throw new Exception("Bus number already exists. Please use a different bus number");
        }

        // Validate bus capacity
        if (!$this->validateCapacity($capacity)) {
            throw new Exception("Invalid bus capacity. Must be either 49 or 54 seats");
        }

        // Validate route ID
        if (!$this->validateRoute($routeId)) {
            throw new Exception("Invalid route ID. The specified route does not exist");
        }

        $stmt = $this->conn->prepare("
            INSERT INTO Bus (RouteId, AdminId, BusNumber, Capacity, LastUpdate) 
            VALUES (?, ?, ?, ?, ?)
        ");
        return $stmt->execute([$routeId, $adminId, $busNumber, $capacity, $lastUpdate]);
    }

    // Update bus
    public function updateBus($id, $routeId, $adminId, $busNumber, $capacity, $lastUpdate) {
        // Validate bus number format
        if (!$this->validateBusNumber($busNumber)) {
            throw new Exception("Invalid bus number format. Must be NB-#### (four numbers)");
        }

        // Validate bus capacity
        if (!$this->validateCapacity($capacity)) {
            throw new Exception("Invalid bus capacity. Must be either 49 or 54 seats");
        }

        // Validate route ID
        if (!$this->validateRoute($routeId)) {
            throw new Exception("Invalid route ID. The specified route does not exist");
        }

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
