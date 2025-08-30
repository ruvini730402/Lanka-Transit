<?php
class Announcement {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $stmt = $this->conn->prepare("SELECT * FROM Announcements ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert($title, $content) {
        $stmt = $this->conn->prepare("INSERT INTO Announcements (title, message) VALUES (?, ?)");
        return $stmt->execute([$title, $content]);
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM Announcements WHERE ID = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $title, $content) {
        $stmt = $this->conn->prepare("UPDATE Announcements SET title = ?, message = ? WHERE ID = ?");
        return $stmt->execute([$title, $content, $id]);
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM Announcements WHERE ID = ?");
        return $stmt->execute([$id]);
    }
}
?>
