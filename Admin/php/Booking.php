<?php
class Booking {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getTotalBookings() {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM Booking");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function getAll() {
        $stmt = $this->conn->prepare("
            SELECT 
                b.ID,
                b.SeatNumber,
                b.BookingTime,
                b.Status,
                b.PhoneNumber,
                b.Fare,
                b.TravelDate,
                b.Origin,
                b.Destination,
                u.Name as PassengerName,
                u.ID as UserId,
                bus.BusNumber,
                r.Origin as RouteOrigin,
                r.Destination as RouteDestination,
                p.Amount as PaymentAmount,
                p.PaymentMethod,
                p.Status as PaymentStatus,
                p.PaymentDate,
                pg.Gender
            FROM Booking b
            LEFT JOIN User u ON b.UserId = u.ID  
            LEFT JOIN Bus bus ON b.BusID = bus.ID
            LEFT JOIN Route r ON bus.RouteId = r.ID
            LEFT JOIN Payment p ON b.ID = p.BookingId
            LEFT JOIN PassengerGender pg ON b.ID = pg.BookingId
            ORDER BY b.BookingTime DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("
            SELECT 
                b.*,
                u.Name as PassengerName,
                u.Email as PassengerEmail,
                bus.BusNumber,
                r.Origin as RouteOrigin,
                r.Destination as RouteDestination,
                p.Amount as PaymentAmount,
                p.PaymentMethod,
                p.Status as PaymentStatus,
                p.PaymentDate,
                pg.Gender
            FROM Booking b
            LEFT JOIN User u ON b.UserId = u.ID  
            LEFT JOIN Bus bus ON b.BusID = bus.ID
            LEFT JOIN Route r ON bus.RouteId = r.ID
            LEFT JOIN Payment p ON b.ID = p.BookingId
            LEFT JOIN PassengerGender pg ON b.ID = pg.BookingId
            WHERE b.ID = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateStatus($id, $status) {
        try {
            $stmt = $this->conn->prepare("UPDATE Booking SET Status = ? WHERE ID = ?");
            return $stmt->execute([$status, $id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function delete($id) {
        try {
            $this->conn->beginTransaction();
            
            // Delete related payment records
            $stmt = $this->conn->prepare("DELETE FROM Payment WHERE BookingId = ?");
            $stmt->execute([$id]);
            
            // Delete related gender records
            $stmt = $this->conn->prepare("DELETE FROM PassengerGender WHERE BookingId = ?");
            $stmt->execute([$id]);
            
            // Delete booking
            $stmt = $this->conn->prepare("DELETE FROM Booking WHERE ID = ?");
            $result = $stmt->execute([$id]);
            
            $this->conn->commit();
            return $result;
        } catch (PDOException $e) {
            $this->conn->rollback();
            return false;
        }
    }

    public function getAllBuses() {
        $stmt = $this->conn->prepare("
            SELECT 
                b.ID,
                b.BusNumber,
                r.Origin,
                r.Destination
            FROM Bus b
            LEFT JOIN Route r ON b.RouteId = r.ID
            ORDER BY b.BusNumber
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllUsers() {
        $stmt = $this->conn->prepare("SELECT ID, Name, Email FROM User ORDER BY Name");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert($userId, $busId, $seatNumber, $phoneNumber, $fare, $travelDate, $origin, $destination, $gender = null) {
        try {
            $this->conn->beginTransaction();
            
            // Insert booking
            $stmt = $this->conn->prepare("
                INSERT INTO Booking (UserId, BusID, SeatNumber, PhoneNumber, Fare, TravelDate, Origin, Destination, Status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'confirmed')
            ");
            $stmt->execute([$userId, $busId, $seatNumber, $phoneNumber, $fare, $travelDate, $origin, $destination]);
            $bookingId = $this->conn->lastInsertId();
            
            // Insert gender if provided
            if ($gender) {
                $stmt = $this->conn->prepare("INSERT INTO PassengerGender (BookingId, Gender) VALUES (?, ?)");
                $stmt->execute([$bookingId, $gender]);
            }
            
            $this->conn->commit();
            return $bookingId;
        } catch (PDOException $e) {
            $this->conn->rollback();
            return false;
        }
    }

    public function update($id, $userId, $busId, $seatNumber, $phoneNumber, $fare, $travelDate, $origin, $destination, $status, $gender = null) {
        try {
            $this->conn->beginTransaction();
            
            // Update booking
            $stmt = $this->conn->prepare("
                UPDATE Booking 
                SET UserId = ?, BusID = ?, SeatNumber = ?, PhoneNumber = ?, Fare = ?, TravelDate = ?, Origin = ?, Destination = ?, Status = ?
                WHERE ID = ?
            ");
            $result = $stmt->execute([$userId, $busId, $seatNumber, $phoneNumber, $fare, $travelDate, $origin, $destination, $status, $id]);
            
            // Update or insert gender
            if ($gender) {
                $stmt = $this->conn->prepare("
                    INSERT INTO PassengerGender (BookingId, Gender) VALUES (?, ?) 
                    ON DUPLICATE KEY UPDATE Gender = ?
                ");
                $stmt->execute([$id, $gender, $gender]);
            }
            
            $this->conn->commit();
            return $result;
        } catch (PDOException $e) {
            $this->conn->rollback();
            return false;
        }
    }
}
?>
