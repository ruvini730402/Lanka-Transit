<?php
class Announcement {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $stmt = $this->conn->prepare("SELECT * FROM announcements ORDER BY posted_date DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert($title, $content) {
        $stmt = $this->conn->prepare("INSERT INTO announcements (title, content) VALUES (?, ?)");
        return $stmt->execute([$title, $content]);
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM announcements WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $title, $content) {
        $stmt = $this->conn->prepare("UPDATE announcements SET title = ?, content = ? WHERE id = ?");
        return $stmt->execute([$title, $content, $id]);
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM announcements WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>
