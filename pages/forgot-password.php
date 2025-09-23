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
      background: linear-gradient(135deg, #f0f4f8 0%, #e8f0fe 100%);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      padding-top: 40px;
      min-height: 100vh;
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
    .forgot-password-header {
      font-size: 2rem;
      font-weight: 700;
      color: #003366;
      text-align: center;
      margin-bottom: 2rem;
      position: relative;
    }
    .forgot-password-header::after {
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
    .text-darkblue {
      color: #003366;
    }
    .back-link {
      font-size: 0.9rem;
      color: #003366;
      text-decoration: none;
      transition: all 0.3s ease;
    }
    .back-link:hover {
      text-decoration: underline;
      color: #800000;
    }
    .card-header a {
      transition: all 0.3s ease;
    }
    .card-header a:hover {
      text-decoration: underline;
      color: #a30000 !important;
    }
    .alert {
      border-radius: 12px;
      border: none;
      font-weight: 500;
      margin-bottom: 1.5rem;
    }
    .alert-warning {
      background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
      color: #856404;
    }
    .alert-success {
      background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
      color: #155724;
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
  </style>
</head>
<body class="bg-light">
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-5">
        <div class="card shadow-lg">
          <div class="card-header d-flex justify-content-between align-items-center">
            <span class="fw-bold fs-5" style="color: #800000;">Lanka Transit</span>
            <a href="login-form.php" class="fw-semibold" style="color: #800000; text-decoration: none;">Back</a>
          </div>
          <div class="card-body">
            <div class="forgot-password-header">Reset Password</div>
            <?php if (isset($_SESSION['forgot_message'])): ?>
              <div class="alert alert-warning text-darkblue">
                <?= htmlspecialchars($_SESSION['forgot_message']); unset($_SESSION['forgot_message']); ?>
              </div>
            <?php elseif (isset($_SESSION['success'])): ?>
              <div class="alert alert-success text-darkblue">
                <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
              </div>
            <?php endif; ?>
            <form action="http://localhost/auth/send_reset_link.php" method="POST" autocomplete="off">
              <div class="mb-3">
                <label for="email" class="form-label text-darkblue">Registered Email</label>
                <input type="email" name="email" id="email" class="form-control" required autocomplete="off" placeholder="Enter your registered email address">
              </div>
              <button type="submit" class="btn btn-maroon w-100 mt-3">Send Reset Link</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>