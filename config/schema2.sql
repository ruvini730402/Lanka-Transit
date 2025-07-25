-- Lanka Transit Extended Schema (schema2.sql)
-- Additional tables with "_2" suffix to extend original database structure

USE codebay_transit;

-- Announcements table (actually used in admin/announcements.php and pages/announcements.php)
CREATE TABLE Announcements (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE INDEX idx_announcements_created_at ON Announcements(created_at);

-- Insert some sample data for testing

-- Sample announcements
INSERT INTO Announcements (title, message) VALUES
('Service Update', 'New bus route from Badulla to Matara now available with enhanced comfort features.'),
('Maintenance Notice', 'Scheduled maintenance on Route 1 buses every Sunday from 6 AM to 8 AM.'),
('Holiday Special', 'Special discount rates available for advance bookings during holiday season.');