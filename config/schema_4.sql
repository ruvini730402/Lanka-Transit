-- Lanka Transit Schema 4 (schema_4.sql)
-- This file adds the BookingCancellation table for handling booking cancellation requests
-- Run this after schema.sql, schema2.sql, and schema_3.sql

USE bosennoy016fmb5flv0m;

-- BookingCancellation table for managing cancellation requests
CREATE TABLE BookingCancellation (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    BookingID INT NOT NULL,
    UserID INT,
    CancellationReason TEXT NOT NULL,
    RequestedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Status ENUM('pending','refunded','declined') NOT NULL DEFAULT 'pending',
    ProcessedBy INT,
    ProcessedAt TIMESTAMP NULL,
    FOREIGN KEY (BookingID) REFERENCES Booking(ID) ON DELETE CASCADE,
    FOREIGN KEY (UserID) REFERENCES User(ID) ON DELETE SET NULL,
    FOREIGN KEY (ProcessedBy) REFERENCES Admin(ID) ON DELETE SET NULL
);

-- Create indexes for better performance
CREATE INDEX idx_booking_cancellation_booking ON BookingCancellation(BookingID);
CREATE INDEX idx_booking_cancellation_user ON BookingCancellation(UserID);
CREATE INDEX idx_booking_cancellation_status ON BookingCancellation(Status);
CREATE INDEX idx_booking_cancellation_requested_at ON BookingCancellation(RequestedAt);
CREATE INDEX idx_booking_cancellation_processed_by ON BookingCancellation(ProcessedBy);

-- Insert sample cancellation data for testing
INSERT INTO BookingCancellation (BookingID, UserID, CancellationReason, Status) VALUES
(1, 1, 'Change of travel plans due to emergency', 'pending'),
(2, NULL, 'Medical emergency - unable to travel', 'refunded'),
(3, 1, 'Double booking mistake', 'declined');

-- Update sample cancellation with processing information
UPDATE BookingCancellation 
SET ProcessedBy = 1, ProcessedAt = NOW(), Status = 'refunded' 
WHERE ID = 2;

UPDATE BookingCancellation 
SET ProcessedBy = 1, ProcessedAt = NOW(), Status = 'declined' 
WHERE ID = 3;
