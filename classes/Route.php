<?php
/**
 * Route Class for Lanka Transit
 * Handles route-related operations following OOP principles
 */

require_once __DIR__ . '/Database.php';

class Route {
    private $pdo;
    private $id;
    private $origin;
    private $destination;
    private $stops;
    
    public function __construct($pdo = null) {
        if ($pdo) {
            $this->pdo = $pdo;
        } else {
            $database = new Database();
            $this->pdo = $database->getConnection();
        }
    }
    
    /**
     * Get all routes from the database
     * @return array
     */
    public function getAllRoutes() {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM Route ORDER BY Origin, Destination");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching all routes: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get route by ID
     * @param int $id
     * @return array|null
     */
    public function getRouteById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM Route WHERE ID = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log("Error fetching route by ID: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Create a new route
     * @param string $origin
     * @param string $destination
     * @param string $stops
     * @return bool
     */
    public function createRoute($origin, $destination, $stops = null) {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO Route (Origin, Destination, Stops) VALUES (?, ?, ?)");
            return $stmt->execute([$origin, $destination, $stops]);
        } catch (PDOException $e) {
            error_log("Error creating route: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update an existing route
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateRoute($id, $data) {
        try {
            $fields = [];
            $values = [];
            
            foreach ($data as $key => $value) {
                if (in_array($key, ['Origin', 'Destination', 'Stops'])) {
                    $fields[] = "$key = ?";
                    $values[] = $value;
                }
            }
            
            if (empty($fields)) {
                return false;
            }
            
            $values[] = $id;
            $sql = "UPDATE Route SET " . implode(', ', $fields) . " WHERE ID = ?";
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($values);
        } catch (PDOException $e) {
            error_log("Error updating route: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete a route
     * @param int $id
     * @return bool
     */
    public function deleteRoute($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM Route WHERE ID = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error deleting route: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get routes by origin and destination
     * @param string $origin
     * @param string $destination
     * @return array
     */
    public function getRoutesByOriginDestination($origin, $destination) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM Route 
                WHERE (Origin = ? AND Destination = ?) 
                   OR (Origin = ? AND FIND_IN_SET(?, Stops) > 0)
                   OR (FIND_IN_SET(?, Stops) > 0 AND Destination = ?)
                   OR (FIND_IN_SET(?, Stops) > 0 AND FIND_IN_SET(?, Stops) > 0 
                       AND FIND_IN_SET(?, Stops) < FIND_IN_SET(?, Stops))
                ORDER BY Origin, Destination
            ");
            
            $stmt->execute([
                $origin, $destination,          // Direct route
                $origin, $destination,          // Origin terminal to stop
                $origin, $destination,          // Stop to destination terminal
                $origin, $destination,          // Stop to stop (same params for position check)
                $origin, $destination
            ]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching routes by origin/destination: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get all unique locations (origins, destinations, and stops) with no duplicates
     * @return array Sorted array of unique location names
     */
    public function getAllUniqueLocations() {
        try {
            $locations = [];
            
            // Get all routes
            $stmt = $this->pdo->prepare("SELECT Origin, Destination, Stops FROM Route");
            $stmt->execute();
            $routes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($routes as $route) {
                // Add origin
                if (!empty($route['Origin'])) {
                    $locations[] = trim($route['Origin']);
                }
                
                // Add destination
                if (!empty($route['Destination'])) {
                    $locations[] = trim($route['Destination']);
                }
                
                // Add stops (comma-separated)
                if (!empty($route['Stops'])) {
                    $stops = explode(',', $route['Stops']);
                    foreach ($stops as $stop) {
                        $stop = trim($stop);
                        if (!empty($stop)) {
                            $locations[] = $stop;
                        }
                    }
                }
            }
            
            // Remove duplicates and sort
            $uniqueLocations = array_unique($locations);
            sort($uniqueLocations);
            
            return array_values($uniqueLocations); // Re-index array
            
        } catch (PDOException $e) {
            error_log("Error fetching unique locations: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get locations formatted for HTML select options
     * @return string HTML option elements
     */
    public function getLocationsAsSelectOptions() {
        $locations = $this->getAllUniqueLocations();
        $options = '';
        
        foreach ($locations as $location) {
            $escapedLocation = htmlspecialchars($location, ENT_QUOTES, 'UTF-8');
            $options .= "<option value=\"{$escapedLocation}\">{$escapedLocation}</option>\n";
        }
        
        return $options;
    }
    
    /**
     * Check if a location exists in any route
     * @param string $location
     * @return bool
     */
    public function locationExists($location) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as count FROM Route 
                WHERE Origin = ? 
                   OR Destination = ? 
                   OR FIND_IN_SET(?, Stops) > 0
            ");
            $stmt->execute([$location, $location, $location]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result['count'] > 0;
        } catch (PDOException $e) {
            error_log("Error checking location existence: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get route statistics
     * @return array
     */
    public function getRouteStatistics() {
        try {
            $stats = [];
            
            // Total routes
            $stmt = $this->pdo->prepare("SELECT COUNT(*) as total_routes FROM Route");
            $stmt->execute();
            $stats['total_routes'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_routes'];
            
            // Total unique locations
            $stats['total_locations'] = count($this->getAllUniqueLocations());
            
            // Routes with stops
            $stmt = $this->pdo->prepare("SELECT COUNT(*) as routes_with_stops FROM Route WHERE Stops IS NOT NULL AND Stops != ''");
            $stmt->execute();
            $stats['routes_with_stops'] = $stmt->fetch(PDO::FETCH_ASSOC)['routes_with_stops'];
            
            return $stats;
        } catch (PDOException $e) {
            error_log("Error fetching route statistics: " . $e->getMessage());
            return [];
        }
    }
}
?>