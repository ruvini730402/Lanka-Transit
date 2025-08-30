<?php
include('../php/dbcon.php');
$stmt = $connection->prepare("
    SELECT f.ID, u.Name AS UserName, f.BusId, f.Rating, f.Comment
    FROM Feedback f
    LEFT JOIN User u ON f.UserId = u.ID
    ORDER BY f.ID DESC
");
$stmt->execute();
$feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
  <style>
    .btn-delete {
      background-color: #dc3545;
      color: white;
    }
    .btn-delete:hover {
      background-color: #c82333;
    }
  </style>
</head>
<body>
<div class="container mt-4">
  <!-- Back Button -->
  <a href="admin.php" class="btn back-btn mb-3">&larr; Back</a>

  <!-- Page Title -->
  <h1 class="text-center fw-bold mb-4">User Feedbacks</h1>

  <!-- Feedback Table -->
  <div class="table-responsive">
    <table class="table table-bordered table-hover">
        <thead class="table-dark">
        <tr>
          <th>User Name</th>
          <th>Bus ID</th>
          <th>Rating</th>
          <th>Comment</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($feedbacks)): ?>
          <?php foreach ($feedbacks as $fb): ?>
            <tr>
              <td><?= htmlspecialchars($fb['UserName'] ?? 'Unknown') ?></td>
              <td><?= htmlspecialchars($fb['BusId']) ?></td>
              <td><?= htmlspecialchars($fb['Rating']) ?></td>
              <td><?= htmlspecialchars($fb['Comment']) ?></td>
              <td><button class="btn btn-sm btn-delete">Delete</button></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="5" class="text-center">No feedback found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
