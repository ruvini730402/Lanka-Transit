-- Lanka Transit Sample Data for Incident Reporting
-- This file adds sample data for testing the incident reporting and management functionality

USE transit;

-- First, let's add some sample users for testing
INSERT IGNORE INTO User (Name, Email, PasswordHash, PhoneNumber, Role) VALUES
('John Silva', 'john.silva@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0771234567', 'registered user'),
('Mary Fernando', 'mary.fernando@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0772345678', 'registered user'),
('David Perera', 'david.perera@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0773456789', 'registered user'),
('Sarah Jayawardena', 'sarah.jay@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0774567890', 'registered user'),
('Guest User 1', 'guest1@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0775678901', 'guest user'),
('Admin User', 'admin.user@lankatransit.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0776789012', 'administrator');

-- Add some sample buses
INSERT IGNORE INTO Bus (RouteId, AdminId, BusNumber, Capacity, LastUpdate) VALUES
(1, 1, 'LT001', 45, '2025-07-20'),
(1, 1, 'LT002', 50, '2025-07-21'),
(2, 1, 'LT003', 45, '2025-07-22'),
(2, 1, 'LT004', 40, '2025-07-23'),
(1, 1, 'LT005', 48, '2025-07-24');

-- Add sample schedules
INSERT IGNORE INTO Schedule (BusID, DepartureTime, ArrivalTime, Fare) VALUES
(1, '2025-07-25 06:00:00', '2025-07-25 14:30:00', 1250.00),
(1, '2025-07-25 15:00:00', '2025-07-25 23:30:00', 1250.00),
(2, '2025-07-25 07:30:00', '2025-07-25 16:00:00', 1300.00),
(3, '2025-07-25 08:00:00', '2025-07-25 16:30:00', 1250.00),
(4, '2025-07-25 09:30:00', '2025-07-25 18:00:00', 1200.00),
(5, '2025-07-25 10:00:00', '2025-07-25 18:30:00', 1280.00);

-- Add sample bookings
INSERT IGNORE INTO Booking (UserId, BusID, SeatNumber, BookingTime, Status, PhoneNumber, Fare) VALUES
(1, 1, 'A01', '2025-07-20 10:30:00', 'confirmed', '0771234567', 1250.00),
(2, 1, 'A02', '2025-07-20 11:45:00', 'confirmed', '0772345678', 1250.00),
(3, 2, 'B05', '2025-07-21 09:15:00', 'completed', '0773456789', 1300.00),
(4, 3, 'C10', '2025-07-22 14:20:00', 'confirmed', '0774567890', 1250.00),
(5, 4, 'D15', '2025-07-23 16:30:00', 'cancelled', '0775678901', 1200.00),
(1, 5, 'A08', '2025-07-24 08:45:00', 'confirmed', '0771234567', 1280.00),
(2, 2, 'B12', '2025-07-22 12:00:00', 'completed', '0772345678', 1300.00),
(6, 1, 'A15', '2025-07-24 13:30:00', 'confirmed', '0776789012', 1250.00);

-- Sample Incident Data with Various Scenarios
INSERT IGNORE INTO Incident (UserId, AdminId, BookingId, Description, Status, ReportedDate, ResolvedDate) VALUES

-- Recent incidents (submitted status)
(1, NULL, 1, 'Bus was 45 minutes late due to heavy traffic near Wellawaya. Passengers were not informed about the delay.', 'submitted', '2025-07-24', NULL),

(2, NULL, 2, 'Air conditioning was not working during the journey from Badulla to Matara. Very uncomfortable for passengers especially during the afternoon heat.', 'submitted', '2025-07-24', NULL),

(NULL, NULL, NULL, 'Driver was using mobile phone while driving between Ella and Wellawaya. This is very dangerous and unprofessional behavior. Bus number LT001.', 'submitted', '2025-07-24', NULL),

-- In progress incidents
(3, 1, 3, 'Seat cushion was torn and springs were coming out. Booking ID reference. Seat number B05 in bus LT002 needs immediate repair.', 'in progress', '2025-07-23', NULL),

(4, 1, 4, 'Bus broke down near Thanamalvila and passengers had to wait 2 hours for a replacement bus. No proper communication from the staff.', 'in progress', '2025-07-22', NULL),

(NULL, 1, NULL, 'Bus toilet was out of order during the entire journey. This caused significant inconvenience for passengers on the 8-hour route.', 'in progress', '2025-07-23', NULL),

-- Resolved incidents (with resolution dates)
(5, 1, 5, 'Overcharging issue - was charged 1500 LKR instead of the standard 1200 LKR fare from Badulla to Matara. Requesting refund.', 'resolved', '2025-07-21', '2025-07-23'),

(1, 1, 6, 'Lost personal bag during the journey. Bag contained important documents and was left under seat A08. Bus staff helped locate and return the bag.', 'resolved', '2025-07-20', '2025-07-22'),

(2, 1, 7, 'Window would not close properly causing rain water to come inside during the journey. Passengers had to change seats.', 'resolved', '2025-07-19', '2025-07-21'),

(NULL, 1, NULL, 'Bus arrived 30 minutes early at Tangalle stop and left without waiting for scheduled time, causing some passengers to miss the bus.', 'resolved', '2025-07-18', '2025-07-20'),

-- More submitted incidents for variety
(6, NULL, 8, 'Rude behavior from the conductor when asked about the next stop. Very unprofessional and discourteous service.', 'submitted', '2025-07-24', NULL),

(3, NULL, NULL, 'Bus was overcrowded with standing passengers blocking the aisle. This is unsafe and uncomfortable for all passengers.', 'submitted', '2025-07-23', NULL),

(NULL, NULL, NULL, 'Music volume was too loud during the night journey making it impossible to sleep. Requests to lower volume were ignored.', 'submitted', '2025-07-23', NULL),

-- Historical incidents (various dates)
(4, 1, NULL, 'Dirty and unhygienic bus interior. Seats had stains and floor was not cleaned properly before departure.', 'resolved', '2025-07-15', '2025-07-17'),

(1, 1, NULL, 'Bus did not stop at Lunugamvehera as scheduled, forcing passengers to get off at the next stop and find alternative transport.', 'resolved', '2025-07-14', '2025-07-16'),

(2, NULL, NULL, 'Smoking passenger in non-smoking bus. Driver did not take action despite complaints from other passengers.', 'in progress', '2025-07-20', NULL),

-- Emergency/Safety incidents
(5, 1, NULL, 'Emergency - Bus tire burst near Dickwella. Driver handled the situation well but replacement bus took too long to arrive.', 'resolved', '2025-07-12', '2025-07-13'),

(NULL, 1, NULL, 'First aid kit was missing from the bus when a passenger needed medical assistance during the journey.', 'in progress', '2025-07-21', NULL),

-- Service quality incidents
(6, NULL, NULL, 'No announcement made about upcoming stops. New passengers and tourists had difficulty knowing when to get off.', 'submitted', '2025-07-24', NULL),

(3, 1, NULL, 'WiFi service advertised but not working during the journey. Passengers rely on this service for work and communication.', 'resolved', '2025-07-17', '2025-07-19'),

-- Recent incidents requiring attention
(1, NULL, NULL, 'Bus departure was delayed by 1 hour due to driver arriving late. No alternative arrangements or communication provided to waiting passengers.', 'submitted', '2025-07-24', NULL);

-- Add some sample feedback data as well (since it is related to incident reporting)
INSERT IGNORE INTO Feedback (UserId, BusId, Comment, Rating, CreatedDate) VALUES
(1, 1, 'Good service overall but could improve punctuality. Driver was professional and helpful.', 4, '2025-07-24'),
(2, 2, 'Excellent journey! Clean bus, comfortable seats, and arrived on time. Highly recommended.', 5, '2025-07-23'),
(3, 1, 'Average experience. Bus was clean but air conditioning could be better during hot weather.', 3, '2025-07-22'),
(4, 3, 'Poor service. Bus was late and overcrowded. Staff was not helpful with passenger concerns.', 2, '2025-07-21'),
(5, 4, 'Very satisfied with the service. Smooth journey and professional staff throughout.', 5, '2025-07-20'),
(6, 2, 'Good value for money. Comfortable seats and reasonable fare. Will use again.', 4, '2025-07-19'),
(1, 5, 'Bus was clean and on time but could use better entertainment system for long journeys.', 4, '2025-07-18'),
(2, 1, 'Outstanding service! Driver was very careful and conductor was helpful. Perfect journey.', 5, '2025-07-17');

-- Summary query to check the inserted data
SELECT 
    'Incident Summary' as DataType,
    Status,
    COUNT(*) as Count,
    MIN(ReportedDate) as EarliestReport,
    MAX(ReportedDate) as LatestReport
FROM Incident 
GROUP BY Status
UNION ALL
SELECT 
    'Total Incidents' as DataType,
    'All' as Status,
    COUNT(*) as Count,
    MIN(ReportedDate) as EarliestReport,
    MAX(ReportedDate) as LatestReport
FROM Incident;

-- Display success message
SELECT 'Lanka Transit sample incident and feedback data inserted successfully!' AS Result,
       'Use this data to test the incident reporting and management functionality' AS Note;
