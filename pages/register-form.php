<?php
require_once __DIR__ . "/../includes/session_config.php";

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
      background: linear-gradient(135deg, #f0f4f8 0%, #e8f0fe 100%);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      padding-top: 40px;
      min-height: 100vh;
      display: flex;
      align-items: center;
    }
    .title-dark-blue {
      color: #003366;
    }
    .card {
      border-radius: 20px;
      border: none;
      backdrop-filter: blur(10px);
      background: rgba(255, 255, 255, 0.95);
      transition: transform 0.3s ease;
    }
    .card:hover {
      transform: translateY(-5px);
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
    .btn-maroon:disabled {
      background: #6c757d;
      box-shadow: none;
      cursor: not-allowed;
    }
    .register-header {
      font-size: 2rem;
      font-weight: 700;
      color: #003366;
      text-align: center;
      margin-bottom: 2rem;
      position: relative;
    }
    .register-header::after {
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
    .alert-success {
      background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
      color: #155724;
    }
    .alert-danger {
      background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
      color: #721c24;
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
    .password-strength {
      height: 5px;
      border-radius: 5px;
      margin-top: 8px;
      transition: all 0.3s ease;
    }
    .strength-weak {
      background: #dc3545;
      width: 33%;
    }
    .strength-medium {
      background: #ffc107;
      width: 66%;
    }
    .strength-strong {
      background: #28a745;
      width: 100%;
    }
    .password-requirements {
      font-size: 0.85rem;
      color: #6c757d;
      margin-top: 0.5rem;
    }
    .password-requirements li.valid {
      color: #28a745;
    }
    .toggle-password {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #003366;
    }
    .password-container {
      position: relative;
    }
  </style>
</head>
<body>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-5">
        <div class="card shadow-lg">
          <!-- Soft navbar-like header inside the card -->
          <div class="card-header d-flex justify-content-between align-items-center">
            <span class="fw-bold fs-5" style="color: #800000;">Lanka Transit</span>
            <a href="../index.php" class="fw-semibold" style="color: #800000; text-decoration: none;">Home</a>
          </div>

          <div class="card-body">
            <?php if (!empty($success)): ?>
              <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
                <?= htmlspecialchars($success) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
              <div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
                <?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
            <?php endif; ?>

            <div class="register-header">User Registration</div>

            <form action="../auth/register.php" method="POST" autocomplete="off" id="registerForm" novalidate>
              <div class="mb-3">
                <label class="form-label" for="name">Full Name</label>
                <input type="text" name="name" id="name" class="form-control" required autocomplete="off" placeholder="Enter your full name" />
              </div>
              <div class="mb-3">
                <label class="form-label" for="email">Email Address</label>
                <input type="email" name="email" id="email" class="form-control" required autocomplete="off" placeholder="Enter your email address" />
              </div>
              <div class="mb-3">
                <label class="form-label" for="mobile">Mobile Number</label>
                <input type="tel" name="mobile" id="mobile" class="form-control" required pattern="[0-9]{10}" placeholder="Enter 10-digit mobile number" autocomplete="off" />
              </div>
              <div class="mb-3 password-container">
                <label class="form-label" for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" required autocomplete="new-password" placeholder="Enter a secure password (min 12 characters)" />
                <span class="toggle-password" onclick="togglePassword()">👁️</span>
                <div class="password-strength" id="passwordStrength"></div>
                <ul class="password-requirements">
                  <li id="length">At least 12 characters</li>
                  <li id="uppercase">At least one uppercase letter</li>
                  <li id="lowercase">At least one lowercase letter</li>
                  <li id="number">At least one number</li>
                  <li id="special">At least one special character</li>
                </ul>
              </div>
              <button type="submit" class="btn btn-maroon w-100 mt-3" id="submitButton" disabled>Register</button>
            </form>

            <div class="text-center mt-3">
              <span class="title-dark-blue">Already have an account?</span>
              <a href="login-form.php" class="link-blue fw-semibold">Login here</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const passwordInput = document.getElementById('password');
    const passwordStrength = document.getElementById('passwordStrength');
    const submitButton = document.getElementById('submitButton');
    const requirements = {
      length: document.getElementById('length'),
      uppercase: document.getElementById('uppercase'),
      lowercase: document.getElementById('lowercase'),
      number: document.getElementById('number'),
      special: document.getElementById('special')
    };

    function validatePassword() {
      const password = passwordInput.value;
      const tests = {
        length: password.length >= 12,
        uppercase: /[A-Z]/.test(password),
        lowercase: /[a-z]/.test(password),
        number: /[0-9]/.test(password),
        special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
      };

      // Update requirement indicators
      Object.keys(tests).forEach(key => {
        requirements[key].classList.toggle('valid', tests[key]);
      });

      // Calculate strength
      const passedTests = Object.values(tests).filter(Boolean).length;
      passwordStrength.className = 'password-strength';
      if (passedTests <= 2) {
        passwordStrength.classList.add('strength-weak');
      } else if (passedTests <= 4) {
        passwordStrength.classList.add('strength-medium');
      } else {
        passwordStrength.classList.add('strength-strong');
      }

      // Enable/disable submit button
      submitButton.disabled = !Object.values(tests).every(Boolean);
    }

    function togglePassword() {
      const type = passwordInput.type === 'password' ? 'text' : 'password';
      passwordInput.type = type;
      document.querySelector('.toggle-password').textContent = type === 'password' ? '👁️' : '🙈';
    }

    passwordInput.addEventListener('input', validatePassword);

    // Initial validation
    validatePassword();
  </script>
</body>
</html>