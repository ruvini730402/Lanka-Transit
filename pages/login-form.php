<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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

    .login-header {
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

    .alert {
      border-radius: 12px;
      font-weight: 500;
    }
    .card-header a:hover {
  text-decoration: underline;
  color: #a30000;
}
  </style>
</head>
<body>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-5">

        <!-- Message alert -->
        <?php if (isset($_SESSION['error'])): ?>
          <div class="alert alert-danger text-center mb-3" id="alertBox" role="alert">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
          </div>
        <?php elseif (isset($_SESSION['success'])): ?>
          <div class="alert alert-success text-center mb-3" id="alertBox" role="alert">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
          </div>

          <!-- Auto-redirect after 2 seconds -->
          <script>
            setTimeout(() => {
              window.location.href = "<?php echo ($_SESSION['role'] === 'admin') ? 'admin_dashboard.php' : 'user_dashboard.php'; ?>";
            }, 2000);
          </script>
        <?php endif; ?>

        <div class="card shadow-lg p-4">
            <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #f9f9f9;">
  <span class="fw-bold" style="color: #800000;">Lanka Transit</span>
  <a href="index.php" class="fw-semibold" style="color: #800000; text-decoration: none;">Home</a>
</div>
          <div class="login-header">User  Login</div>
          <?php if (isset($_SESSION['success'])): ?>
  <div class="alert alert-success text-center" id="alertBox">
    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
  </div>
<?php elseif (isset($_SESSION['error'])): ?>
  <div class="alert alert-danger text-center" id="alertBox">
    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
  </div>
<?php endif; ?>

          <form action="../auth/login.php" method="POST" autocomplete="off"  >
            <div class="mb-3">
              
            </div>
            <div class="mb-3">
              <label class="form-label">Email Address</label>
              <input type="email" name="email" class="form-control" required  autocomplete="off" />
            </div>
            <div class="mb-3">
  <label class="form-label">Password</label>
  <input type="password" name="password" class="form-control" required autocomplete="new-password" />
  
</div>




            <button type="submit" class="btn btn-maroon w-100">Login</button>
          </form>
          <div class="text-center mt-3">
            <span class="title-dark-blue">Don’t have an account?</span>
            <a href="register-form.php" class="link-blue fw-semibold">Register here</a>
          </div>
          <div class="text-center mt-3">
          <span class="title-dark-blue">Forgot your password?</span>
          <a href="forgot-password.php" class="link-blue fw-semibold">Update password</a>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php if (isset($_SESSION['role'])): ?>
<script>
  setTimeout(() => {
    window.location.href = "<?php echo ($_SESSION['role'] === 'admin') ? 'admin_dashboard.php' : 'user_dashboard.php'; ?>";
  }, 2000);
</script>
<?php endif; ?>

  <!-- Auto-dismiss alert after 3 seconds -->
  <script>
    const alertBox = document.getElementById('alertBox');
    if (alertBox) {
      setTimeout(() => {
        alertBox.style.display = 'none';
      }, 3000);
    }
  </script>
</body>
</html>




