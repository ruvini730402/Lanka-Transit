<?php
class Route {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $stmt = $this->conn->prepare("
            SELECT 
                r.*,
                COUNT(b.ID) as BusCount
            FROM Route r
            LEFT JOIN Bus b ON r.ID = b.RouteId
            GROUP BY r.ID
            ORDER BY r.Origin, r.Destination
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM Route WHERE ID = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insert($origin, $destination, $stops = null) {
        try {
            // Check if route already exists
            $checkStmt = $this->conn->prepare("SELECT ID FROM Route WHERE Origin = ? AND Destination = ?");
            $checkStmt->execute([$origin, $destination]);
            if ($checkStmt->fetch()) {
                return false; // Route already exists
            }

            $stmt = $this->conn->prepare("INSERT INTO Route (Origin, Destination, Stops) VALUES (?, ?, ?)");
            return $stmt->execute([$origin, $destination, $stops]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function update($id, $origin, $destination, $stops = null) {
        try {
            // Check if another route with same origin/destination exists (excluding current route)
            $checkStmt = $this->conn->prepare("SELECT ID FROM Route WHERE Origin = ? AND Destination = ? AND ID != ?");
            $checkStmt->execute([$origin, $destination, $id]);
            if ($checkStmt->fetch()) {
                return false; // Another route with same origin/destination exists
            }

            $stmt = $this->conn->prepare("UPDATE Route SET Origin = ?, Destination = ?, Stops = ? WHERE ID = ?");
            return $stmt->execute([$origin, $destination, $stops, $id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function delete($id) {
        try {
            // Check if route is being used by any buses
            $checkStmt = $this->conn->prepare("SELECT COUNT(*) FROM Bus WHERE RouteId = ?");
            $checkStmt->execute([$id]);
            $busCount = $checkStmt->fetchColumn();
            
            if ($busCount > 0) {
                return ['success' => false, 'message' => "Cannot delete route. $busCount bus(es) are using this route."];
            }

            $stmt = $this->conn->prepare("DELETE FROM Route WHERE ID = ?");
            $success = $stmt->execute([$id]);
            
            return ['success' => $success, 'message' => $success ? 'Route deleted successfully' : 'Failed to delete route'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function getBusesOnRoute($routeId) {
        $stmt = $this->conn->prepare("
            SELECT 
                b.ID,
                b.BusNumber,
                b.Capacity,
                a.Name as AdminName
            FROM Bus b
            LEFT JOIN Admin a ON b.AdminId = a.ID
            WHERE b.RouteId = ?
            ORDER BY b.BusNumber
        ");
        $stmt->execute([$routeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalRoutes() {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM Route");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function getRouteStatistics() {
        $stmt = $this->conn->prepare("
            SELECT 
                COUNT(DISTINCT r.ID) as TotalRoutes,
                COUNT(b.ID) as TotalBuses,
                AVG(b.Capacity) as AvgCapacity
            FROM Route r
            LEFT JOIN Bus b ON r.ID = b.RouteId
        ");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function searchRoutes($searchTerm) {
        $stmt = $this->conn->prepare("
            SELECT 
                r.*,
                COUNT(b.ID) as BusCount
            FROM Route r
            LEFT JOIN Bus b ON r.ID = b.RouteId
            WHERE r.Origin LIKE ? OR r.Destination LIKE ? OR r.Stops LIKE ?
            GROUP BY r.ID
            ORDER BY r.Origin, r.Destination
        ");
        $searchParam = "%$searchTerm%";
        $stmt->execute([$searchParam, $searchParam, $searchParam]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>