-- Schema Update 7: Add travel_date, origin, destination to Booking table
-- This update adds direct storage of route and date information to bookings for easier retrieval

ALTER TABLE Booking 
ADD COLUMN TravelDate DATE NULL AFTER Status,
ADD COLUMN Origin VARCHAR(50) NULL AFTER TravelDate,
ADD COLUMN Destination VARCHAR(50) NULL AFTER Origin;

-- Create index for better performance on route searches
CREATE INDEX idx_booking_route ON Booking(Origin, Destination, TravelDate);
CREATE INDEX idx_booking_travel_date ON Booking(TravelDate);