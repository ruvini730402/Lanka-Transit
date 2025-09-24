<?php
require_once __DIR__ . '/../includes/session_config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Password Reset Successful - Lanka Transit</title>
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
        .success-header {
            font-size: 2rem;
            font-weight: 700;
            color: #003366;
            text-align: center;
            margin-bottom: 2rem;
            position: relative;
        }
        .success-header::after {
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
        .alert-success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
            border-radius: 12px;
            border: none;
            font-weight: 500;
            margin-bottom: 1.5rem;
            font-size: 1.1rem;
            text-align: center;
        }
        .card > .card-body {
            padding: 2rem;
        }
        .shadow-lg {
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1), 0 5px 15px rgba(0, 0, 0, 0.07) !important;
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
                        <a href="login-form.php" class="fw-semibold" style="color: #800000; text-decoration: none;" onmouseover="this.style.textDecoration='underline'; this.style.color='#a30000'" onmouseout="this.style.textDecoration='none'; this.style.color='#800000'">Go to Login</a>
                    </div>
                    <div class="card-body">
                        <div class="success-header">Password Reset Successful</div>
                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success text-darkblue">
                                <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-success text-darkblue">
                                ✅ Your password has been reset successfully! 🎉 Please log in to continue your journey with Lanka Transit.
                            </div>
                        <?php endif; ?>
                        <a href="login-form.php" class="btn btn-maroon w-100 mt-3">Log In Now</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>