-- Sample data for testing Lanka Transit application
USE transit;

-- Insert sample buses
INSERT INTO Bus (RouteId, AdminId, BusNumber, Capacity, LastUpdate) VALUES
(1, 1, 'NB-1234', 45, '2024-07-19'),
(1, 1, 'NB-5678', 50, '2024-07-19'),
(2, 1, 'NB-9012', 40, '2024-07-19'),
(2, 1, 'NB-3456', 45, '2024-07-19');
-- Insert sample schedules for today and tomorrow
INSERT INTO Schedule (BusID, DepartureTime, ArrivalTime, Fare) VALUES
-- Colombo to Kandy buses
(1, '2024-07-19 06:00:00', '2024-07-19 09:30:00', 450.00),
(1, '2024-07-19 14:00:00', '2024-07-19 17:30:00', 450.00),
(1, '2024-07-20 06:00:00', '2024-07-20 09:30:00', 450.00),
(1, '2024-07-20 14:00:00', '2024-07-20 17:30:00', 450.00),

(2, '2024-07-19 08:00:00', '2024-07-19 11:30:00', 500.00),
(2, '2024-07-19 16:00:00', '2024-07-19 19:30:00', 500.00),
(2, '2024-07-20 08:00:00', '2024-07-20 11:30:00', 500.00),
(2, '2024-07-20 16:00:00', '2024-07-20 19:30:00', 500.00),

-- Colombo to Galle buses
(3, '2024-07-19 07:00:00', '2024-07-19 10:00:00', 350.00),
(3, '2024-07-19 15:00:00', '2024-07-19 18:00:00', 350.00),
(3, '2024-07-20 07:00:00', '2024-07-20 10:00:00', 350.00),
(3, '2024-07-20 15:00:00', '2024-07-20 18:00:00', 350.00),

(4, '2024-07-19 09:00:00', '2024-07-19 12:00:00', 380.00),
(4, '2024-07-19 17:00:00', '2024-07-19 20:00:00', 380.00),
(4, '2024-07-20 09:00:00', '2024-07-20 12:00:00', 380.00),
(4, '2024-07-20 17:00:00', '2024-07-20 20:00:00', 380.00);



-- Generate seats for all buses
-- Bus 1 (45 seats)
INSERT INTO Seat (BusID, SeatNumber, Status, GenderPreference, IsLadySeat) VALUES
(1, 'A1', 'available', 'male', FALSE),
(1, 'A2', 'available', 'female', TRUE),
(1, 'A3', 'available', 'male', FALSE),
(1, 'A4', 'available', 'female', TRUE),
(1, 'B1', 'available', 'male', FALSE),
(1, 'B2', 'available', 'female', TRUE),
(1, 'B3', 'available', 'male', FALSE),
(1, 'B4', 'available', 'female', TRUE),
(1, 'C1', 'available', 'other', FALSE),
(1, 'C2', 'available', 'other', FALSE),
(1, 'C3', 'available', 'other', FALSE),
(1, 'C4', 'available', 'other', FALSE),
(1, 'D1', 'available', 'other', FALSE),
(1, 'D2', 'available', 'other', FALSE),
(1, 'D3', 'available', 'other', FALSE),
(1, 'D4', 'available', 'other', FALSE),
(1, 'E1', 'available', 'other', FALSE),
(1, 'E2', 'available', 'other', FALSE),
(1, 'E3', 'available', 'other', FALSE),
(1, 'E4', 'available', 'other', FALSE),
(1, 'F1', 'available', 'other', FALSE),
(1, 'F2', 'available', 'other', FALSE),
(1, 'F3', 'available', 'other', FALSE),
(1, 'F4', 'available', 'other', FALSE),
(1, 'G1', 'available', 'other', FALSE),
(1, 'G2', 'available', 'other', FALSE),
(1, 'G3', 'available', 'other', FALSE),
(1, 'G4', 'available', 'other', FALSE),
(1, 'H1', 'available', 'other', FALSE),
(1, 'H2', 'available', 'other', FALSE),
(1, 'H3', 'available', 'other', FALSE),
(1, 'H4', 'available', 'other', FALSE),
(1, 'I1', 'available', 'other', FALSE),
(1, 'I2', 'available', 'other', FALSE),
(1, 'I3', 'available', 'other', FALSE),
(1, 'I4', 'available', 'other', FALSE),
(1, 'J1', 'available', 'other', FALSE),
(1, 'J2', 'available', 'other', FALSE),
(1, 'J3', 'available', 'other', FALSE),
(1, 'J4', 'available', 'other', FALSE),
(1, 'K1', 'available', 'other', FALSE),
(1, 'K2', 'available', 'other', FALSE),
(1, 'K3', 'available', 'other', FALSE),
(1, 'K4', 'available', 'other', FALSE),
(1, 'L1', 'available', 'other', FALSE);

-- Bus 2 (50 seats) - simplified generation
INSERT INTO Seat (BusID, SeatNumber, Status, GenderPreference, IsLadySeat)
SELECT 2, CONCAT(CHAR(65 + (n DIV 4)), (n MOD 4) + 1), 'available', 
       CASE WHEN n < 8 AND n MOD 2 = 1 THEN 'female' 
            WHEN n < 8 AND n MOD 2 = 0 THEN 'male' 
            ELSE 'other' END,
       CASE WHEN n < 8 AND n MOD 2 = 1 THEN TRUE ELSE FALSE END
FROM (SELECT 0 n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10 UNION SELECT 11 UNION SELECT 12 UNION SELECT 13 UNION SELECT 14 UNION SELECT 15 UNION SELECT 16 UNION SELECT 17 UNION SELECT 18 UNION SELECT 19 UNION SELECT 20 UNION SELECT 21 UNION SELECT 22 UNION SELECT 23 UNION SELECT 24 UNION SELECT 25 UNION SELECT 26 UNION SELECT 27 UNION SELECT 28 UNION SELECT 29 UNION SELECT 30 UNION SELECT 31 UNION SELECT 32 UNION SELECT 33 UNION SELECT 34 UNION SELECT 35 UNION SELECT 36 UNION SELECT 37 UNION SELECT 38 UNION SELECT 39 UNION SELECT 40 UNION SELECT 41 UNION SELECT 42 UNION SELECT 43 UNION SELECT 44 UNION SELECT 45 UNION SELECT 46 UNION SELECT 47 UNION SELECT 48 UNION SELECT 49) numbers;

-- Bus 3 (40 seats)
INSERT INTO Seat (BusID, SeatNumber, Status, GenderPreference, IsLadySeat)
SELECT 3, CONCAT(CHAR(65 + (n DIV 4)), (n MOD 4) + 1), 'available', 
       CASE WHEN n < 8 AND n MOD 2 = 1 THEN 'female' 
            WHEN n < 8 AND n MOD 2 = 0 THEN 'male' 
            ELSE 'other' END,
       CASE WHEN n < 8 AND n MOD 2 = 1 THEN TRUE ELSE FALSE END
FROM (SELECT 0 n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10 UNION SELECT 11 UNION SELECT 12 UNION SELECT 13 UNION SELECT 14 UNION SELECT 15 UNION SELECT 16 UNION SELECT 17 UNION SELECT 18 UNION SELECT 19 UNION SELECT 20 UNION SELECT 21 UNION SELECT 22 UNION SELECT 23 UNION SELECT 24 UNION SELECT 25 UNION SELECT 26 UNION SELECT 27 UNION SELECT 28 UNION SELECT 29 UNION SELECT 30 UNION SELECT 31 UNION SELECT 32 UNION SELECT 33 UNION SELECT 34 UNION SELECT 35 UNION SELECT 36 UNION SELECT 37 UNION SELECT 38 UNION SELECT 39) numbers;

-- Bus 4 (45 seats)
INSERT INTO Seat (BusID, SeatNumber, Status, GenderPreference, IsLadySeat)
SELECT 4, CONCAT(CHAR(65 + (n DIV 4)), (n MOD 4) + 1), 'available', 
       CASE WHEN n < 8 AND n MOD 2 = 1 THEN 'female' 
            WHEN n < 8 AND n MOD 2 = 0 THEN 'male' 
            ELSE 'other' END,
       CASE WHEN n < 8 AND n MOD 2 = 1 THEN TRUE ELSE FALSE END
FROM (SELECT 0 n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10 UNION SELECT 11 UNION SELECT 12 UNION SELECT 13 UNION SELECT 14 UNION SELECT 15 UNION SELECT 16 UNION SELECT 17 UNION SELECT 18 UNION SELECT 19 UNION SELECT 20 UNION SELECT 21 UNION SELECT 22 UNION SELECT 23 UNION SELECT 24 UNION SELECT 25 UNION SELECT 26 UNION SELECT 27 UNION SELECT 28 UNION SELECT 29 UNION SELECT 30 UNION SELECT 31 UNION SELECT 32 UNION SELECT 33 UNION SELECT 34 UNION SELECT 35 UNION SELECT 36 UNION SELECT 37 UNION SELECT 38 UNION SELECT 39 UNION SELECT 40 UNION SELECT 41 UNION SELECT 42 UNION SELECT 43 UNION SELECT 44) numbers;



-- Add some sample bookings to test availability
INSERT INTO Booking (UserId, BusID, SeatNumber, BookingTime, Status, PhoneNumber, Fare) VALUES
(NULL, 1, 'A1', '2024-07-19 10:00:00', 'confirmed', '0771234567', 450.00),
(NULL, 1, 'A3', '2024-07-19 10:05:00', 'confirmed', '0779876543', 450.00),
(NULL, 2, 'B1', '2024-07-19 11:00:00', 'confirmed', '0765432109', 350.00);