<?php
class Dashboard {
    private $conn;

    public function __construct($db) {
        $this->conn = $db; // Save the database connection
    }

    public function getTotalBuses() {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM Bus");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function getTotalUsers() {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM User");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function getTotalBookings() {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM Booking");
        $stmt->execute();
        return $stmt->fetchColumn();
    }
}
?>
