<?php
/**
 * Route Model
 * 
 * Handles route-related database operations
 */

require_once __DIR__ . '/BaseModel.php';

class Route extends BaseModel {
    protected string $table = 'routes';
    protected array $fillable = [
        'origin',
        'destination', 
        'distance',
        'estimated_duration',
        'status',
        'created_at',
        'updated_at'
    ];
    
    /**
     * Search routes by origin and destination
     * 
     * @param string $origin
     * @param string $destination
     * @return array
     */
    public function searchRoutes(string $origin, string $destination): array {
        $sql = "
            SELECT * FROM {$this->table} 
            WHERE LOWER(origin) LIKE LOWER(?) 
                AND LOWER(destination) LIKE LOWER(?) 
                AND status = 'active'
            ORDER BY origin, destination
        ";
        
        return $this->query($sql, ["%{$origin}%", "%{$destination}%"]);
    }
    
    /**
     * Get all unique origins
     * 
     * @return array
     */
    public function getAllOrigins(): array {
        $sql = "
            SELECT DISTINCT origin 
            FROM {$this->table} 
            WHERE status = 'active' 
            ORDER BY origin
        ";
        
        return $this->query($sql);
    }
    
    /**
     * Get all unique destinations
     * 
     * @return array
     */
    public function getAllDestinations(): array {
        $sql = "
            SELECT DISTINCT destination 
            FROM {$this->table} 
            WHERE status = 'active' 
            ORDER BY destination
        ";
        
        return $this->query($sql);
    }
    
    /**
     * Get destinations for a given origin
     * 
     * @param string $origin
     * @return array
     */
    public function getDestinationsForOrigin(string $origin): array {
        $sql = "
            SELECT DISTINCT destination 
            FROM {$this->table} 
            WHERE LOWER(origin) = LOWER(?) 
                AND status = 'active' 
            ORDER BY destination
        ";
        
        return $this->query($sql, [$origin]);
    }
    
    /**
     * Find exact route
     * 
     * @param string $origin
     * @param string $destination
     * @return array|null
     */
    public function findExactRoute(string $origin, string $destination): ?array {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM {$this->table} 
                WHERE LOWER(origin) = LOWER(?) 
                    AND LOWER(destination) = LOWER(?) 
                    AND status = 'active'
                LIMIT 1
            ");
            $stmt->execute([$origin, $destination]);
            
            $result = $stmt->fetch();
            return $result ?: null;
            
        } catch (PDOException $e) {
            error_log("Database error in Route::findExactRoute - " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get routes with available buses
     * 
     * @param string $origin
     * @param string $destination
     * @param string $date
     * @return array
     */
    public function getRoutesWithAvailableBuses(string $origin, string $destination, string $date): array {
        $sql = "
            SELECT 
                r.*,
                COUNT(DISTINCT bs.id) as total_schedules,
                COUNT(DISTINCT CASE WHEN b.status = 'active' THEN bs.id END) as active_schedules
            FROM {$this->table} r
            LEFT JOIN bus_schedules bs ON r.id = bs.route_id
            LEFT JOIN buses b ON bs.bus_id = b.id
            WHERE LOWER(r.origin) LIKE LOWER(?)
                AND LOWER(r.destination) LIKE LOWER(?)
                AND r.status = 'active'
            GROUP BY r.id
            HAVING active_schedules > 0
            ORDER BY r.origin, r.destination
        ";
        
        return $this->query($sql, ["%{$origin}%", "%{$destination}%"]);
    }
}
