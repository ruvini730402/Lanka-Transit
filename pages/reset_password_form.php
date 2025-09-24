<?php
require_once __DIR__ . '/../includes/session_config.php';
$token = $_GET['token'] ?? '';
if (!$token || $token === '$token') {
    $_SESSION['reset_error'] = '❌ Invalid or missing token.';
    header('Location: http://localhost:8080/pages/forgot-password.php');
    exit;
}
error_log("Accessing reset_password_form.php with token: " . $token);
?>
<!DOCTYPE html>
<html>
<head>
  <title>Reset Password</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
    .text-darkblue {
      color: #002244;
    }
    .btn-maroon {
      background-color: #800000;
      color: white;
    }
    .btn-maroon:hover {
      background-color: #a30000;
    }
    .password-instructions {
      background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
      border-radius: 10px;
      padding: 1rem;
      margin-bottom: 1.5rem;
      font-size: 0.9rem;
      color: #003366;
    }
    .password-instructions ul {
      margin-bottom: 0;
      padding-left: 1.2rem;
    }
    .password-instructions li {
      margin-bottom: 0.3rem;
    }
    .password-match-indicator {
      font-size: 0.85rem;
      margin-top: 0.5rem;
      font-weight: 500;
    }
    .password-match-indicator.match {
      color: #28a745;
    }
    .password-match-indicator.no-match {
      color: #dc3545;
    }
  </style>
</head>
<body class="bg-light">
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card p-4 shadow">
          <h4 class="text-center mb-4 text-darkblue">Reset Your Password</h4>
          <?php if (isset($_SESSION['reset_error'])): ?>
            <div class="alert alert-danger text-darkblue"><?= htmlspecialchars($_SESSION['reset_error']); unset($_SESSION['reset_error']); ?></div>
          <?php endif; ?>
          <div class="password-instructions">
            <strong>Password Requirements:</strong>
            <ul>
              <li>At least 12 characters long</li>
              <li>Include both uppercase and lowercase letters</li>
              <li>Include at least one number</li>
              <li>Passwords must match</li>
            </ul>
          </div>
          <form method="POST" action="http://localhost:8080/auth/reset_password.php" autocomplete="off">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
            <div class="mb-3">
              <label class="form-label text-darkblue">New Password</label>
              <input type="password" name="password" id="new_password" class="form-control" required minlength="12" autocomplete="new-password" placeholder="Enter your new password">
            </div>
            <div class="mb-3">
              <label class="form-label text-darkblue">Confirm Password</label>
              <input type="password" name="confirm_password" id="confirm_password" class="form-control" required minlength="12" autocomplete="new-password" placeholder="Confirm your new password">
              <div id="passwordMatchIndicator" class="password-match-indicator" style="display: none;"></div>
            </div>
            <button type="submit" class="btn btn-maroon w-100" id="submitBtn">Reset Password</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <script>
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('confirm_password');
    const matchIndicator = document.getElementById('passwordMatchIndicator');
    const submitBtn = document.getElementById('submitBtn');
    function checkPasswordMatch() {
      if (confirmPassword.value === '') {
        matchIndicator.style.display = 'none';
        return;
      }
      matchIndicator.style.display = 'block';
      if (newPassword.value === confirmPassword.value) {
        matchIndicator.textContent = '✓ Passwords match';
        matchIndicator.className = 'password-match-indicator match';
        confirmPassword.style.borderColor = '#28a745';
        submitBtn.disabled = false;
      } else {
        matchIndicator.textContent = '✗ Passwords do not match';
        matchIndicator.className = 'password-match-indicator no-match';
        confirmPassword.style.borderColor = '#dc3545';
        submitBtn.disabled = true;
      }
    }
    newPassword.addEventListener('input', checkPasswordMatch);
    confirmPassword.addEventListener('input', checkPasswordMatch);
    document.querySelector('form').addEventListener('submit', function(e) {
      if (newPassword.value !== confirmPassword.value) {
        e.preventDefault();
        alert('Passwords do not match. Please check and try again.');
        return false;
      }
      if (newPassword.value.length < 12) {
        e.preventDefault();
        alert('Password must be at least 12 characters long.');
        return false;
      }
      if (!/[A-Z]/.test(newPassword.value) || !/[a-z]/.test(newPassword.value) || !/[0-9]/.test(newPassword.value)) {
        e.preventDefault();
        alert('Password must include uppercase, lowercase letters, and at least one number.');
        return false;
      }
    });
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>