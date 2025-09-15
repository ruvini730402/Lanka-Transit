<?php
/**
 * Announcement Class
 * Handles announcements management for Lanka Transit
 */

require_once 'Database.php';

class Announcement {
    private $db;
    private $conn;
    
    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }
    
    /**
     * Create a new announcement
     * @param string $title
     * @param string $message
     * @return boolean
     */
    public function createAnnouncement($title, $message) {
        try {
            $sql = "INSERT INTO Announcements (title, message, created_at, updated_at) 
                    VALUES (?, ?, NOW(), NOW())";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$title, $message]);
        } catch (Exception $e) {
            error_log("Error creating announcement: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all announcements ordered by creation date (newest first)
     * @return array
     */
    public function getAllAnnouncements() {
        try {
            $sql = "SELECT ID, title, message, created_at, updated_at 
                    FROM Announcements 
                    ORDER BY created_at DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error fetching announcements: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get recent announcements with limit
     * @param int $limit
     * @return array
     */
    public function getRecentAnnouncements($limit = 5) {
        try {
            $sql = "SELECT ID, title, message, created_at, updated_at 
                    FROM Announcements 
                    ORDER BY created_at DESC 
                    LIMIT ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error fetching recent announcements: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Update an announcement
     * @param int $id
     * @param string $title
     * @param string $message
     * @return boolean
     */
    public function updateAnnouncement($id, $title, $message) {
        try {
            $sql = "UPDATE Announcements 
                    SET title = ?, message = ?, updated_at = NOW() 
                    WHERE ID = ?";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$title, $message, $id]);
        } catch (Exception $e) {
            error_log("Error updating announcement: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete an announcement
     * @param int $id
     * @return boolean
     */
    public function deleteAnnouncement($id) {
        try {
            $sql = "DELETE FROM Announcements WHERE ID = ?";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$id]);
        } catch (Exception $e) {
            error_log("Error deleting announcement: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get announcement by ID
     * @param int $id
     * @return array|null
     */
    public function getAnnouncementById($id) {
        try {
            $sql = "SELECT ID, title, message, created_at, updated_at 
                    FROM Announcements 
                    WHERE ID = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (Exception $e) {
            error_log("Error fetching announcement by ID: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get announcements count
     * @return int
     */
    public function getAnnouncementsCount() {
        try {
            $sql = "SELECT COUNT(*) as count FROM Announcements";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$result['count'];
        } catch (Exception $e) {
            error_log("Error counting announcements: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Check if announcements table exists and create if not
     * @return boolean
     */
    public function initializeTable() {
        try {
            $sql = "CREATE TABLE IF NOT EXISTS Announcements (
                ID INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(200) NOT NULL,
                message TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error initializing announcements table: " . $e->getMessage());
            return false;
        }
    }
}
?>
