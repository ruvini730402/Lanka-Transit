<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Forgot Password</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .card {
      border-radius: 16px;
      padding: 30px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
    .btn-maroon {
      background-color: #800000;
      color: white;
    }
    .btn-maroon:hover {
      background-color: #a30000;
    }
    .text-darkblue {
      color: #002244;
    }
    .back-link {
      font-size: 0.9rem;
      color: #003366;
      text-decoration: none;
    }
    .back-link:hover {
      text-decoration: underline;
      color: #0055a5;
    }
  </style>
</head>
<body class="bg-light">
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card p-4 shadow">
        <h4 class="text-center mb-4 text-darkblue">Forgot Your Password?</h4>

        <?php if (isset($_SESSION['forgot_message'])): ?>
          <div class="alert alert-warning text-darkblue"><?= $_SESSION['forgot_message']; unset($_SESSION['forgot_message']); ?></div>
        <?php elseif (isset($_SESSION['success'])): ?>
          <div class="alert alert-success text-darkblue"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <form action="../auth/send_reset_link.php" method="POST">
          <div class="mb-3">
            <label for="email" class="form-label text-darkblue">Registered Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <button type="submit" class="btn btn-maroon w-100">Send Reset Link</button>
        </form>
      </div>
    </div>
  </div>
</div>
</body>
</html>
