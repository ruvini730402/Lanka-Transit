<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Feedback Management - Admin Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="../assets/css/admin-style.css">
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
  <a href="dashboard.php" class="btn back-btn mb-3">&larr; Back</a>

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
        <tr>
          <td>Devindi Jayaweera</td>
          <td>BUS-1023</td>
          <td>5</td>
          <td>Excellent service and clean bus.</td>
          <td><button class="btn btn-sm btn-delete">Delete</button></td>
        </tr>
        <tr>
          <td>Kasun Perera</td>
          <td>BUS-1102</td>
          <td>4</td>
          <td>Driver was polite, but slight delay in departure.</td>
          <td><button class="btn btn-sm btn-delete">Delete</button></td>
        </tr>
        <tr>
          <td>Nimali Fernando</td>
          <td>BUS-1075</td>
          <td>2</td>
          <td>Bus was too crowded. Not satisfied.</td>
          <td><button class="btn btn-sm btn-delete">Delete</button></td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
