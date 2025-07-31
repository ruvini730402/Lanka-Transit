<?php
//require_once '../config/database.php';

/**
 * Bus Class
 * Handles bus-related operations including search functionality
 */
class Bus {
    private $conn;
    private $table_name = "Bus";
    
    // Bus properties
    public $id;
    public $routeId;
    public $adminId;
    public $busNumber;
    public $capacity;
    public $lastUpdate;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Search buses based on filters
     * @param string $origin
     * @param string $destination
     * @param string $travelDate
     * @param float $maxFare
     * @return array
     */
    public function searchBuses($origin, $destination, $travelDate, $maxFare = null) {
        // Sanitize inputs
        $origin = Database::sanitizeInput($origin);
        $destination = Database::sanitizeInput($destination);
        $travelDate = Database::sanitizeInput($travelDate);
        
        // Validate inputs
        if (!Database::validateInput($origin) || !Database::validateInput($destination)) {
            return ['error' => 'Invalid origin or destination'];
        }
        
        if (!Database::validateInput($travelDate, 'date')) {
            return ['error' => 'Invalid travel date'];
        }
        
        // Check if travel date is not in the past
        if (strtotime($travelDate) < strtotime(date('Y-m-d'))) {
            return ['error' => 'Travel date cannot be in the past'];
        }
        
        // Build query
        $query = "SELECT DISTINCT 
                    b.ID as bus_id,
                    b.BusNumber,
                    b.Capacity,
                    r.Origin,
                    r.Destination,
                    r.Stops,
                    s.DepartureTime,
                    s.ArrivalTime,
                    s.Fare,
                    (b.Capacity - COALESCE(booked_seats.count, 0)) as available_seats
                  FROM " . $this->table_name . " b
                  INNER JOIN Route r ON b.RouteId = r.ID
                  INNER JOIN Schedule s ON b.ID = s.BusID
                  LEFT JOIN (
                      SELECT BusID, COUNT(*) as count 
                      FROM Booking 
                      WHERE Status = 'confirmed' 
                      AND DATE(BookingTime) = :booking_date
                      GROUP BY BusID
                  ) booked_seats ON b.ID = booked_seats.BusID
                  WHERE r.Origin = :origin 
                  AND r.Destination = :destination
                  AND DATE(s.DepartureTime) = :travel_date";
        
        $params = [
            ':origin' => $origin,
            ':destination' => $destination,
            ':travel_date' => $travelDate,
            ':booking_date' => $travelDate
        ];
        
        // Add fare filter if provided
        if ($maxFare !== null && is_numeric($maxFare)) {
            $query .= " AND s.Fare <= :max_fare";
            $params[':max_fare'] = $maxFare;
        }
        
        $query .= " ORDER BY s.DepartureTime ASC";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            
            $buses = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $buses[] = [
                    'bus_id' => $row['bus_id'],
                    'bus_number' => $row['BusNumber'],
                    'capacity' => $row['Capacity'],
                    'origin' => $row['Origin'],
                    'destination' => $row['Destination'],
                    'stops' => $row['Stops'],
                    'departure_time' => $row['DepartureTime'],
                    'arrival_time' => $row['ArrivalTime'],
                    'fare' => $row['Fare'],
                    'available_seats' => $row['available_seats']
                ];
            }
            
            return ['success' => true, 'data' => $buses];
            
        } catch(PDOException $exception) {
            error_log("Search error: " . $exception->getMessage());
            return ['error' => 'Database error occurred(SearchBus) '. $exception->getMessage()];
        }
    }
    
    /**
     * Get available seats for a specific bus
     * @param int $busId
     * @param string $travelDate
     * @return array
     */
    public function getAvailableSeats($busId, $travelDate) {
        $busId = (int)$busId;
        $travelDate = Database::sanitizeInput($travelDate);
        
        if (!Database::validateInput($travelDate, 'date')) {
            return ['error' => 'Invalid travel date'];
        }
        
        // First check if seat records exist for this bus
        $seatCheckQuery = "SELECT COUNT(*) FROM Seat WHERE BusID = ?";
        $stmt = $this->conn->prepare($seatCheckQuery);
        $stmt->execute([$busId]);
        $seatCount = $stmt->fetchColumn();
        
        if ($seatCount == 0) {
            // Generate default seat layout (40 seats)
            $seats = [];
            for ($i = 1; $i <= 40; $i++) {
                $seats[] = [
                    'seat_number' => (string)$i,
                    'gender_preference' => 'other',
                    'is_lady_seat' => ($i <= 8),
                    'status' => 'available'
                ];
            }
            return ['success' => true, 'data' => $seats];
        }
        
        $query = "SELECT 
                    s.SeatNumber,
                    s.GenderPreference,
                    s.IsLadySeat,
                    CASE 
                        WHEN b.ID IS NOT NULL THEN 'booked'
                        ELSE 'available'
                    END as status
                  FROM Seat s
                  LEFT JOIN Booking b ON s.BusID = b.BusID 
                    AND s.SeatNumber = b.SeatNumber 
                    AND b.Status = 'confirmed'
                    AND DATE(b.BookingTime) = :travel_date
                  WHERE s.BusID = :bus_id
                  ORDER BY s.SeatNumber";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':bus_id' => $busId,
                ':travel_date' => $travelDate
            ]);
            
            $seats = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $seats[] = [
                    'seat_number' => $row['SeatNumber'],
                    'gender_preference' => $row['GenderPreference'],
                    'is_lady_seat' => (bool)$row['IsLadySeat'],
                    'status' => $row['status']
                ];
            }
            
            return ['success' => true, 'data' => $seats];
            
        } catch(PDOException $exception) {
            error_log("Seat availability error: " . $exception->getMessage());
            return ['error' => 'Database error occurred(AvailableSeats)'];
        }
    }
    
    /**
     * Check if a seat is a lady seat
     * @param int $busId
     * @param string $seatNumber
     * @return bool
     */
    public function isLadySeat($busId, $seatNumber) {
        $busId = (int)$busId;
        $seatNumber = Database::sanitizeInput($seatNumber);
        
        $query = "SELECT IsLadySeat FROM Seat WHERE BusID = :bus_id AND SeatNumber = :seat_number";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':bus_id' => $busId,
                ':seat_number' => $seatNumber
            ]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? (bool)$result['IsLadySeat'] : false;
            
        } catch(PDOException $exception) {
            error_log("Lady seat check error: " . $exception->getMessage());
            return false;
        }
    }
    
    /**
     * Get bus details by ID
     * @param int $busId
     * @return array
     */
    public function getBusDetails($busId) {
        $busId = (int)$busId;
        
        $query = "SELECT 
                    b.ID,
                    b.BusNumber,
                    b.Capacity,
                    r.Origin,
                    r.Destination,
                    r.Stops,
                    s.DepartureTime,
                    s.ArrivalTime,
                    s.Fare
                  FROM " . $this->table_name . " b
                  INNER JOIN Route r ON b.RouteId = r.ID
                  INNER JOIN Schedule s ON b.ID = s.BusID
                  WHERE b.ID = :bus_id";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':bus_id' => $busId]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                return [
                    'success' => true,
                    'data' => [
                        'bus_id' => $result['ID'],
                        'bus_number' => $result['BusNumber'],
                        'capacity' => $result['Capacity'],
                        'origin' => $result['Origin'],
                        'destination' => $result['Destination'],
                        'stops' => $result['Stops'],
                        'departure_time' => $result['DepartureTime'],
                        'arrival_time' => $result['ArrivalTime'],
                        'fare' => $result['Fare']
                    ]
                ];
            } else {
                return ['error' => 'Bus not found'];
            }
            
        } catch(PDOException $exception) {
            error_log("Bus details error: " . $exception->getMessage());
            return ['error' => 'Database error occurred(BusDetails)'];
        }
    }
}
?>