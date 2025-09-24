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
     * Search buses with advanced filters
     * @param string $origin
     * @param string $destination
     * @param string $travelDate
     * @param array $filters
     * @return array
     */
    public function searchBusesWithFilters($origin, $destination, $travelDate, $filters = []) {
        // Sanitize inputs
        $origin = Database::sanitizeInput($origin);
        $destination = Database::sanitizeInput($destination);
        $travelDate = Database::sanitizeInput($travelDate);
        
        // Comprehensive validation
        if (!Database::validateInput($origin) || !Database::validateInput($destination)) {
            return ['error' => 'Please enter valid city names for origin and destination.'];
        }
        
        if ($origin === $destination) {
            return ['error' => 'Origin and destination cities must be different. Please select different locations.'];
        }
        
        if (!Database::validateInput($travelDate, 'date')) {
            return ['error' => 'Please enter a valid travel date in the correct format.'];
        }
        
        // Check if travel date is not in the past
        if (strtotime($travelDate) < strtotime(date('Y-m-d'))) {
            return ['error' => 'Travel date must be today or a future date. Please select a valid date.'];
        }
        
        // Build base query - Fixed column names and table references
        $query = "SELECT DISTINCT 
                    b.ID as bus_id,
                    b.BusNumber as bus_number,
                    b.Capacity as capacity,
                    r.Origin as origin,
                    r.Destination as destination,
                    r.Stops as stops,
                    s.DepartureTime as departure_time,
                    s.ArrivalTime as arrival_time,
                    s.Fare as fare,
                    (b.Capacity - COALESCE(booked_seats.count, 0)) as available_seats
                  FROM Bus b
                  INNER JOIN Route r ON b.RouteId = r.ID
                  INNER JOIN Schedule s ON b.ID = s.BusID
                  LEFT JOIN (
                      SELECT BusID, COUNT(*) as count 
                      FROM Booking 
                      WHERE Status = 'confirmed' 
                      AND TravelDate = :booking_date
                      GROUP BY BusID
                  ) booked_seats ON b.ID = booked_seats.BusID
                  WHERE (
                      -- Direct route match
                      (r.Origin = :origin1 AND r.Destination = :destination1) OR
                      -- Origin is terminal, destination is in stops
                      (r.Origin = :origin2 AND FIND_IN_SET(:destination2, r.Stops) > 0) OR
                      -- Origin is in stops, destination is terminal
                      (FIND_IN_SET(:origin3, r.Stops) > 0 AND r.Destination = :destination3) OR
                      -- Both are intermediate stops and in correct order
                      (FIND_IN_SET(:origin4, r.Stops) > 0 AND FIND_IN_SET(:destination4, r.Stops) > 0 
                       AND FIND_IN_SET(:origin5, r.Stops) < FIND_IN_SET(:destination5, r.Stops))
                  )
                  AND DATE(s.DepartureTime) = :travel_date";
        
        $params = [
            ':origin1' => $origin,
            ':destination1' => $destination,
            ':origin2' => $origin,
            ':destination2' => $destination,
            ':origin3' => $origin,
            ':destination3' => $destination,
            ':origin4' => $origin,
            ':destination4' => $destination,
            ':origin5' => $origin,
            ':destination5' => $destination,
            ':travel_date' => $travelDate,
            ':booking_date' => $travelDate
        ];
        
        // Apply filters
        if (!empty($filters['min_fare']) && is_numeric($filters['min_fare'])) {
            $query .= " AND s.Fare >= :min_fare";
            $params[':min_fare'] = (float)$filters['min_fare'];
        }
        
        if (!empty($filters['max_fare']) && is_numeric($filters['max_fare'])) {
            $query .= " AND s.Fare <= :max_fare";
            $params[':max_fare'] = (float)$filters['max_fare'];
        }
        
        if (!empty($filters['departure_time_from'])) {
            $query .= " AND TIME(s.DepartureTime) >= :departure_from";
            $params[':departure_from'] = $filters['departure_time_from'];
        }
        
        if (!empty($filters['departure_time_to'])) {
            $query .= " AND TIME(s.DepartureTime) <= :departure_to";
            $params[':departure_to'] = $filters['departure_time_to'];
        }
        
        if (!empty($filters['min_seats']) && is_numeric($filters['min_seats'])) {
            $query .= " HAVING available_seats >= :min_seats";
            $params[':min_seats'] = (int)$filters['min_seats'];
        }
        
        // Apply sorting
        $sortBy = $filters['sort_by'] ?? 'departure_time';
        switch ($sortBy) {
            case 'fare_low':
                $query .= " ORDER BY s.Fare ASC, s.DepartureTime ASC";
                break;
            case 'fare_high':
                $query .= " ORDER BY s.Fare DESC, s.DepartureTime ASC";
                break;
            case 'departure_early':
                $query .= " ORDER BY s.DepartureTime ASC";
                break;
            case 'departure_late':
                $query .= " ORDER BY s.DepartureTime DESC";
                break;
            case 'seats_available':
                $query .= " ORDER BY available_seats DESC, s.DepartureTime ASC";
                break;
            default:
                $query .= " ORDER BY s.DepartureTime ASC";
        }
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            
            $buses = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $buses[] = [
                    'bus_id' => $row['bus_id'],
                    'bus_number' => $row['bus_number'],
                    'capacity' => $row['capacity'],
                    'origin' => $row['origin'],
                    'destination' => $row['destination'],
                    'stops' => $row['stops'],
                    'departure_time' => $row['departure_time'],
                    'arrival_time' => $row['arrival_time'],
                    'fare' => $row['fare'],
                    'available_seats' => $row['available_seats']
                ];
            }
            
            return ['success' => true, 'data' => $buses];
            
        } catch(PDOException $exception) {
            error_log("Search error: " . $exception->getMessage());
            return ['error' => 'We are experiencing technical difficulties. Please try your search again in a few moments.'];
        }
    }
    
    /**
     * Get fare range for a specific route
     * @param string $origin
     * @param string $destination
     * @return array
     */
    public function getFareRange($origin, $destination) {
        try {
            $query = "SELECT MIN(s.Fare) as min_fare, MAX(s.Fare) as max_fare
                      FROM Schedule s
                      INNER JOIN Bus b ON s.BusID = b.ID
                      INNER JOIN Route r ON b.RouteId = r.ID
                      WHERE (
                          -- Direct route match
                          (r.Origin = ? AND r.Destination = ?) OR
                          -- Origin is terminal, destination is in stops
                          (r.Origin = ? AND FIND_IN_SET(?, r.Stops) > 0) OR
                          -- Origin is in stops, destination is terminal
                          (FIND_IN_SET(?, r.Stops) > 0 AND r.Destination = ?) OR
                          -- Both are intermediate stops and in correct order
                          (FIND_IN_SET(?, r.Stops) > 0 AND FIND_IN_SET(?, r.Stops) > 0 
                           AND FIND_IN_SET(?, r.Stops) < FIND_IN_SET(?, r.Stops))
                      )";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$origin, $destination, $origin, $destination, $origin, $destination, $origin, $destination, $origin, $destination]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return [
                'min_fare' => $result['min_fare'] ?? 0,
                'max_fare' => $result['max_fare'] ?? 5000
            ];
        } catch (PDOException $e) {
            error_log("Fare range error: " . $e->getMessage());
            return ['min_fare' => 0, 'max_fare' => 5000];
        }
    }
    
    /**
     * Get departure time range for a specific route
     * @param string $origin
     * @param string $destination
     * @return array
     */
    public function getDepartureTimeRange($origin, $destination) {
        try {
            $query = "SELECT MIN(TIME(s.DepartureTime)) as earliest_time, MAX(TIME(s.DepartureTime)) as latest_time
                      FROM Schedule s
                      INNER JOIN Bus b ON s.BusID = b.ID
                      INNER JOIN Route r ON b.RouteId = r.ID
                      WHERE (
                          -- Direct route match
                          (r.Origin = ? AND r.Destination = ?) OR
                          -- Origin is terminal, destination is in stops
                          (r.Origin = ? AND FIND_IN_SET(?, r.Stops) > 0) OR
                          -- Origin is in stops, destination is terminal
                          (FIND_IN_SET(?, r.Stops) > 0 AND r.Destination = ?) OR
                          -- Both are intermediate stops and in correct order
                          (FIND_IN_SET(?, r.Stops) > 0 AND FIND_IN_SET(?, r.Stops) > 0 
                           AND FIND_IN_SET(?, r.Stops) < FIND_IN_SET(?, r.Stops))
                      )";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$origin, $destination, $origin, $destination, $origin, $destination, $origin, $destination, $origin, $destination]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return [
                'earliest_time' => $result['earliest_time'] ?? '05:00',
                'latest_time' => $result['latest_time'] ?? '23:00'
            ];
        } catch (PDOException $e) {
            error_log("Departure time range error: " . $e->getMessage());
            return ['earliest_time' => '05:00', 'latest_time' => '23:00'];
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
            return ['error' => 'Please select a valid travel date.'];
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
                    AND b.TravelDate = :travel_date
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
            return ['error' => 'Unable to load seat information. Please refresh the page and try again.'];
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
                  FROM Bus b
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
                return ['error' => 'Bus not found. This bus may no longer be available for booking.'];
            }
            
        } catch(PDOException $exception) {
            error_log("Bus details error: " . $exception->getMessage());
            return ['error' => 'Unable to load bus details. Please try again.'];
        }
    }
}
?>