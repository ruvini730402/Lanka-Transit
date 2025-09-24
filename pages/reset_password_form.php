<?php
require_once __DIR__ . '/../includes/session_config.php';

$token = $_GET['token'] ?? '';
if (!$token) {
    die("❌ Invalid or missing token.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password - Lanka Transit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
        .btn-maroon:disabled {
            background: #6c757d;
            box-shadow: none;
            cursor: not-allowed;
        }
        .reset-password-header {
            font-size: 2rem;
            font-weight: 700;
            color: #003366;
            text-align: center;
            margin-bottom: 2rem;
            position: relative;
        }
        .reset-password-header::after {
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
        .alert-danger {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
        }
        .mb-3 {
            margin-bottom: 1.5rem !important;
        }
        .card > .card-body {
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
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-lg">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span class="fw-bold fs-5" style="color: #800000;">Lanka Transit</span>
                        <a href="login-form.php" class="fw-semibold" style="color: #800000; text-decoration: none;">Back to Login</a>
                    </div>
                    <div class="card-body">
                        <div class="reset-password-header">Reset Your Password</div>
                        <?php if (isset($_SESSION['reset_error'])): ?>
                            <div class="alert alert-danger text-darkblue">
                                <?= htmlspecialchars($_SESSION['reset_error']); unset($_SESSION['reset_error']); ?>
                            </div>
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
                        <form method="POST" action="../auth/reset_password.php" autocomplete="off" id="resetPasswordForm">
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
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const newPassword = document.getElementById('new_password');
        const confirmPassword = document.getElementById('confirm_password');
        const matchIndicator = document.getElementById('passwordMatchIndicator');
        const submitBtn = document.getElementById('submitBtn');
        const form = document.getElementById('resetPasswordForm');

        function validatePassword(password) {
            const minLength = password.length >= 12;
            const hasUppercase = /[A-Z]/.test(password);
            const hasLowercase = /[a-z]/.test(password);
            const hasNumber = /[0-9]/.test(password);
            return minLength && hasUppercase && hasLowercase && hasNumber;
        }

        function checkPasswordMatch() {
            if (confirmPassword.value === '') {
                matchIndicator.style.display = 'none';
                submitBtn.disabled = false;
                return;
            }
            matchIndicator.style.display = 'block';
            if (newPassword.value === confirmPassword.value) {
                matchIndicator.textContent = '✓ Passwords match';
                matchIndicator.className = 'password-match-indicator match';
                confirmPassword.style.borderColor = '#28a745';
                submitBtn.disabled = !validatePassword(newPassword.value);
            } else {
                matchIndicator.textContent = '✗ Passwords do not match';
                matchIndicator.className = 'password-match-indicator no-match';
                confirmPassword.style.borderColor = '#dc3545';
                submitBtn.disabled = true;
            }
        }

        newPassword.addEventListener('input', () => {
            checkPasswordMatch();
            if (newPassword.value !== '') {
                if (validatePassword(newPassword.value)) {
                    newPassword.style.borderColor = '#28a745';
                } else {
                    newPassword.style.borderColor = '#dc3545';
                }
            }
        });

        confirmPassword.addEventListener('input', checkPasswordMatch);

        form.addEventListener('submit', function(e) {
            if (newPassword.value !== confirmPassword.value) {
                e.preventDefault();
                alert('Passwords do not match. Please check and try again.');
                return false;
            }
            if (!validatePassword(newPassword.value)) {
                e.preventDefault();
                alert('Password must be at least 12 characters long, include uppercase and lowercase letters, and at least one number.');
                return false;
            }
        });
    </script>
</body>
</html>