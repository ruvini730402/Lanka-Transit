<?php
require_once "Database.php";

class Bus {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
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
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }

    // Get all buses
    public function getAllBuses() {
        $stmt = $this->conn->prepare("
            SELECT b.ID, b.BusNumber, b.Capacity, b.LastUpdate, 
                   r.ID AS RouteID, r.Origin, r.Destination,
                   a.ID AS AdminID
            FROM Bus b
            LEFT JOIN Route r ON b.RouteId = r.ID
            LEFT JOIN Admin a ON b.AdminId = a.ID
            ORDER BY b.ID DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get single bus
    public function getBus($id) {
        $stmt = $this->conn->prepare("
            SELECT b.ID, b.BusNumber, b.Capacity, b.LastUpdate, 
                   b.RouteId, b.AdminId,
                   r.Origin, r.Destination
            FROM Bus b
            LEFT JOIN Route r ON b.RouteId = r.ID
            LEFT JOIN Admin a ON b.AdminId = a.ID
            WHERE b.ID = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Add new bus
    public function addBus($routeId, $adminId, $busNumber, $capacity, $lastUpdate) {
        // Validate bus number format
        if (!$this->validateBusNumber($busNumber)) {
            throw new Exception("Invalid bus number format. Must be NB-#### (four numbers)");
        }

        
        // Build base query - Modified to include intermediate stops
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
                  WHERE (
                      (r.Origin = :origin1 AND r.Destination = :destination1) OR
                      (FIND_IN_SET(:origin2, r.Stops) > 0 AND FIND_IN_SET(:destination2, r.Stops) > 0 
                       AND FIND_IN_SET(:origin3, r.Stops) < FIND_IN_SET(:destination3, r.Stops))
                  )
                  AND DATE(s.DepartureTime) = :travel_date";
        
        $params = [
            ':origin1' => $origin,
            ':destination1' => $destination,
            ':origin2' => $origin,
            ':destination2' => $destination,
            ':origin3' => $origin,
            ':destination3' => $destination,
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
            return ['error' => 'We are experiencing technical difficulties. Please try your search again in a few moments.'];

        }

        $stmt = $this->conn->prepare("
            INSERT INTO Bus (RouteId, AdminId, BusNumber, Capacity, LastUpdate) 
            VALUES (?, ?, ?, ?, ?)
        ");
        return $stmt->execute([$routeId, $adminId, $busNumber, $capacity, $lastUpdate]);
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
                          (r.Origin = ? AND r.Destination = ?) OR
                          (FIND_IN_SET(?, r.Stops) > 0 AND FIND_IN_SET(?, r.Stops) > 0 
                           AND FIND_IN_SET(?, r.Stops) < FIND_IN_SET(?, r.Stops))
                      )";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$origin, $destination, $origin, $destination, $origin, $destination]);
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
                          (r.Origin = ? AND r.Destination = ?) OR
                          (FIND_IN_SET(?, r.Stops) > 0 AND FIND_IN_SET(?, r.Stops) > 0 
                           AND FIND_IN_SET(?, r.Stops) < FIND_IN_SET(?, r.Stops))
                      )";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$origin, $destination, $origin, $destination, $origin, $destination]);
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

        // Validate bus capacity
        if (!$this->validateCapacity($capacity)) {
            throw new Exception("Invalid bus capacity. Must be either 49 or 54 seats");
        }

        // Validate route ID
        if (!$this->validateRoute($routeId)) {
            throw new Exception("Invalid route ID. The specified route does not exist");
        }

        $stmt = $this->conn->prepare("
            UPDATE Bus 
            SET RouteId = ?, AdminId = ?, BusNumber = ?, Capacity = ?, LastUpdate = ?
            WHERE ID = ?
        ");
        return $stmt->execute([$routeId, $adminId, $busNumber, $capacity, $lastUpdate, $id]);
    }

    // Delete bus
    public function deleteBus($id) {
        $stmt = $this->conn->prepare("DELETE FROM Bus WHERE ID = ?");
        return $stmt->execute([$id]);
    }
}
  // Validate bus number format (NB-####)
    private function validateBusNumber($busNumber) {
        return preg_match('/^NB-\d{4}$/', $busNumber);
    }

    // Validate bus capacity (must be 54 or 49)
    private function validateCapacity($capacity) {
        return in_array($capacity, [49, 54]);
    }

    // Validate if route exists
    private function validateRoute($routeId) {
        $stmt = $this->conn->prepare("SELECT ID FROM Route WHERE ID = ?");
        $stmt->execute([$routeId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }

    // Check if bus number already exists
    private function isBusNumberDuplicate($busNumber, $excludeId = null) {
        if ($excludeId) {
            $stmt = $this->conn->prepare("SELECT ID FROM Bus WHERE BusNumber = ? AND ID != ?");
            $stmt->execute([$busNumber, $excludeId]);
        } else {
            $stmt = $this->conn->prepare("SELECT ID FROM Bus WHERE BusNumber = ?");
            $stmt->execute([$busNumber]);
           // Check for duplicate bus number
        if ($this->isBusNumberDuplicate($busNumber)) {
            throw new Exception("Bus number already exists. Please use a different bus number");
        }

        // Validate bus capacity
        if (!$this->validateCapacity($capacity)) {
            throw new Exception("Invalid bus capacity. Must be either 49 or 54 seats");
        }

        // Validate route ID
        if (!$this->validateRoute($routeId)) {
            throw new Exception("Invalid route ID. The specified route does not exist");
          
          // Update bus
    public function updateBus($id, $routeId, $adminId, $busNumber, $capacity, $lastUpdate) {
        // Validate bus number format
        if (!$this->validateBusNumber($busNumber)) {
            throw new Exception("Invalid bus number format. Must be NB-#### (four numbers)");
?>
