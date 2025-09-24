-- Lanka Transit Database Schema 6
-- Add BookingId column to Feedback table with foreign key reference

USE bosennoy016fmb5flv0m;

-- Add BookingId column to Feedback table
ALTER TABLE Feedback 
ADD COLUMN BookingId INT NULL 
AFTER BusId;

-- Add foreign key constraint to reference Booking table
ALTER TABLE Feedback 
ADD CONSTRAINT fk_feedback_booking 
FOREIGN KEY (BookingId) REFERENCES Booking(ID) 
ON DELETE SET NULL 
ON UPDATE CASCADE;

-- Add index for better query performance
CREATE INDEX idx_feedback_booking ON Feedback(BookingId);
