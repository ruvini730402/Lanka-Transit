<?php
require_once __DIR__ . '/../../classes/Database.php';
include('Feedback.php');

// Initialize variables
$feedback_data = null;
$error_message = '';
$users = [];
$buses = [];
$bookings = [];

try {
    $database = new Database();
    $connection = $database->getConnection();
    $feedbackObj = new Feedback($connection);
    
    // Get data for dropdowns
    $users = $feedbackObj->getAllUsers();
    $buses = $feedbackObj->getAllBuses();
    $bookings = $feedbackObj->getAllBookings();
    
    // Get feedback data for editing
    if (isset($_GET['id'])) {
        $feedback_data = $feedbackObj->getById($_GET['id']);
        
        if (!$feedback_data) {
            header("Location: ../feedback.php?msg=Feedback not found");
            exit();
        }
    }
} catch (PDOException $e) {
    $error_message = "Error fetching data: " . $e->getMessage();
}

// Handle form submission
if (isset($_POST['update_feedback']) && isset($_POST['id'])) {
    $id = $_POST['id'];
    $userId = !empty($_POST['user_id']) ? $_POST['user_id'] : null;
    $busId = !empty($_POST['bus_id']) ? $_POST['bus_id'] : null;
    $bookingId = !empty($_POST['booking_id']) ? $_POST['booking_id'] : null;
    $comment = $_POST['comment'];
    $rating = $_POST['rating'];

    try {
        if ($feedbackObj->update($id, $userId, $busId, $bookingId, $comment, $rating)) {
            header("Location: ../feedback.php?msg=Feedback updated successfully");
            exit();
        } else {
            $error_message = "Failed to update feedback";
        }
    } catch (PDOException $e) {
        $error_message = "Error updating feedback: " . $e->getMessage();
    }
}

// If no feedback data and no error, redirect back
if (!$feedback_data && !$error_message) {
    header("Location: ../feedback.php?msg=Invalid feedback ID");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Feedback</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="container mt-4">
    <!-- Back Button -->
    <a href="../feedback.php" class="btn btn-maroon-outline back-btn">&larr; Back to Feedback</a>
    
    <h2 class="mb-4">Update Feedback</h2>

    <!-- Error Message -->
    <?php if ($error_message): ?>
        <div class="alert alert-danger" role="alert">
            <?= htmlspecialchars($error_message) ?>
        </div>
    <?php endif; ?>

    <?php if ($feedback_data): ?>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <form method="POST" action="">
                <input type="hidden" name="id" value="<?= htmlspecialchars($feedback_data['ID']) ?>">

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="user_id" class="form-label">User (Optional)</label>
                            <select class="form-select" id="user_id" name="user_id">
                                <option value="">Anonymous User</option>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?= htmlspecialchars($user['ID']) ?>" 
                                            <?= ($feedback_data['UserId'] == $user['ID']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($user['Name']) ?> (<?= htmlspecialchars($user['Email']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="bus_id" class="form-label">Bus (Optional)</label>
                            <select class="form-select" id="bus_id" name="bus_id">
                                <option value="">No specific bus</option>
                                <?php foreach ($buses as $bus): ?>
                                    <option value="<?= htmlspecialchars($bus['ID']) ?>" 
                                            <?= ($feedback_data['BusId'] == $bus['ID']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($bus['BusNumber']) ?> - <?= htmlspecialchars($bus['Origin']) ?> → <?= htmlspecialchars($bus['Destination']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="booking_id" class="form-label">Related Booking (Optional)</label>
                            <select class="form-select" id="booking_id" name="booking_id">
                                <option value="">No specific booking</option>
                                <?php foreach ($bookings as $booking): ?>
                                    <option value="<?= htmlspecialchars($booking['ID']) ?>" 
                                            <?= ($feedback_data['BookingId'] == $booking['ID']) ? 'selected' : '' ?>>
                                        Booking #<?= htmlspecialchars($booking['ID']) ?> - 
                                        <?= htmlspecialchars($booking['BusNumber']) ?> 
                                        (<?= htmlspecialchars($booking['UserName'] ?: 'Walk-in') ?> - Seat <?= htmlspecialchars($booking['SeatNumber']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="rating" class="form-label">Rating</label>
                            <select class="form-select" id="rating" name="rating" required>
                                <option value="">Select rating...</option>
                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                    <option value="<?= $i ?>" <?= ($feedback_data['Rating'] == $i) ? 'selected' : '' ?>>
                                        <?= $i ?> Star<?= $i > 1 ? 's' : '' ?> <?= str_repeat('⭐', $i) ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="comment" class="form-label">Comment</label>
                    <textarea id="comment" 
                              name="comment" 
                              class="form-control" 
                              rows="4" 
                              maxlength="1000"
                              placeholder="Enter feedback comment..."><?= htmlspecialchars($feedback_data['Comment']) ?></textarea>
                    <div class="form-text">Maximum 1000 characters</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Created Date</label>
                    <input type="text" 
                           class="form-control" 
                           value="<?= htmlspecialchars($feedback_data['CreatedDate']) ?>" 
                           readonly>
                    <div class="form-text">Creation date cannot be changed</div>
                </div>

                <button type="submit" name="update_feedback" class="btn btn-maroon w-100">Update Feedback</button>

            </form>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const rating = document.getElementById('rating').value;
    const comment = document.getElementById('comment').value.trim();
    
    if (!rating) {
        e.preventDefault();
        alert('Please select a rating.');
        return false;
    }
    
    if (comment.length > 1000) {
        e.preventDefault();
        alert('Comment must be 1000 characters or less.');
        return false;
    }
});
</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>