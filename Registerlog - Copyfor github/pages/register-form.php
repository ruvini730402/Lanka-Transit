<?php
session_start();

// Get and clean the messages
$success = (!empty($_SESSION['success']) && trim($_SESSION['success']) !== '') ? trim($_SESSION['success']) : null;
$error = (!empty($_SESSION['error']) && trim($_SESSION['error']) !== '') ? trim($_SESSION['error']) : null;
unset($_SESSION['success'], $_SESSION['error']);



?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>User Registration</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background-color: #f0f4f8;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .title-dark-blue {
      color: #003366;
    }
    .card {
      border-radius: 15px;
    }
    .form-label {
      color: #003366;
      font-weight: 500;
    }
    .btn-maroon {
      background-color: #800000;
      color: white;
    }
    .btn-maroon:hover {
      background-color: #a30000;
    }
    .register-header {
      font-size: 1.8rem;
      font-weight: bold;
      color: #003366;
      text-align: center;
      margin-bottom: 20px;
    }
    .link-blue {
      color: #003366;
    }
    .link-blue:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-5">
        <div class="card shadow-lg p-4">

          <?php if (!empty($success)): ?>
  <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
    <?= htmlspecialchars($success) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

<?php      If (!empty($error)): ?>
  <div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
    <?= htmlspecialchars($error) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

          <div class="register-header">User Registration</div>

          <form action="../auth/register.php" method="POST" autocomplete="off">
            <div class="mb-3">
              <label class="form-label" for="name">Full Name</label>
              <input type="text" name="name" id="name" class="form-control" required autocomplete="off" />
            </div>
            <div class="mb-3">
              <label class="form-label" for="email">Email Address</label>
              <input type="email" name="email" id="email" class="form-control" required autocomplete="off" />
            </div>
            <div class="mb-3">
              <label class="form-label" for="password">Password</label>
              <input type="password" name="password" id="password" class="form-control" required minlength="6" autocomplete="new-password" />
            </div>
            <button type="submit" class="btn btn-maroon w-100">Register</button>
          </form>

          <div class="text-center mt-3">
            <span class="title-dark-blue">Already have an account?</span>
            <a href="login-form.php" class="link-blue fw-semibold">Login here</a>
          </div>
          

        </div>
      </div>
    </div>
  </div>
     

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

