<?php
session_start();
$token = $_GET['token'] ?? '';

if (!$token) {
    die("❌ Invalid or missing token.");
}
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
    </style>
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-4 shadow">
                <h4 class="text-center mb-4 text-darkblue">Reset Your Password</h4>

                <?php if (isset($_SESSION['reset_error'])): ?>
                    <div class="alert alert-danger text-darkblue"><?= $_SESSION['reset_error']; unset($_SESSION['reset_error']); ?></div>
                <?php endif; ?>

                <form method="POST" action="../auth/reset_password.php">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                    <div class="mb-3">
                        <label class="form-label text-darkblue">New Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-darkblue">Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-maroon w-100">Reset Password</button>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
