<?php
class Booking {
    private $db;
    private $data;

    public function __construct($postData) {
        $this->data = $postData;
        $this->connectDB();
    }

    private function connectDB() {
        try {
            $this->db = new PDO("mysql:host=localhost;dbname=busbooking", "root", "");
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            $this->respond(false, "Database connection failed: " . $e->getMessage());
            exit;
        }
    }

    public function processBooking() {
        if (!$this->validateInput()) return;

        if ($this->isSeatBooked()) {
            $this->respond(false, "Seat already booked.");
            return;
        }

        if ($this->isLadySeatRestricted()) {
            $this->respond(false, "This is a Lady Seat. Only females can book it.");
            return;
        }

        $this->saveBooking();
    }

    private function validateInput() {
        $seat = (int) $this->data['seat'];
        $phone = trim($this->data['phone']);
        $name = trim($this->data['name'] ?? '');
        $nic = trim($this->data['nic'] ?? '');
        $gender = $this->data['gender'];

        if (empty($name)) {
            $this->respond(false, "Name is required.");
            return false;
        }

        if (!preg_match('/^\d{10}$/', $phone)) {
            $this->respond(false, "Phone number must be exactly 10 digits.");
            return false;
        }

        if (!empty($nic) && !$this->isValidNIC($nic)) {
            $this->respond(false, "Invalid Sri Lankan NIC.");
            return false;
        }

        if (!in_array($gender, ['male', 'female', 'undisclosed'])) {
            $this->respond(false, "Invalid gender selection.");
            return false;
        }

        return true;
    }

    private function isValidNIC($nic) {
        return preg_match('/^(\d{9}[vVxX]|\d{12})$/', $nic);
    }

    private function isSeatBooked() {
        $stmt = $this->db->prepare("SELECT * FROM bookings WHERE seat = ?");
        $stmt->execute([$this->data['seat']]);
        return $stmt->rowCount() > 0;
    }

    private function isLadySeatRestricted() {
        $seat = (int) $this->data['seat'];
        $gender = $this->data['gender'];
        return $seat <= 8 && $gender !== 'female';
    }
private function saveBooking() {
    $booking_id = uniqid("BID");
    $stmt = $this->db->prepare("INSERT INTO bookings (booking_id, seat, phone, name, nic, gender) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $booking_id,
        $this->data['seat'],
        $this->data['phone'],
        $this->data['name'],
        $this->data['nic'] ?? '',
        $this->data['gender']
    ]);

    // Send JSON response including booking_id
    $this->respond(true, "Seat booked successfully!", $booking_id);
}
private function respond($success, $message, $booking_id = null) {
    $response = ["success" => $success, "message" => $message];
    if ($booking_id !== null) {
        $response["booking_id"] = $booking_id;
    }
    echo json_encode($response);
}

   
}




