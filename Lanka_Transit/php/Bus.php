<?php
class Bus {
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    public function getAll() {
        $stmt = $this->conn->prepare("SELECT * FROM buses");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert($bus_no, $route, $driver_contact, $status, $seat_count) {
    try {
        // Check for duplicate bus_no first
        $checkSql = "SELECT COUNT(*) FROM buses WHERE bus_no = :bus_no";
        $checkStmt = $this->conn->prepare($checkSql);
        $checkStmt->execute([':bus_no' => $bus_no]);

        if ($checkStmt->fetchColumn() > 0) {
            // Duplicate found
            // You can throw exception or return specific error
            throw new Exception("Bus number already exists.");
        }

        // Proceed to insert
        $sql = "INSERT INTO buses (bus_no, route, driver_contact, status, seat_count)
                VALUES (:bus_no, :route, :driver_contact, :status, :seat_count)";

        $stmt = $this->conn->prepare($sql);
        $success = $stmt->execute([
            ':bus_no' => $bus_no,
            ':route' => $route,
            ':driver_contact' => $driver_contact,
            ':status' => $status,
            ':seat_count' => $seat_count
        ]);

        if (!$success) {
            $errorInfo = $stmt->errorInfo();
            throw new Exception("Insert failed: " . implode(" | ", $errorInfo));
        }

        return true;
    } catch (Exception $e) {
        error_log("Bus Insert Error: " . $e->getMessage());
        // Optionally, rethrow or handle the error
        return false;
    }
}

    public function delete($bus_no) {
        $stmt = $this->conn->prepare("DELETE FROM buses WHERE bus_no = :bus_no");
        return $stmt->execute([':bus_no' => $bus_no]);
    }

    public function getOne($bus_no) {
        $stmt = $this->conn->prepare("SELECT * FROM buses WHERE bus_no = :bus_no");
        $stmt->execute([':bus_no' => $bus_no]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($bus_no, $route, $contact, $status, $seats) {
    $stmt = $this->conn->prepare("UPDATE buses SET route = :route, driver_contact = :contact, status = :status, seat_count = :seats
                                  WHERE bus_no = :bus_no");
    return $stmt->execute([
        ':bus_no' => $bus_no,
        ':route' => $route,
        ':contact' => $contact,
        ':status' => $status,
        ':seats' => $seats
    ]);
}

    // New method added to get total count of buses
    public function getTotalBuses() {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM buses");
        $stmt->execute();
        return $stmt->fetchColumn();
    }
}
?>
