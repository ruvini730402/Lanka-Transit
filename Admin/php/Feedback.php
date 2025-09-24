<?php
class Feedback {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $stmt = $this->conn->prepare("
            SELECT 
                f.ID,
                f.UserId,
                f.BusId,
                f.BookingId,
                f.Comment,
                f.Rating,
                f.CreatedDate,
                u.Name as UserName,
                u.Email as UserEmail,
                bus.BusNumber,
                r.Origin,
                r.Destination,
                b.SeatNumber,
                b.TravelDate
            FROM Feedback f
            LEFT JOIN User u ON f.UserId = u.ID  
            LEFT JOIN Bus bus ON f.BusId = bus.ID
            LEFT JOIN Route r ON bus.RouteId = r.ID
            LEFT JOIN Booking b ON f.BookingId = b.ID
            ORDER BY f.CreatedDate DESC, f.ID DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("
            SELECT 
                f.*,
                u.Name as UserName,
                u.Email as UserEmail,
                bus.BusNumber,
                r.Origin,
                r.Destination,
                b.SeatNumber,
                b.TravelDate
            FROM Feedback f
            LEFT JOIN User u ON f.UserId = u.ID  
            LEFT JOIN Bus bus ON f.BusId = bus.ID
            LEFT JOIN Route r ON bus.RouteId = r.ID
            LEFT JOIN Booking b ON f.BookingId = b.ID
            WHERE f.ID = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insert($userId, $busId, $bookingId, $comment, $rating) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO Feedback (UserId, BusId, BookingId, Comment, Rating, CreatedDate) 
                VALUES (?, ?, ?, ?, ?, CURDATE())
            ");
            return $stmt->execute([$userId, $busId, $bookingId, $comment, $rating]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function update($id, $userId, $busId, $bookingId, $comment, $rating) {
        try {
            $stmt = $this->conn->prepare("
                UPDATE Feedback 
                SET UserId = ?, BusId = ?, BookingId = ?, Comment = ?, Rating = ?
                WHERE ID = ?
            ");
            return $stmt->execute([$userId, $busId, $bookingId, $comment, $rating, $id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function delete($id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM Feedback WHERE ID = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getAllUsers() {
        $stmt = $this->conn->prepare("SELECT ID, Name, Email FROM User ORDER BY Name");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    public function getAllBookings() {
        $stmt = $this->conn->prepare("
            SELECT 
                b.ID,
                b.SeatNumber,
                b.TravelDate,
                u.Name as UserName,
                bus.BusNumber
            FROM Booking b
            LEFT JOIN User u ON b.UserId = u.ID
            LEFT JOIN Bus bus ON b.BusID = bus.ID
            ORDER BY b.TravelDate DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalFeedbacks() {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM Feedback");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function getAverageRating() {
        $stmt = $this->conn->prepare("SELECT AVG(Rating) FROM Feedback WHERE Rating IS NOT NULL");
        $stmt->execute();
        return round($stmt->fetchColumn(), 1);
    }

    public function getRatingDistribution() {
        $stmt = $this->conn->prepare("
            SELECT Rating, COUNT(*) as Count 
            FROM Feedback 
            WHERE Rating IS NOT NULL 
            GROUP BY Rating 
            ORDER BY Rating DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>