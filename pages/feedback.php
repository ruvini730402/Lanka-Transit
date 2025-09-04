<?php
session_start();

// Include database configuration
require_once '../classes/Database.php';

// Get database connection
$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    die("Connection failed: Unable to connect to database");
}

// Try to get UserId from session (optional)
$userId = $_SESSION['UserID'] ?? null;

// Fetch buses — Make sure 'Bus' table exists
$stmt = $conn->prepare("SELECT ID, BusNumber FROM Bus");
$stmt->execute();
$busResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Feedback Form - LankaTransit</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/user-feedback.css" />
  <style>
    .star-rating {
      text-align: left;
      padding: 15px 0;
    }
    
    .stars {
      font-size: 2rem;
      margin-bottom: 10px;
    }
    
    .star {
      color: #ddd;
      cursor: pointer;
      transition: color 0.2s ease;
      margin: 0 2px;
    }
    
    .star:hover,
    .star.active {
      color: #ffc107;
    }
    
    .star.hovered {
      color: #ffc107;
    }
    
    .rating-text {
      font-size: 14px;
      color: #666;
      margin-top: 8px;
      font-weight: 500;
    }
    
    .rating-selected {
      color: #800000;
      font-weight: bold;
    }
  </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="../index.php">
                <span class="fw-bold" style="color: #800000;">Lanka Transit</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="incidents.php">
                            <i class="fas fa-exclamation-triangle me-1"></i>Report Incident
                        </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="../auth/Logout.php">
                        <i class="fas fa-sign-out-alt me-1"></i>Logout
                      </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>


<div class="feedback-wrapper">
  <div class="feedback-container">
    <h4 class="mb-3 text-center">Feedback Form</h4>
    <p class="text-muted mb-4 text-center">
      We value your opinion! Help us improve the LankaTransit experience by sharing your thoughts.
    </p>

    <form action="submit_feedback.php" method="POST">
      <?php if ($userId): ?>
        <input type="hidden" name="user_id" value="<?= htmlspecialchars($userId) ?>">
      <?php endif; ?>

      <div class="mb-3">
        <label for="bus_id" class="form-label">Select Bus</label>
        <select name="bus_id" id="bus_id" class="form-select" required>
          <option value="">-- Select Bus --</option>
          <?php foreach ($busResult as $bus): ?>
            <option value="<?= $bus['ID'] ?>"><?= htmlspecialchars($bus['BusNumber']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="mb-3">
        <label for="rating" class="form-label">Rating</label>
        <div class="star-rating">
          <input type="hidden" name="rating" id="rating" required>
          <div class="stars" id="starContainer">
            <i class="fas fa-star star" data-rating="1"></i>
            <i class="fas fa-star star" data-rating="2"></i>
            <i class="fas fa-star star" data-rating="3"></i>
            <i class="fas fa-star star" data-rating="4"></i>
            <i class="fas fa-star star" data-rating="5"></i>
          </div>
          <div class="rating-text" id="ratingText">Click stars to rate</div>
        </div>
      </div>

      <div class="mb-3">
        <label for="comment" class="form-label">Comments (Optional)</label>
        <textarea name="comment" id="comment" rows="5" class="form-control" placeholder="Write your comments here..."></textarea>
      </div>

      <div class="text-end">
        <button type="submit" class="btn-lanka">Submit Feedback</button>
      </div>
    </form>
  </div>
</div>

<!-- Footer -->
<footer class="text-white py-4 mt-5" style="background-color: #800000;">
  <div class="container">
    <div class="row">
      <p>&copy; 2025 Transit. All rights reserved.</p>
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const stars = document.querySelectorAll('.star');
    const ratingInput = document.getElementById('rating');
    const ratingText = document.getElementById('ratingText');
    const ratingLabels = {
        1: 'Poor - Needs significant improvement',
        2: 'Fair - Below expectations',
        3: 'Good - Meets expectations',
        4: 'Very Good - Exceeds expectations',
        5: 'Excellent - Outstanding service!'
    };
    
    let selectedRating = 0;
    
    stars.forEach((star, index) => {
        // Click event
        star.addEventListener('click', function() {
            selectedRating = parseInt(this.dataset.rating);
            ratingInput.value = selectedRating;
            updateStars(selectedRating);
            ratingText.textContent = ratingLabels[selectedRating];
            ratingText.classList.add('rating-selected');
        });
        
        // Hover events
        star.addEventListener('mouseenter', function() {
            if (selectedRating === 0) {
                const hoverRating = parseInt(this.dataset.rating);
                updateStars(hoverRating, true);
                ratingText.textContent = ratingLabels[hoverRating];
            }
        });
        
        star.addEventListener('mouseleave', function() {
            if (selectedRating === 0) {
                updateStars(0);
                ratingText.textContent = 'Click stars to rate';
            }
        });
    });
    
    function updateStars(rating, isHover = false) {
        stars.forEach((star, index) => {
            star.classList.remove('active', 'hovered');
            if (index < rating) {
                if (isHover && selectedRating === 0) {
                    star.classList.add('hovered');
                } else if (!isHover) {
                    star.classList.add('active');
                }
            }
        });
    }
    
    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        if (selectedRating === 0) {
            e.preventDefault();
            alert('Please select a rating before submitting your feedback.');
            return false;
        }
    });
});
</script>
</body>
</html>
