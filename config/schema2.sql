-- Lanka Transit Extended Schema (schema2.sql)
-- Additional tables with "_2" suffix to extend original database structure
-- RULE: DO NOT MODIFY ORIGINAL TABLES - Only create new tables with connections
-- ONLY INCLUDES ATTRIBUTES ACTUALLY USED IN PROJECT QUERIES

USE codebay_transit;

-- User_2 table to extend User with passenger name for booking forms
CREATE TABLE User_2 (
    user_id INT PRIMARY KEY,
    passenger_name VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES User(ID) ON DELETE CASCADE
);

-- Schedule_2 table to extend Schedule with travel_date for date-based searches
CREATE TABLE Schedule_2 (
    schedule_id INT PRIMARY KEY,
    travel_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (schedule_id) REFERENCES Schedule(ID) ON DELETE CASCADE
);

-- Booking_2 table to extend Booking with form fields and booking reference
CREATE TABLE Booking_2 (
    booking_id INT PRIMARY KEY,
    seat_number VARCHAR(10),
    passenger_name VARCHAR(100),
    travel_date DATE,
    booking_reference VARCHAR(50) UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES Booking(ID) ON DELETE CASCADE
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
CREATE INDEX idx_user2_passenger_name ON User_2(passenger_name);
CREATE INDEX idx_schedule2_travel_date ON Schedule_2(travel_date);
CREATE INDEX idx_booking2_travel_date ON Booking_2(travel_date);
CREATE INDEX idx_booking2_booking_reference ON Booking_2(booking_reference);
CREATE INDEX idx_booking2_passenger_name ON Booking_2(passenger_name);
CREATE INDEX idx_announcements_created_at ON Announcements(created_at);

-- Insert some sample data for testing

-- Sample announcements
INSERT INTO Announcements (title, message) VALUES
('Service Update', 'New bus route from Badulla to Matara now available with enhanced comfort features.'),
('Maintenance Notice', 'Scheduled maintenance on Route 1 buses every Sunday from 6 AM to 8 AM.'),
('Holiday Special', 'Special discount rates available for advance bookings during holiday season.');

-- Sample extended user data (if User with ID 1 exists)
INSERT IGNORE INTO User_2 (user_id, passenger_name) VALUES
(1, 'John Doe');

-- Sample schedule data (if Schedule with ID 1 exists)
INSERT IGNORE INTO Schedule_2 (schedule_id, travel_date) VALUES
(1, '2025-08-01');

-- Sample booking data (if Booking with ID 1 exists) 
INSERT IGNORE INTO Booking_2 (booking_id, seat_number, passenger_name, travel_date, booking_reference) VALUES
(1, 'A1', 'John Doe', '2025-08-01', 'LT-000001');
