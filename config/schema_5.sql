-- Lanka Transit Schema 5 (schema_5.sql)
-- This file adds capacity constraints to the Bus table
-- Limits bus seat capacity to only 49 and 54
-- Run this after schema.sql, schema2.sql, schema_3.sql, and schema_4.sql

USE bosennoy016fmb5flv0m;

-- Add constraint to Bus table to limit capacity to only 49 and 54
ALTER TABLE Bus 
ADD CONSTRAINT chk_bus_capacity 
CHECK (Capacity IN (49, 54));

-- Add a comment to the Bus table documenting the capacity constraint
ALTER TABLE Bus 
COMMENT = 'Bus table with capacity limited to standard sizes: 49 seats or 54 seats';

-- Update any existing buses that don't meet the capacity constraint
-- This will set invalid capacities to 49 (default regular bus)
UPDATE Bus 
SET Capacity = 49 
WHERE Capacity NOT IN (49, 54);

-- Add index on capacity for efficient queries
CREATE INDEX idx_bus_capacity ON Bus(Capacity);

-- Insert sample buses with valid capacities for testing
INSERT INTO Bus (RouteId, AdminId, BusNumber, Capacity, LastUpdate) VALUES
(1, 1, 'NB-1001', 49, CURDATE()),
(1, 1, 'NB-1002', 54, CURDATE()),
(2, 1, 'NB-2001', 49, CURDATE()),
(2, 1, 'NB-2002', 54, CURDATE());

-- Create a view for bus capacity statistics
CREATE VIEW BusCapacityStats AS
SELECT 
    Capacity,
    COUNT(*) as BusCount,
    CONCAT(COUNT(*), ' buses with ', Capacity, ' seats') as Description
FROM Bus 
GROUP BY Capacity
ORDER BY Capacity;

-- Comments explaining the capacity choices:
-- 49 seats: Standard bus configuration (2+2 seating, 12 rows + driver area)
-- 54 seats: Luxury bus configuration (2+3 seating, 11 rows + driver area)
-- These are common industry standards for inter-city buses in Sri Lanka
