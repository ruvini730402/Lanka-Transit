<?php
class User {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getTotalUsers() {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM User");
        $stmt->execute();
        return $stmt->fetchColumn();
    }
}
?>
