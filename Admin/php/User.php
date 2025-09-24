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

    public function getAll() {
        $stmt = $this->conn->prepare("SELECT * FROM User ORDER BY Name ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM User WHERE ID = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insert($name, $email, $password, $phone, $role = 'registered user') {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        try {
            // Check if email already exists
            $checkStmt = $this->conn->prepare("SELECT ID FROM User WHERE Email = ?");
            $checkStmt->execute([$email]);
            if ($checkStmt->fetch()) {
                return false;
            }

            $stmt = $this->conn->prepare("INSERT INTO User (Name, Email, PasswordHash, PhoneNumber, Role) VALUES (?, ?, ?, ?, ?)");
            return $stmt->execute([$name, $email, $hashedPassword, $phone, $role]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function update($id, $name, $email, $phone, $role, $password = null) {
        try {
            if ($password) {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $this->conn->prepare("UPDATE User SET Name = ?, Email = ?, PasswordHash = ?, PhoneNumber = ?, Role = ? WHERE ID = ?");
                return $stmt->execute([$name, $email, $hashedPassword, $phone, $role, $id]);
            } else {
                $stmt = $this->conn->prepare("UPDATE User SET Name = ?, Email = ?, PhoneNumber = ?, Role = ? WHERE ID = ?");
                return $stmt->execute([$name, $email, $phone, $role, $id]);
            }
        } catch (PDOException $e) {
            return false;
        }
    }

    public function delete($id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM User WHERE ID = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>
