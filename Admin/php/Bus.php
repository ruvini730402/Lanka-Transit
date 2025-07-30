<?php
class Bus {
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    public function getAll() {
        $stmt = $this->conn->prepare("SELECT * FROM Bus");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert($bus_no, $route, $driver_contact, $status, $seat_count) {
    try {
        $checkSql = "SELECT COUNT(*) FROM Bus WHERE BusNumber = :bus_no";
        $checkStmt = $this->conn->prepare($checkSql);
        $checkStmt->execute([':bus_no' => $bus_no]);

        if ($checkStmt->fetchColumn() > 0) {
            throw new Exception("Bus number already exists.");
        }

        // Note: This assumes RouteId=1 and AdminId=1 as defaults since the form doesn't collect these
        $sql = "INSERT INTO Bus (RouteId, AdminId, BusNumber, Capacity, LastUpdate)
                VALUES (:route_id, :admin_id, :bus_no, :capacity, CURDATE())";

        $stmt = $this->conn->prepare($sql);
        $success = $stmt->execute([
            ':route_id' => 1, // Default route - should be updated to get from form
            ':admin_id' => 1, // Default admin - should be updated to get from session
            ':bus_no' => $bus_no,
            ':capacity' => $seat_count
        ]);

        if (!$success) {
            $errorInfo = $stmt->errorInfo();
            throw new Exception("Insert failed: " . implode(" | ", $errorInfo));
        }

        return true;
    } catch (Exception $e) {
        error_log("Bus Insert Error: " . $e->getMessage());
        return false;
    }
}

    public function delete($bus_no) {
        $stmt = $this->conn->prepare("DELETE FROM Bus WHERE BusNumber = :bus_no");
        return $stmt->execute([':bus_no' => $bus_no]);
    }

    public function getOne($bus_no) {
        $stmt = $this->conn->prepare("SELECT * FROM Bus WHERE BusNumber = :bus_no");
        $stmt->execute([':bus_no' => $bus_no]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($bus_no, $route, $contact, $status, $seats) {
    $stmt = $this->conn->prepare("UPDATE Bus SET RouteId = :route, Capacity = :seats, LastUpdate = CURDATE()
                                  WHERE BusNumber = :bus_no");
    return $stmt->execute([
        ':bus_no' => $bus_no,
        ':route' => $route,
        ':seats' => $seats
    ]);
}

    // New method added to get total count of buses
    public function getTotalBuses() {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM Bus");
        $stmt->execute();
        return $stmt->fetchColumn();
    }
}
?>
