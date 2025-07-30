-- Lanka Transit Schema 3 (schema_3.sql)
-- This file adds route_id to the existing Incident table and provides comprehensive sample data
-- Run this after schema.sql and schema2.sql

USE bosennoy016fmb5flv0m;

-- Add route_id column to existing Incident table
ALTER TABLE Incident 
ADD COLUMN RouteId INT,
ADD FOREIGN KEY (RouteId) REFERENCES Route(ID) ON DELETE SET NULL;

-- Add index for the new RouteId column for better performance
CREATE INDEX idx_incident_route ON Incident(RouteId);

-- Update existing incidents from complete_schema_with_data.sql to assign appropriate RouteId values
-- Based on incident descriptions and route context

-- Route 1: Badulla to Matara incidents
UPDATE Incident SET RouteId = 1 WHERE ID IN (
    1,  -- 'Bus was 45 minutes late due to heavy traffic near Wellawaya' (Wellawaya is on Badulla-Matara route)
    2,  -- 'Air conditioning was not working during the journey from Badulla to Matara'
    3,  -- 'Driver was speeding dangerously on mountain roads between Badulla and Bandarawela' (Badulla direction)
    5,  -- 'Bus broke down for 2 hours near Embilipitiya' (on Badulla-Matara route)
    9,  -- 'Excessive noise from engine throughout the journey from Badulla to Matara'
    16, -- 'Bus breakdown due to tire puncture' (general route issue)
    21, -- 'Driver exceeded speed limits on highway sections' (highway sections on main route)
    24, -- 'Bus arrived 2 hours late causing passengers to miss connecting transport'
    27, -- 'Bus ventilation system not working' (during hot afternoon on main route)
    29  -- 'Bus music/entertainment system too loud' (general route issue)
) AND RouteId IS NULL;

-- Route 2: Matara to Badulla incidents  
UPDATE Incident SET RouteId = 2 WHERE ID IN (
    4,  -- 'Seat was broken and uncomfortable throughout the 8-hour journey' (return journey context)
    6,  -- 'Overcharging incident - conductor tried to charge extra for luggage'
    7,  -- 'Unhygienic restroom facilities at Wellawaya rest stop' (Wellawaya on return route)
    8,  -- 'Bus departed 10 minutes early without waiting for booked passengers'
    10, -- 'Driver was using mobile phone while driving'
    12, -- 'Conductor was rude and unprofessional when passengers asked about arrival time'
    14, -- 'Overbooked bus - more passengers than seats available'
    18, -- 'Driver took unauthorized detour adding 1 hour to journey time'
    22, -- 'Bus heating system malfunctioned during mountain section' (mountain section on return)
    26  -- 'Driver refused to stop at scheduled rest area despite passenger requests'
) AND RouteId IS NULL;

-- General incidents that could apply to either route (assign to Route 1 as primary route)
UPDATE Incident SET RouteId = 1 WHERE ID IN (
    11, -- 'Bus toilet facilities were out of order for entire journey'
    13, -- 'Bus seats were dirty and not properly cleaned'
    15, -- 'No announcement about meal stops'
    17, -- 'WiFi service advertised but not working during entire journey'
    19, -- 'Bus luggage compartment lock was broken'
    20, -- 'No working charging ports despite being advertised as available'
    23, -- 'Conductor lost passenger ticket and demanded second payment'
    25, -- 'Food service on bus was poor quality and overpriced'
    28  -- 'Seat reclining mechanism broken'
) AND RouteId IS NULL;