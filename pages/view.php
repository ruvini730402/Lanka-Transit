<!-- view.php -->
<form method="POST">
  <label>Booking ID or Phone Number: <input type="text" name="identifier" required></label>
  <button type="submit">View Booking</button>
</form>

<?php
if ($_POST) {
    $id = $_POST['identifier'];
    $db = new PDO("mysql:host=localhost;dbname=busbooking", "root", "");
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
}
?>
