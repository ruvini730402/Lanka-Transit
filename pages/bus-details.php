<!-- bus-details.php -->
<?php
require_once '../config/database.php';
?>

<form method="POST">
  <label>Booking ID or Phone Number: <input type="text" name="identifier" required></label>
  <button type="submit">View Booking</button>
</form>

<?php
if ($_POST) {
    $id = $_POST['identifier'];
    $database = new Database();
    $db = $database->getConnection();
    
    if ($db) {
        $stmt = $db->prepare("SELECT * FROM bookings WHERE booking_id = ? OR phone = ?");
        $stmt->execute([$id, $id]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($results) {
            foreach ($results as $row) {
                echo "<p>Seat: {$row['seat']}, Gender: {$row['gender']}, Booking ID: {$row['booking_id']}</p>";
            }
        } else {
            echo "<p>No booking found.</p>";
        }
        $database->closeConnection();
    } else {
        echo "<p>Database connection failed.</p>";
    }
}
?>