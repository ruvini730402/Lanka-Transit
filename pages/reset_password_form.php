<?php
session_start();
$token = $_GET['token'] ?? '';
if (!$token || $token === '$token') {
    $_SESSION['reset_error'] = '❌ Invalid or missing token.';
    header('Location: http://localhost/pages/forgot-password.php');
    exit;
}
error_log("Accessing reset_password_form.php with token: " . $token);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Lanka Transit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f0f4f8 0%, #d9e2ec 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .card {
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            background: #ffffff;
        }
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        .app-title {
            color: #c1121f;
            font-size: 1.75rem;
            font-weight: 700;
            margin: 0;
        }
        .text-darkblue {
            color: #002244;
        }
        .btn-maroon {
            background-color: #800000;
            color: white;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            border-radius: 8px;
        }
        .btn-maroon:hover {
            background-color: #a30000;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(163, 0, 0, 0.3);
        }
        .btn-maroon:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }
        .btn-maroon .spinner {
            display: none;
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
        }
        .btn-maroon.loading .spinner {
            display: inline-block;
        }
        .btn-maroon.loading span {
            visibility: hidden;
        }
        .password-instructions {
            background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
            border-radius: 12px;
            padding: 1.2rem;
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
            color: #003366;
        }
        .password-instructions ul {
            margin-bottom: 0;
            padding-left: 1.2rem;
        }
        .password-instructions li {
            margin-bottom: 0.5rem;
        }
        .form-control {
            border-radius: 8px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .form-control:focus {
            border-color: #800000;
            box-shadow: 0 0 8px rgba(128, 0, 0, 0.2);
        }
        .password-match-indicator {
            font-size: 0.9rem;
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
    <div class="container mt-4 mt-md-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card p-4 shadow">
                    <div class="card-header">
                        <h2 class="app-title">Lanka Transit</h2>
                        <h4 class="text-darkblue mb-0">Reset Your Password</h4>
                    </div>
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
                    <form method="POST" action="http://localhost/auth/reset_password.php" autocomplete="off">
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
                        <button type="submit" class="btn btn-maroon w-100" id="submitBtn">
                            <span>Reset Password</span>
                            <span class="spinner spinner-border spinner-border-sm"></span>
                        </button>
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
            submitBtn.disabled = true;
            submitBtn.classList.add('loading');

            if (newPassword.value !== confirmPassword.value) {
                e.preventDefault();
                alert('Passwords do not match. Please check and try again.');
                submitBtn.disabled = false;
                submitBtn.classList.remove('loading');
                return false;
            }
            if (newPassword.value.length < 12) {
                e.preventDefault();
                alert('Password must be at least 12 characters long.');
                submitBtn.disabled = false;
                submitBtn.classList.remove('loading');
                return false;
            }
            if (!/[A-Z]/.test(newPassword.value) || !/[a-z]/.test(newPassword.value) || !/[0-9]/.test(newPassword.value)) {
                e.preventDefault();
                alert('Password must include uppercase, lowercase letters, and at least one number.');
                submitBtn.disabled = false;
                submitBtn.classList.remove('loading');
                return false;
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>