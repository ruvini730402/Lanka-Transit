<?php
/**
 * Simple Booking Form - Demo for booking confirmation and PDF ticket
 */
// Set demo data for quick booking
$demoRoutes = [
    ['origin' => 'Badulla', 'destination' => 'Matara'],
    ['origin' => 'Matara', 'destination' => 'Badulla']
];

$demoBuses = [
    ['id' => 1, 'number' => 'NB-1001', 'fare' => 1500.00],
    ['id' => 2, 'number' => 'NB-1002', 'fare' => 1200.00],
    ['id' => 3, 'number' => 'NB-1003', 'fare' => 1800.00]
];

$demoSeats = ['A1', 'A2', 'A3', 'A4', 'B1', 'B2', 'B3', 'B4'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Booking Form - Lanka Transit</title>
</head>
<body>
    <h1>Demo Booking Form</h1>
    <p><em>Temporary demo form for testing booking confirmation and PDF generation</em></p>
    
    <form method="POST" action="confirmation.php">
        <h3>Passenger Details</h3>
        
        <label for="passenger_name">Full Name:</label><br>
        <input type="text" id="passenger_name" name="passenger_name" required><br><br>
        
        <label for="phone">Phone Number:</label><br>
        <input type="tel" id="phone" name="phone" required pattern="[0-9]{10}"><br><br>
        
        <h3>Travel Details</h3>
        
        <label for="origin">From:</label><br>
        <select id="origin" name="origin" required>
            <option value="">Select Origin</option>
            <?php foreach ($demoRoutes as $route): ?>
                <option value="<?= $route['origin'] ?>"><?= $route['origin'] ?></option>
            <?php endforeach; ?>
        </select><br><br>
        
        <label for="destination">To:</label><br>
        <select id="destination" name="destination" required>
            <option value="">Select Destination</option>
            <?php foreach ($demoRoutes as $route): ?>
                <option value="<?= $route['destination'] ?>"><?= $route['destination'] ?></option>
            <?php endforeach; ?>
        </select><br><br>
        
        <label for="travel_date">Travel Date:</label><br>
        <input type="date" id="travel_date" name="travel_date" required 
               min="<?= date('Y-m-d') ?>" 
               value="<?= date('Y-m-d', strtotime('+1 day')) ?>"><br><br>
        
        <label for="bus_id">Select Bus:</label><br>
        <select id="bus_id" name="bus_id" required>
            <option value="">Choose Bus</option>
            <?php foreach ($demoBuses as $bus): ?>
                <option value="<?= $bus['id'] ?>" data-fare="<?= $bus['fare'] ?>">
                    <?= $bus['number'] ?> - Rs. <?= number_format($bus['fare'], 2) ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>
        
        <label for="seat_number">Seat Number:</label><br>
        <select id="seat_number" name="seat_number" required>
            <option value="">Choose Seat</option>
            <?php foreach ($demoSeats as $seat): ?>
                <option value="<?= $seat ?>"><?= $seat ?></option>
            <?php endforeach; ?>
        </select><br><br>
        
        <input type="hidden" id="fare" name="fare" value="">
        <input type="hidden" name="demo_mode" value="1">
        
        <button type="submit">Book Ticket</button>
    </form>
    
    <hr>
    <h4>Quick Demo Fill</h4>
    <button type="button" onclick="fillDemoData()">Auto-fill Demo Data</button>
    
    <script>
        // Auto-fill demo data
        function fillDemoData() {
            document.getElementById('passenger_name').value = 'John Doe';
            document.getElementById('phone').value = '0771234567';
            document.getElementById('origin').value = 'Badulla';
            document.getElementById('destination').value = 'Matara';
            document.getElementById('bus_id').value = '1';
            document.getElementById('seat_number').value = 'A1';
            document.getElementById('fare').value = '1500.00';
        }
        
        // Update fare when bus is selected
        document.getElementById('bus_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.dataset.fare) {
                document.getElementById('fare').value = selectedOption.dataset.fare;
            }
        });
        
        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const origin = document.getElementById('origin').value;
            const destination = document.getElementById('destination').value;
            
            if (origin === destination && origin !== '') {
                e.preventDefault();
                alert('Origin and destination cannot be the same!');
                return false;
            }
            
            if (!document.getElementById('fare').value) {
                e.preventDefault();
                alert('Please select a bus to continue!');
                return false;
            }
        });
    </script>
</body>
</html>
