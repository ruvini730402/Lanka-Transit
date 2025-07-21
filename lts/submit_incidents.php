<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    showToast("Access denied.", "error");
    exit();
}

// DB Connection
$conn = new mysqli('localhost', 'root', '', 'lankatrasit');
if ($conn->connect_error) {
    showToast("❌ Failed to connect to database.", "error");
    exit();
}

// Helper: Sanitize
function clean($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Toast Function
function showToast($message, $type = 'success') {
    $bg = $type === 'success' ? '#4caf50' : '#f44336'; // green or red
    echo <<<HTML
    <div style="
        position: fixed;
        top: 20px;
        right: 20px;
        background-color: $bg;
        color: white;
        padding: 16px 24px;
        border-radius: 8px;
        font-family: sans-serif;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        z-index: 9999;
        animation: fadeInOut 3s forwards;
    ">
        $message
    </div>
    <style>
    @keyframes fadeInOut {
        0% { opacity: 0; transform: translateY(-10px); }
        10% { opacity: 1; transform: translateY(0); }
        90% { opacity: 1; transform: translateY(0); }
        100% { opacity: 0; transform: translateY(-10px); }
    }
    </style>
    <script>
        setTimeout(() => {
            window.location.href = 'Report_Incidents.php';
        }, 3000);
    </script>
    HTML;
    exit();
}

// Collect form values
$name        = clean($_POST['user_name'] ?? 'Anonymous');
$type        = clean($_POST['incident_type'] ?? '');
$datetime    = $_POST['incident_datetime'] ?? '';
$location    = clean($_POST['incident_location'] ?? '');
$bus_number  = clean($_POST['bus_number'] ?? '');
$route       = clean($_POST['route'] ?? '');
$description = clean($_POST['description'] ?? '');

// Validate
if (!$type || !$datetime || !$location || !$bus_number || !$route || !$description) {
    showToast("⚠️ Please fill all required fields.", "error");
}

// Generate Tracking ID
$res = $conn->query("SELECT MAX(id) AS max_id FROM incidents");
$row = $res->fetch_assoc();
$nextId = $row['max_id'] + 1;
$tracking_id = "INC-" . str_pad($nextId, 4, "0", STR_PAD_LEFT);

// File upload
$uploadDir = "uploads/";
$attachment = "";

if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

if (!empty($_FILES['attachment']['name'])) {
    $fileName = basename($_FILES['attachment']['name']);
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'pdf'];

    if (in_array($fileExt, $allowed)) {
        $newFile = $uploadDir . uniqid("file_") . "." . $fileExt;
        if (move_uploaded_file($_FILES['attachment']['tmp_name'], $newFile)) {
            $attachment = $newFile;
        } else {
            showToast("⚠️ File upload failed.", "error");
        }
    } else {
        showToast("❌ Invalid file type. Only JPG, PNG, PDF allowed.", "error");
    }
}

// Insert into DB
$stmt = $conn->prepare("INSERT INTO incidents (tracking_id, user_name, incident_type, incident_datetime, location, bus_number, route, description, attachment, status)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')");
$stmt->bind_param("sssssssss", $tracking_id, $name, $type, $datetime, $location, $bus_number, $route, $description, $attachment);

if ($stmt->execute()) {
    showToast("✅ Incident submitted! Tracking ID: $tracking_id", "success");
} else {
    showToast("❌ Database error while saving incident.", "error");
}

$stmt->close();
$conn->close();
?>
