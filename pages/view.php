<!-- view.php -->
<form method="POST">
  <label>Booking ID or Phone Number: <input type="text" name="identifier" required></label>
  <button type="submit">View Booking</button>
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../classes/Database.php'; // Adjust this path if needed

    $id = trim($_POST['identifier']);

    $database = new Database();
    $db = $database->getConnection();

    if ($db === null) {
        echo "<p>Database connection failed.</p>";
        exit;
    }

    try {
        $stmt = $db->prepare("SELECT * FROM Booking WHERE booking_id = ? OR phone = ?");
        $stmt->execute([$id, $id]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($results) {
            foreach ($results as $row) {
                echo "<p>
                    <strong>Seat:</strong> {$row['seat']}<br>
                    <strong>Name:</strong> {$row['name']}<br>
                    <strong>Gender:</strong> {$row['gender']}<br>
                    <strong>Phone:</strong> {$row['phone']}<br>
                    <strong>NIC:</strong> {$row['nic']}<br>
                    <strong>Booking ID:</strong> {$row['booking_id']}
                </p><hr>";
            }
        } else {
            echo "<p>No booking found.</p>";
        }
    } catch (PDOException $e) {
        echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
?>



