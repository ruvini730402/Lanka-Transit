<?php
require_once __DIR__ . '/../classes/Database.php';
include('php/Feedback.php');

try {
    $database = new Database();
    $connection = $database->getConnection();
    $feedbackObj = new Feedback($connection);
    $feedbacks = $feedbackObj->getAll();
    $users = $feedbackObj->getAllUsers();
    $buses = $feedbackObj->getAllBuses();
    $bookings = $feedbackObj->getAllBookings();
    
    // Get statistics
    $totalFeedbacks = $feedbackObj->getTotalFeedbacks();
    $averageRating = $feedbackObj->getAverageRating();
    $ratingDistribution = $feedbackObj->getRatingDistribution();
} catch (PDOException $e) {
    die("Error fetching feedbacks: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Feedback Management - Admin Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container mt-4">
  <!-- Back Button -->
  <a href="admin.html" class="btn btn-maroon-outline back-btn">&larr; Back</a>

  <!-- Page Title -->
  <h1 class="text-center fw-bold mb-4">Feedback Management</h1>

  <!-- Success/Error Messages -->
  <?php if (isset($_GET['msg'])): ?>
      <?php
      $msg = $_GET['msg'];
      $isDelete = stripos($msg, 'delete') !== false;
      $alertClass = $isDelete ? 'alert-danger' : 'alert-success';
      ?>
      <div class="alert <?= $alertClass ?> alert-dismissible fade show" role="alert">
          <?= htmlspecialchars($msg) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
  <?php endif; ?>

  <!-- Statistics Cards -->
  <div class="row mb-4">
      <div class="col-md-4">
          <div class="card text-center">
              <div class="card-body">
                  <h5 class="card-title">Total Feedbacks</h5>
                  <h2 class="text-primary"><?= $totalFeedbacks ?></h2>
              </div>
          </div>
      </div>
      <div class="col-md-4">
          <div class="card text-center">
              <div class="card-body">
                  <h5 class="card-title">Average Rating</h5>
                  <h2 class="text-warning"><?= $averageRating ?> ⭐</h2>
              </div>
          </div>
      </div>
      <div class="col-md-4">
          <div class="card text-center">
              <div class="card-body">
                  <h5 class="card-title">Rating Distribution</h5>
                  <div class="text-start">
                      <?php foreach ($ratingDistribution as $rating): ?>
                          <small><?= $rating['Rating'] ?>⭐: <?= $rating['Count'] ?> reviews</small><br>
                      <?php endforeach; ?>
                  </div>
              </div>
          </div>
      </div>
  </div>

  <!-- Add Button -->
  <div class="d-flex justify-content-end mb-3">
      <button class="btn btn-maroon" data-bs-toggle="modal" data-bs-target="#addModal">Add Feedback</button>
  </div>

  <!-- Feedback Table -->
  <div class="table-responsive">
    <table class="table table-bordered table-hover">
        <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>User</th>
          <th>Bus</th>
          <th>Booking</th>
          <th>Rating</th>
          <th>Comment</th>
          <th>Date</th>
          <th>Update</th>
          <th>Delete</th>
        </tr>
      </thead>
      <tbody>
      <?php if (!empty($feedbacks)): ?>
          <?php foreach ($feedbacks as $feedback): ?>
              <tr>
                  <td><?= htmlspecialchars($feedback['ID']) ?></td>
                  <td>
                      <?php if ($feedback['UserName']): ?>
                          <strong><?= htmlspecialchars($feedback['UserName']) ?></strong>
                          <br><small class="text-muted"><?= htmlspecialchars($feedback['UserEmail']) ?></small>
                      <?php else: ?>
                          <em class="text-muted">Anonymous</em>
                      <?php endif; ?>
                  </td>
                  <td>
                      <?php if ($feedback['BusNumber']): ?>
                          <strong><?= htmlspecialchars($feedback['BusNumber']) ?></strong>
                          <br><small class="text-muted"><?= htmlspecialchars($feedback['Origin']) ?> → <?= htmlspecialchars($feedback['Destination']) ?></small>
                      <?php else: ?>
                          <em class="text-muted">General</em>
                      <?php endif; ?>
                  </td>
                  <td>
                      <?php if ($feedback['BookingId']): ?>
                          <span class="badge bg-info">#<?= htmlspecialchars($feedback['BookingId']) ?></span>
                          <?php if ($feedback['SeatNumber']): ?>
                              <br><small>Seat: <?= htmlspecialchars($feedback['SeatNumber']) ?></small>
                          <?php endif; ?>
                      <?php else: ?>
                          <em class="text-muted">N/A</em>
                      <?php endif; ?>
                  </td>
                  <td>
                      <span class="fs-5">
                          <?php if ($feedback['Rating']): ?>
                              <?= str_repeat('⭐', $feedback['Rating']) ?>
                              <br><small class="text-muted"><?= $feedback['Rating'] ?>/5</small>
                          <?php else: ?>
                              <em class="text-muted">No rating</em>
                          <?php endif; ?>
                      </span>
                  </td>
                  <td>
                      <?php if ($feedback['Comment']): ?>
                          <div style="max-width: 200px;">
                              <?= htmlspecialchars(substr($feedback['Comment'], 0, 100)) ?>
                              <?= strlen($feedback['Comment']) > 100 ? '...' : '' ?>
                          </div>
                      <?php else: ?>
                          <em class="text-muted">No comment</em>
                      <?php endif; ?>
                  </td>
                  <td><?= htmlspecialchars($feedback['CreatedDate']) ?></td>
                  <td>
                      <a href="php/update_feedback.php?id=<?= $feedback['ID'] ?>" class="btn btn-success btn-sm">Update</a>
                  </td>
                  <td>
                      <!-- Delete Button triggers Modal -->
                      <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $feedback['ID'] ?>">
                          Delete
                      </button>

                      <!-- Delete Confirmation Modal -->
                      <div class="modal fade" id="deleteModal<?= $feedback['ID'] ?>" tabindex="-1" aria-labelledby="deleteLabel<?= $feedback['ID'] ?>" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title" id="deleteLabel<?= $feedback['ID'] ?>">Confirm Delete</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                              Are you sure you want to delete this feedback?
                              <br><strong>Rating:</strong> <?= $feedback['Rating'] ?> stars
                              <br><strong>User:</strong> <?= htmlspecialchars($feedback['UserName'] ?: 'Anonymous') ?>
                              <br><small class="text-danger">This action cannot be undone.</small>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                              <a href="php/delete_feedback.php?id=<?= $feedback['ID'] ?>" class="btn btn-danger">Delete</a>
                            </div>
                          </div>
                        </div>
                      </div>
                  </td>
              </tr>
          <?php endforeach; ?>
      <?php else: ?>
          <tr>
              <td colspan="9" class="text-center text-muted">No feedback found.</td>
          </tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Add Feedback Modal -->
<form action="php/insert_feedback.php" method="POST">
    <div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Feedback</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">User (Optional)</label>
                                <select name="user_id" class="form-select">
                                    <option value="">Anonymous User</option>
                                    <?php foreach ($users as $user): ?>
                                        <option value="<?= htmlspecialchars($user['ID']) ?>">
                                            <?= htmlspecialchars($user['Name']) ?> (<?= htmlspecialchars($user['Email']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Bus (Optional)</label>
                                <select name="bus_id" class="form-select">
                                    <option value="">General Feedback</option>
                                    <?php foreach ($buses as $bus): ?>
                                        <option value="<?= htmlspecialchars($bus['ID']) ?>">
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
                                <label class="form-label">Related Booking (Optional)</label>
                                <select name="booking_id" class="form-select">
                                    <option value="">No specific booking</option>
                                    <?php foreach ($bookings as $booking): ?>
                                        <option value="<?= htmlspecialchars($booking['ID']) ?>">
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
                                <label class="form-label">Rating</label>
                                <select name="rating" class="form-select" required>
                                    <option value="">Select rating...</option>
                                    <option value="5">5 Stars ⭐⭐⭐⭐⭐ (Excellent)</option>
                                    <option value="4">4 Stars ⭐⭐⭐⭐ (Good)</option>
                                    <option value="3">3 Stars ⭐⭐⭐ (Average)</option>
                                    <option value="2">2 Stars ⭐⭐ (Poor)</option>
                                    <option value="1">1 Star ⭐ (Very Poor)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Comment</label>
                        <textarea name="comment" class="form-control" rows="4" maxlength="1000" 
                                  placeholder="Enter feedback comment..."></textarea>
                        <div class="form-text">Maximum 1000 characters</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="add_feedback" class="btn btn-maroon w-100">Add Feedback</button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
// Form validation
document.querySelector('#addModal form').addEventListener('submit', function(e) {
    const rating = this.querySelector('select[name="rating"]').value;
    const comment = this.querySelector('textarea[name="comment"]').value.trim();
    
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

// Character counter for comment field
document.querySelector('#addModal textarea[name="comment"]').addEventListener('input', function() {
    const maxLength = 1000;
    const currentLength = this.value.length;
    const remaining = maxLength - currentLength;
    
    let helpText = this.parentNode.querySelector('.form-text');
    helpText.textContent = `${remaining} characters remaining (${currentLength}/${maxLength})`;
    
    if (remaining < 100) {
        helpText.className = 'form-text text-warning';
    } else if (remaining < 50) {
        helpText.className = 'form-text text-danger';
    } else {
        helpText.className = 'form-text';
    }
});
</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
