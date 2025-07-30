<?php
class Dashboard {
    private $conn;

    public function __construct($db) {
        $this->conn = $db; // Save the database connection
    }

    public function getTotalBuses() {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM buses");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function getTotalUsers() {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM users");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function getTotalBookings() {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM bookings");
        $stmt->execute();
        return $stmt->fetchColumn();
    }
}
?>
