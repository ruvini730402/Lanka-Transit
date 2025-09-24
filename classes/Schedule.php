<?php
/**
 * Schedule Class for Lanka Transit
 * Handles schedule-related operations following OOP principles
 * Enforces one schedule per bus per day constraint
 */

require_once __DIR__ . '/Database.php';

class Schedule {
    private $pdo;
    private $id;
    private $busId;
    private $departureTime;
    private $arrivalTime;
    private $fare;
    
    public function __construct($pdo = null) {
        if ($pdo) {
            $this->pdo = $pdo;
        } else {
            $database = new Database();
            $this->pdo = $database->getConnection();
        }
    }
    
    /**
     * Get all schedules with bus and route information
     * @return array
     */
    public function getAllSchedules() {
        try {
            $sql = "SELECT s.ID, s.BusID, s.DepartureTime, s.ArrivalTime, s.Fare,
                           b.BusNumber, b.Capacity,
                           r.Origin, r.Destination,
                           DATE(s.DepartureTime) as ScheduleDate,
                           TIME(s.DepartureTime) as DepartureTimeOnly,
                           TIME(s.ArrivalTime) as ArrivalTimeOnly
                    FROM Schedule s
                    JOIN Bus b ON s.BusID = b.ID
                    JOIN Route r ON b.RouteId = r.ID
                    ORDER BY s.DepartureTime DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching all schedules: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Search schedules by various criteria
     * @param string $searchTerm
     * @return array
     */
    public function searchSchedules($searchTerm) {
        try {
            $searchTerm = "%{$searchTerm}%";
            $sql = "SELECT s.ID, s.BusID, s.DepartureTime, s.ArrivalTime, s.Fare,
                           b.BusNumber, b.Capacity,
                           r.Origin, r.Destination,
                           DATE(s.DepartureTime) as ScheduleDate,
                           TIME(s.DepartureTime) as DepartureTimeOnly,
                           TIME(s.ArrivalTime) as ArrivalTimeOnly
                    FROM Schedule s
                    JOIN Bus b ON s.BusID = b.ID
                    JOIN Route r ON b.RouteId = r.ID
                    WHERE b.BusNumber LIKE ? 
                       OR r.Origin LIKE ? 
                       OR r.Destination LIKE ?
                       OR DATE(s.DepartureTime) LIKE ?
                    ORDER BY s.DepartureTime DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error searching schedules: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get schedule by ID with complete information
     * @param int $id
     * @return array|null
     */
    public function getScheduleById($id) {
        try {
            $sql = "SELECT s.ID, s.BusID, s.DepartureTime, s.ArrivalTime, s.Fare,
                           b.BusNumber, b.Capacity, b.RouteId,
                           r.Origin, r.Destination,
                           DATE(s.DepartureTime) as ScheduleDate,
                           TIME(s.DepartureTime) as DepartureTimeOnly,
                           TIME(s.ArrivalTime) as ArrivalTimeOnly
                    FROM Schedule s
                    JOIN Bus b ON s.BusID = b.ID
                    JOIN Route r ON b.RouteId = r.ID
                    WHERE s.ID = ?";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log("Error fetching schedule by ID: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get schedule statistics for analytics
     * @return array
     */
    public function getScheduleStatistics() {
        try {
            $stats = [];
            
            // Total schedules
            $stmt = $this->pdo->prepare("SELECT COUNT(*) as total_schedules FROM Schedule");
            $stmt->execute();
            $stats['total_schedules'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_schedules'];
            
            // Active schedules (future dates)
            $stmt = $this->pdo->prepare("SELECT COUNT(*) as active_schedules FROM Schedule WHERE DepartureTime > NOW()");
            $stmt->execute();
            $stats['active_schedules'] = $stmt->fetch(PDO::FETCH_ASSOC)['active_schedules'];
            
            // Today's schedules
            $stmt = $this->pdo->prepare("SELECT COUNT(*) as today_schedules FROM Schedule WHERE DATE(DepartureTime) = CURDATE()");
            $stmt->execute();
            $stats['today_schedules'] = $stmt->fetch(PDO::FETCH_ASSOC)['today_schedules'];
            
            // Unique buses with schedules
            $stmt = $this->pdo->prepare("SELECT COUNT(DISTINCT BusID) as scheduled_buses FROM Schedule");
            $stmt->execute();
            $stats['scheduled_buses'] = $stmt->fetch(PDO::FETCH_ASSOC)['scheduled_buses'];
            
            // Average fare
            $stmt = $this->pdo->prepare("SELECT AVG(Fare) as avg_fare FROM Schedule");
            $stmt->execute();
            $stats['avg_fare'] = round($stmt->fetch(PDO::FETCH_ASSOC)['avg_fare'], 2);
            
            // Schedule distribution by route
            $stmt = $this->pdo->prepare("
                SELECT r.Origin, r.Destination, COUNT(s.ID) as schedule_count
                FROM Schedule s
                JOIN Bus b ON s.BusID = b.ID
                JOIN Route r ON b.RouteId = r.ID
                GROUP BY r.ID, r.Origin, r.Destination
                ORDER BY schedule_count DESC
            ");
            $stmt->execute();
            $stats['route_distribution'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $stats;
        } catch (PDOException $e) {
            error_log("Error fetching schedule statistics: " . $e->getMessage());
            return [
                'total_schedules' => 0,
                'active_schedules' => 0,
                'today_schedules' => 0,
                'scheduled_buses' => 0,
                'avg_fare' => 0,
                'route_distribution' => []
            ];
        }
    }
    
    /**
     * Check if a bus already has a schedule on a specific date
     * @param int $busId
     * @param string $date (Y-m-d format)
     * @param int $excludeScheduleId (for updates)
     * @return bool
     */
    public function hasScheduleOnDate($busId, $date, $excludeScheduleId = null) {
        try {
            $sql = "SELECT COUNT(*) as count FROM Schedule WHERE BusID = ? AND DATE(DepartureTime) = ?";
            $params = [$busId, $date];
            
            if ($excludeScheduleId) {
                $sql .= " AND ID != ?";
                $params[] = $excludeScheduleId;
            }
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result['count'] > 0;
        } catch (PDOException $e) {
            error_log("Error checking schedule on date: " . $e->getMessage());
            return true; // Return true to prevent duplicate creation on error
        }
    }
    
    /**
     * Get all buses with their route information for schedule creation
     * @return array
     */
    public function getBusesForScheduling() {
        try {
            $sql = "SELECT b.ID, b.BusNumber, b.Capacity, 
                           r.Origin, r.Destination, r.ID as RouteId
                    FROM Bus b
                    JOIN Route r ON b.RouteId = r.ID
                    ORDER BY r.Origin, r.Destination, b.BusNumber";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching buses for scheduling: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Insert a new schedule
     * @param int $busId
     * @param string $departureTime
     * @param string $arrivalTime
     * @param float $fare
     * @return bool
     */
    public function insertSchedule($busId, $departureTime, $arrivalTime, $fare) {
        try {
            // Check if bus already has a schedule on this date
            $scheduleDate = date('Y-m-d', strtotime($departureTime));
            if ($this->hasScheduleOnDate($busId, $scheduleDate)) {
                return false; // Bus already has a schedule on this date
            }
            
            // Validate that departure time is before arrival time
            if (strtotime($departureTime) >= strtotime($arrivalTime)) {
                return false; // Invalid time range
            }
            
            $sql = "INSERT INTO Schedule (BusID, DepartureTime, ArrivalTime, Fare) VALUES (?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$busId, $departureTime, $arrivalTime, $fare]);
        } catch (PDOException $e) {
            error_log("Error inserting schedule: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update an existing schedule
     * @param int $id
     * @param int $busId
     * @param string $departureTime
     * @param string $arrivalTime
     * @param float $fare
     * @return bool
     */
    public function updateSchedule($id, $busId, $departureTime, $arrivalTime, $fare) {
        try {
            // Check if bus already has a schedule on this date (excluding current schedule)
            $scheduleDate = date('Y-m-d', strtotime($departureTime));
            if ($this->hasScheduleOnDate($busId, $scheduleDate, $id)) {
                return false; // Bus already has another schedule on this date
            }
            
            // Validate that departure time is before arrival time
            if (strtotime($departureTime) >= strtotime($arrivalTime)) {
                return false; // Invalid time range
            }
            
            $sql = "UPDATE Schedule SET BusID = ?, DepartureTime = ?, ArrivalTime = ?, Fare = ? WHERE ID = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$busId, $departureTime, $arrivalTime, $fare, $id]);
        } catch (PDOException $e) {
            error_log("Error updating schedule: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete a schedule
     * @param int $id
     * @return bool
     */
    public function deleteSchedule($id) {
        try {
            // Check if schedule has active bookings
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as booking_count 
                FROM Booking b 
                JOIN Schedule s ON b.BusID = s.BusID 
                WHERE s.ID = ? AND b.Status = 'confirmed'
            ");
            $stmt->execute([$id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result['booking_count'] > 0) {
                return false; // Cannot delete schedule with active bookings
            }
            
            $sql = "DELETE FROM Schedule WHERE ID = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error deleting schedule: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get bookings for a specific schedule
     * @param int $scheduleId
     * @return array
     */
    public function getScheduleBookings($scheduleId) {
        try {
            $sql = "SELECT b.ID, b.SeatNumber, b.BookingTime, b.Status, b.PhoneNumber, b.Fare,
                           u.Username, u.Email
                    FROM Booking b
                    LEFT JOIN User u ON b.UserId = u.ID
                    JOIN Schedule s ON b.BusID = s.BusID
                    WHERE s.ID = ?
                    ORDER BY b.BookingTime DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$scheduleId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching schedule bookings: " . $e->getMessage());
            return [];
        }
    }
    
    // Getter and Setter methods
    public function getId() { return $this->id; }
    public function getBusId() { return $this->busId; }
    public function getDepartureTime() { return $this->departureTime; }
    public function getArrivalTime() { return $this->arrivalTime; }
    public function getFare() { return $this->fare; }
    
    public function setId($id) { $this->id = $id; }
    public function setBusId($busId) { $this->busId = $busId; }
    public function setDepartureTime($departureTime) { $this->departureTime = $departureTime; }
    public function setArrivalTime($arrivalTime) { $this->arrivalTime = $arrivalTime; }
    public function setFare($fare) { $this->fare = $fare; }
}
?>