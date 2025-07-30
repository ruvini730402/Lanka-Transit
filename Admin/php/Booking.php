<?php
class Booking {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getTotalBookings() {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM bookings");
        $stmt->execute();
        return $stmt->fetchColumn();
    }
}
?>
