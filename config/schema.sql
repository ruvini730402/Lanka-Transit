-- Lanka Transit Database Schema
-- Based on ER diagram requirements

CREATE DATABASE IF NOT EXISTS bosennoy016fmb5flv0m;
USE bosennoy016fmb5flv0m;

-- User table
CREATE TABLE User (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    Name VARCHAR(100) NOT NULL,
    Email VARCHAR(60) NOT NULL UNIQUE,
    PasswordHash VARCHAR(255) NOT NULL,
    PhoneNumber VARCHAR(10) NOT NULL,
    Role ENUM('administrator','guest user','registered user') NOT NULL DEFAULT 'registered user',
    RegistrationDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Admin table
CREATE TABLE Admin (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    Email VARCHAR(60) NOT NULL UNIQUE,
    PasswordHash VARCHAR(255) NOT NULL,
    Name VARCHAR(100) NOT NULL,
    PhoneNumber VARCHAR(10) NOT NULL
);

-- Location table
CREATE TABLE Location (
    UniqueID INT PRIMARY KEY AUTO_INCREMENT,
    Name VARCHAR(100) NOT NULL,
    Type ENUM('terminal','stop') NOT NULL
);

-- Route table
CREATE TABLE Route (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    Origin VARCHAR(50) NOT NULL,
    Destination VARCHAR(50) NOT NULL,
    Stops TEXT
);

-- Bus table
CREATE TABLE Bus (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    RouteId INT,
    AdminId INT,
    BusNumber VARCHAR(7) NOT NULL UNIQUE,
    Capacity INT NOT NULL,
    LastUpdate DATE,
    FOREIGN KEY (RouteId) REFERENCES Route(ID) ON DELETE SET NULL,
    FOREIGN KEY (AdminId) REFERENCES Admin(ID) ON DELETE SET NULL
);

-- Schedule table
CREATE TABLE Schedule (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    BusID INT,
    DepartureTime DATETIME NOT NULL,
    ArrivalTime DATETIME NOT NULL,
    Fare DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (BusID) REFERENCES Bus(ID) ON DELETE CASCADE
);

-- Seat table
CREATE TABLE Seat (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    BusID INT,
    SeatNumber VARCHAR(6) NOT NULL,
    Status ENUM('available','booked') DEFAULT 'available',
    GenderPreference ENUM('male','female','other') NOT NULL,
    IsLadySeat BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (BusID) REFERENCES Bus(ID) ON DELETE CASCADE,
    UNIQUE KEY unique_seat_per_bus (BusID, SeatNumber)
);

-- Booking table
CREATE TABLE Booking (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    UserId INT,
    BusID INT,
    SeatNumber VARCHAR(6) NOT NULL,
    BookingTime TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Status ENUM('confirmed','cancelled','completed') NOT NULL DEFAULT 'confirmed',
    PhoneNumber VARCHAR(10) NOT NULL,
    Fare DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (UserId) REFERENCES User(ID) ON DELETE SET NULL,
    FOREIGN KEY (BusID) REFERENCES Bus(ID) ON DELETE CASCADE
);

-- Payment table
CREATE TABLE Payment (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    BookingId INT NOT NULL,
    PaymentMethod VARCHAR(50),
    Status ENUM('success','failed') NOT NULL,
    Amount DECIMAL(10,2) NOT NULL,
    PaymentDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    TransactionId VARCHAR(100),
    FOREIGN KEY (BookingId) REFERENCES Booking(ID) ON DELETE CASCADE
);

-- Receipt table
CREATE TABLE Receipt (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    PaymentId INT NOT NULL,
    PdfUrl VARCHAR(255) NOT NULL,
    GeneratedDate DATE DEFAULT (CURRENT_DATE),
    FOREIGN KEY (PaymentId) REFERENCES Payment(ID) ON DELETE CASCADE
);

-- Feedback table
CREATE TABLE Feedback (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    UserId INT,
    BusId INT,
    Comment TEXT,
    Rating INT CHECK (Rating >= 1 AND Rating <= 5),
    CreatedDate DATE DEFAULT (CURRENT_DATE),
    FOREIGN KEY (UserId) REFERENCES User(ID) ON DELETE SET NULL,
    FOREIGN KEY (BusId) REFERENCES Bus(ID) ON DELETE CASCADE
);

-- Incident table
CREATE TABLE Incident (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    UserId INT,
    AdminId INT,
    BookingId INT,
    Description TEXT,
    Status ENUM('submitted','in progress','resolved') NOT NULL DEFAULT 'submitted',
    ReportedDate DATE DEFAULT (CURRENT_DATE),
    ResolvedDate DATE,
    FOREIGN KEY (UserId) REFERENCES User(ID) ON DELETE SET NULL,
    FOREIGN KEY (AdminId) REFERENCES Admin(ID) ON DELETE SET NULL,
    FOREIGN KEY (BookingId) REFERENCES Booking(ID) ON DELETE SET NULL
);

-- Insert sample locations
INSERT INTO Location (Name, Type) VALUES
('Badulla', 'terminal'),
('Ella', 'stop'),
('Wellawaya', 'stop'),
('Thanamalvila', 'stop'),
('Lunugamvehera', 'stop'),
('Tangalle', 'stop'),
('Dickwella', 'stop'),
('Devinuwara', 'stop'),
('Matara', 'terminal');


-- Insert sample routes
INSERT INTO Route (Origin, Destination, Stops) VALUES
('Badulla', 'Matara', 'Ella,Wellawaya,Thanamalvila,Lunugamvehera,Tangalle,Dickwella,Devinuwara'),
('Matara', 'Badulla', 'Devinuwara,Dickwella,Tangalle,Lunugamvehera,Thanamalvila,Wellawaya,Ella');

-- Insert sample admin
INSERT INTO Admin (Email, PasswordHash, Name, PhoneNumber) VALUES
('admin@lankatransit.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', '0771234567');

-- Create indexes for better performance
CREATE INDEX idx_booking_user ON Booking(UserId);
CREATE INDEX idx_booking_bus ON Booking(BusID);
CREATE INDEX idx_booking_phone ON Booking(PhoneNumber);
CREATE INDEX idx_schedule_bus ON Schedule(BusID);
CREATE INDEX idx_schedule_departure ON Schedule(DepartureTime);
CREATE INDEX idx_seat_bus ON Seat(BusID);
CREATE INDEX idx_payment_booking ON Payment(BookingId);
CREATE INDEX idx_route_origin_dest ON Route(Origin, Destination);