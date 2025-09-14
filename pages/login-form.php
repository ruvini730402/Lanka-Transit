<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #f0f4f8 0%, #e8f0fe 100%);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      padding-top: 40px;
      min-height: 100vh;
    }

    .title-dark-blue {
      color: #003366;
    }

    .card {
      border-radius: 20px;
      border: none;
      backdrop-filter: blur(10px);
      background: rgba(255, 255, 255, 0.95);
    }

    .card-header {
      background: linear-gradient(135deg, #f9f9f9 0%, #ffffff 100%) !important;
      border-radius: 20px 20px 0 0 !important;
      border-bottom: 1px solid #e9ecef;
      padding: 1.5rem 2rem;
    }

    .form-label {
      color: #003366;
      font-weight: 600;
      font-size: 0.95rem;
      margin-bottom: 0.7rem;
    }

    .form-control {
      border-radius: 12px;
      border: 2px solid #e9ecef;
      padding: 12px 16px;
      font-size: 1rem;
      transition: all 0.3s ease;
    }

    .form-control:focus {
      border-color: #003366;
      box-shadow: 0 0 0 0.2rem rgba(0, 51, 102, 0.15);
    }

    .btn-maroon {
      background: linear-gradient(135deg, #800000 0%, #a30000 100%);
      color: white;
      border: none;
      border-radius: 12px;
      padding: 14px;
      font-weight: 600;
      font-size: 1.1rem;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(128, 0, 0, 0.3);
    }

    .btn-maroon:hover {
      background: linear-gradient(135deg, #a30000 0%, #cc0000 100%);
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(128, 0, 0, 0.4);
    }

    .login-header {
      font-size: 2rem;
      font-weight: 700;
      color: #003366;
      text-align: center;
      margin-bottom: 2rem;
      position: relative;
    }

    .login-header::after {
      content: '';
      position: absolute;
      bottom: -10px;
      left: 50%;
      transform: translateX(-50%);
      width: 60px;
      height: 3px;
      background: linear-gradient(135deg, #800000, #a30000);
      border-radius: 2px;
    }

    .link-blue {
      color: #003366;
      text-decoration: none;
      transition: all 0.3s ease;
    }

    .link-blue:hover {
      color: #800000;
      text-decoration: underline;
    }

    .alert {
      border-radius: 12px;
      border: none;
      font-weight: 500;
      margin-bottom: 1.5rem;
    }

    .alert-success {
      background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
      color: #155724;
    }

    .alert-danger {
      background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
      color: #721c24;
    }

    .card-header a {
      transition: all 0.3s ease;
    }

    .card-header a:hover {
      text-decoration: underline;
      color: #a30000 !important;
    }

    .mb-3 {
      margin-bottom: 1.5rem !important;
    }

    .card > .card-body, .card > :not(.card-header):first-child {
      padding: 2rem;
    }

    .shadow-lg {
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1), 0 5px 15px rgba(0, 0, 0, 0.07) !important;
    }

    .text-center.mt-3 {
      margin-top: 2rem !important;
      padding-top: 1.5rem;
      border-top: 1px solid #e9ecef;
    }

    .text-center.mt-3:first-of-type {
      border-top: 1px solid #e9ecef;
    }

    .text-center.mt-3:last-of-type {
      border-top: none;
      margin-top: 1rem !important;
      padding-top: 0;
    }

    input::placeholder {
      color: #6c757d;
      font-style: italic;
    }

    .form-control:valid {
      border-color: #28a745;
    }

    .form-control:invalid:not(:placeholder-shown) {
      border-color: #dc3545;
    }

    .alert-container {
      position: fixed;
      top: 20px;
      left: 50%;
      transform: translateX(-50%);
      z-index: 1050;
      width: 90%;
      max-width: 500px;
    }

    @media (min-width: 768px) {
      .alert-container {
        position: relative;
        top: auto;
        left: auto;
        transform: none;
        width: 100%;
        max-width: none;
      }
    }
  </style>
</head>
<body>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-5">

        <!-- Message alert -->
        <div class="alert-container">
          <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger text-center" id="alertBox" role="alert">
              <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
          <?php elseif (isset($_SESSION['success'])): ?>
            <div class="alert alert-success text-center" id="alertBox" role="alert">
              <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
            </div>

            <!-- Auto-redirect after 2 seconds -->
            <script>
              setTimeout(() => {
                window.location.href = "<?php echo ($_SESSION['role'] === 'admin') ? 'admin.php' : 'dashboard.php'; ?>";
              }, 2000);
            </script>
          <?php endif; ?>
        </div>

        <div class="card shadow-lg">
          <div class="card-header d-flex justify-content-between align-items-center">
            <span class="fw-bold fs-5" style="color: #800000;">Lanka Transit</span>
            <a href="../index.php" class="fw-semibold" style="color: #800000; text-decoration: none;">Home</a>
          </div>

          <div class="card-body">
            <div class="login-header">User Login</div>

            <form action="../auth/login.php" method="POST" autocomplete="off">
              <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" required autocomplete="off" placeholder="Enter your email address" />
              </div>
              <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required autocomplete="new-password" placeholder="Enter your password" />
              </div>

              <button type="submit" class="btn btn-maroon w-100 mt-3">Login</button>
            </form>

            <div class="text-center mt-3">
              <span class="title-dark-blue">Don't have an account?</span>
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
  </div>

  <?php if (isset($_SESSION['role'])): ?>
  <script>
    setTimeout(() => {
      window.location.href = "<?php echo ($_SESSION['role'] === 'admin') ? 'admin.php' : 'dashboard.php'; ?>";
    }, 2000);
  </script>
  <?php endif; ?>

  <!-- Auto-dismiss alert after 3 seconds -->
  <script>
    const alertBox = document.getElementById('alertBox');
    if (alertBox) {
      setTimeout(() => {
        alertBox.style.opacity = '0';
        alertBox.style.transition = 'opacity 0.3s ease';
        setTimeout(() => {
          alertBox.style.display = 'none';
        }, 300);
      }, 3000);
    }
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>