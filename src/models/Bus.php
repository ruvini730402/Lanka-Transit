<?php
/**
 * Bus Model
 * 
 * Handles bus-related database operations
 */

require_once __DIR__ . '/BaseModel.php';

class Bus extends BaseModel {
    protected string $table = 'buses';
    protected array $fillable = [
        'bus_number',
        'bus_type',
        'total_seats',
        'amenities',
        'operator_id',
        'status',
        'created_at',
        'updated_at'
    ];
    
    /**
     * Get buses with operator information
     * 
     * @return array
     */
    public function getBusesWithOperator(): array {
        $sql = "
            SELECT 
                b.*,
                o.name as operator_name,
                o.contact_number as operator_contact
            FROM {$this->table} b
            LEFT JOIN operators o ON b.operator_id = o.id
            WHERE b.status = 'active'
            ORDER BY b.bus_number
        ";
        
        return $this->query($sql);
    }
    
    /**
     * Find bus by bus number
     * 
     * @param string $busNumber
     * @return array|null
     */
    public function findByBusNumber(string $busNumber): ?array {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE bus_number = ? AND status = 'active'");
            $stmt->execute([$busNumber]);
            
            $result = $stmt->fetch();
            return $result ?: null;
            
        } catch (PDOException $e) {
            error_log("Database error in Bus::findByBusNumber - " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get available buses for a route and date
     * 
     * @param int $routeId
     * @param string $date
     * @return array
     */
    public function getAvailableBuses(int $routeId, string $date): array {
        $sql = "
            SELECT DISTINCT
                b.*,
                o.name as operator_name,
                bs.departure_time,
                bs.arrival_time,
                bs.fare,
                bs.id as schedule_id,
                (b.total_seats - COALESCE(booked.booked_seats, 0)) as available_seats
            FROM {$this->table} b
            INNER JOIN bus_schedules bs ON b.id = bs.bus_id
            INNER JOIN operators o ON b.operator_id = o.id
            LEFT JOIN (
                SELECT 
                    bs.id as schedule_id,
                    COUNT(bk.id) as booked_seats
                FROM bus_schedules bs
                LEFT JOIN bookings bk ON bs.id = bk.schedule_id 
                    AND bk.travel_date = ? 
                    AND bk.status IN ('confirmed', 'pending')
                GROUP BY bs.id
            ) booked ON bs.id = booked.schedule_id
            WHERE bs.route_id = ?
                AND b.status = 'active'
                AND bs.status = 'active'
            ORDER BY bs.departure_time
        ";
        
        return $this->query($sql, [$date, $routeId]);
    }
}
