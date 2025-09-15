<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Transit</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
	<link href="<?php echo (strpos($_SERVER['PHP_SELF'], '/pages/') !== false) ? '../assets/css/main.css' : 'assets/css/main.css'; ?>" rel="stylesheet">
</head>
<body style="padding-top: 60px;">
	<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top" id="mainNavbar">
		<div class="container">
			<a class="navbar-brand d-flex align-items-center" href="<?php echo (strpos($_SERVER['PHP_SELF'], '/pages/') !== false) ? '../index.php' : 'index.php'; ?>">
				<span class="fw-bold" style="color: #800000;">Transit</span>
			</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarNav">
				<ul class="navbar-nav ms-auto">
					<li class="nav-item">
						<a class="nav-link" href="<?php echo (strpos($_SERVER['PHP_SELF'], '/pages/') !== false) ? '../index.php' : 'index.php'; ?>">
							<i class="fas fa-home me-1"></i>Home
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="<?php echo (strpos($_SERVER['PHP_SELF'], '/pages/') !== false) ? 'announcements.php' : 'pages/announcements.php'; ?>">
							<i class="fas fa-bullhorn me-1"></i>Announcements
						</a>
					</li>
					<?php if (isset($_SESSION['email']) && isset($_SESSION['role'])): ?>
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
								<div class="d-flex align-items-center">
									<i class="fas fa-user me-1"></i><?php echo htmlspecialchars($_SESSION['name'] ?? 'User'); ?>
								</div>
							</a>
							<ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
								<li><a class="dropdown-item" href="<?php echo (strpos($_SERVER['PHP_SELF'], '/pages/') !== false) ? '../dashboard/' : 'dashboard/'; ?>"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
								<li><a class="dropdown-item" href="<?php echo (strpos($_SERVER['PHP_SELF'], '/pages/') !== false) ? '../pages/bookings.php' : 'pages/bookings.php'; ?>"><i class="fas fa-ticket-alt me-2"></i>My Bookings</a></li>
								<li><a class="dropdown-item" href="<?php echo (strpos($_SERVER['PHP_SELF'], '/pages/') !== false) ? '../pages/profile.php' : 'pages/profile.php'; ?>"><i class="fas fa-user-edit me-2"></i>Profile</a></li>
								<li><hr class="dropdown-divider"></li>
								<li><a class="dropdown-item" href="<?php echo (strpos($_SERVER['PHP_SELF'], '/pages/') !== false) ? '../auth/logout.php' : 'auth/logout.php'; ?>"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
							</ul>
						</li>
					<?php else: ?>
						<li class="nav-item">
							<a class="nav-link" href="<?php echo (strpos($_SERVER['PHP_SELF'], '/pages/') !== false) ? '../auth/login.php' : 'auth/login.php'; ?>">
								<i class="fas fa-sign-in-alt me-1"></i>Login
							</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?php echo (strpos($_SERVER['PHP_SELF'], '/pages/') !== false) ? 'register-form.php' : 'pages/register-form.php'; ?>">
								<i class="fas fa-user-plus me-1"></i>Sign Up
							</a>
						</li>
					<?php endif; ?>
				</ul>
			</div>
		</div>
	</nav>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
	<script>
		window.addEventListener('scroll', function() {
			const navbar = document.getElementById('mainNavbar');
			if (window.scrollY > 50) {
				navbar.classList.add('scrolled');
			} else {
				navbar.classList.remove('scrolled');
			}
		});
	</script>
