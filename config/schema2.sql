-- Lanka Transit Extended Schema (schema2.sql)
-- Additional tables with "_2" suffix to extend original database structure

USE codebay_transit;

-- User_2 table to extend User with additional fields
CREATE TABLE User_2 (
    user_id INT PRIMARY KEY,
    reset_token VARCHAR(255),
    token_expiry DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES User(ID) ON DELETE CASCADE
);

-- Booking_2 table to extend Booking with additional fields
CREATE TABLE Booking_2 (
    booking_id INT PRIMARY KEY,
    gender ENUM('male', 'female', 'undisclosed') DEFAULT 'undisclosed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES Booking(ID) ON DELETE CASCADE
);

-- Admin_2 table to extend Admin with additional fields
CREATE TABLE Admin_2 (
    admin_id INT PRIMARY KEY,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES Admin(ID) ON DELETE CASCADE
);

-- Announcements table (actually used in admin/announcements.php and pages/announcements.php)
CREATE TABLE Announcements (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create indexes for better performance on extended tables
CREATE INDEX idx_user2_reset_token ON User_2(reset_token);
CREATE INDEX idx_user2_token_expiry ON User_2(token_expiry);
CREATE INDEX idx_user2_created_at ON User_2(created_at);
CREATE INDEX idx_booking2_gender ON Booking_2(gender);
CREATE INDEX idx_booking2_created_at ON Booking_2(created_at);
CREATE INDEX idx_admin2_created_at ON Admin_2(created_at);
CREATE INDEX idx_announcements_created_at ON Announcements(created_at);

-- Insert some sample data for testing

-- Sample announcements
INSERT INTO Announcements (title, message) VALUES
('Service Update', 'New bus route from Badulla to Matara now available with enhanced comfort features.'),
('Maintenance Notice', 'Scheduled maintenance on Route 1 buses every Sunday from 6 AM to 8 AM.'),
('Holiday Special', 'Special discount rates available for advance bookings during holiday season.');

-- Sample extended user data (if User with ID 1 exists)
INSERT IGNORE INTO User_2 (user_id, created_at) VALUES
(1, NOW());

-- Sample extended booking data (if Booking with ID 1 exists)
INSERT IGNORE INTO Booking_2 (booking_id, gender, created_at) VALUES
(1, 'undisclosed', NOW());

-- Sample extended admin data (if Admin with ID 1 exists)
INSERT IGNORE INTO Admin_2 (admin_id, created_at) VALUES
(1, NOW());