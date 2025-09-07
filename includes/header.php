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
	<style>
		.navbar {
			padding: 1rem 0;
			transition: all 0.3s ease;
			border-bottom: 1px solid rgba(0, 0, 0, 0.08);
		}
		.navbar-brand {
			font-size: 1.75rem;
			font-weight: 700;
			letter-spacing: -0.025em;
			transition: color 0.3s ease;
		}
		.navbar-brand:hover {
			color: #4B0000 !important;
		}
		.navbar-nav .nav-link {
			font-weight: 500;
			font-size: 0.95rem;
			padding: 0.75rem 1rem !important;
			margin: 0 0.25rem;
			border-radius: 8px;
			transition: all 0.3s ease;
			color: #374151 !important;
			position: relative;
		}
		.navbar-nav .nav-link:hover {
			background-color: rgba(128, 0, 0, 0.08);
			color: #800000 !important;
			transform: translateY(-1px);
		}
		.navbar-nav .nav-link.active {
			background-color: rgba(128, 0, 0, 0.1);
			color: #800000 !important;
			font-weight: 600;
		}
		.navbar-nav .nav-link.active::after {
			content: '';
			position: absolute;
			bottom: 0;
			left: 50%;
			transform: translateX(-50%);
			width: 30px;
			height: 3px;
			background: linear-gradient(135deg, #800000 0%, #4B0000 100%);
			border-radius: 2px;
		}
		.navbar-toggler {
			border: none;
			padding: 0.5rem;
			border-radius: 8px;
			transition: all 0.3s ease;
		}
		.navbar-toggler:focus {
			box-shadow: 0 0 0 3px rgba(128, 0, 0, 0.1);
		}
		.navbar-toggler:hover {
			background-color: rgba(128, 0, 0, 0.05);
		}
		.dropdown-menu {
			border: none;
			box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
			border-radius: 12px;
			padding: 0.75rem 0;
			margin-top: 0.5rem;
			min-width: 200px;
		}
		.dropdown-item {
			padding: 0.75rem 1.25rem;
			font-size: 0.9rem;
			font-weight: 500;
			transition: all 0.2s ease;
			border-radius: 0;
		}
		.dropdown-item:hover {
			background-color: rgba(128, 0, 0, 0.08);
			color: #800000;
			padding-left: 1.5rem;
		}
		.dropdown-item i {
			width: 18px;
			text-align: center;
			opacity: 0.7;
		}
		.dropdown-divider {
			margin: 0.5rem 1rem;
			opacity: 0.1;
		}
		.nav-item.dropdown .nav-link {
			display: flex;
			align-items: center;
			gap: 0.5rem;
		}
		.navbar-nav .dropdown-toggle::after {
			margin-left: 0.5rem;
			transition: transform 0.3s ease;
		}
		.navbar-nav .dropdown.show .dropdown-toggle::after {
			transform: rotate(180deg);
		}
		@media (max-width: 991.98px) {
			.navbar-nav {
				padding-top: 1rem;
				border-top: 1px solid rgba(0, 0, 0, 0.08);
				margin-top: 1rem;
			}
			.navbar-nav .nav-link {
				padding: 0.75rem 0 !important;
				margin: 0;
				border-radius: 0;
				border-bottom: 1px solid rgba(0, 0, 0, 0.05);
			}
			.navbar-nav .nav-link:last-child {
				border-bottom: none;
			}
			.dropdown-menu {
				background-color: rgba(248, 249, 250, 0.95);
				backdrop-filter: blur(10px);
				border: 1px solid rgba(0, 0, 0, 0.05);
				margin-left: 1rem;
			}
		}
		.navbar.scrolled {
			background-color: rgba(255, 255, 255, 0.95) !important;
			backdrop-filter: blur(20px);
			box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
		}
	</style>
</head>
<body style="padding-top: 80px;">
	<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top" id="mainNavbar">
		<div class="container">
			<a class="navbar-brand d-flex align-items-center" href="/index.php">
				<span class="fw-bold" style="color: #800000;">Transit</span>
			</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarNav">
				<ul class="navbar-nav ms-auto">
					<li class="nav-item">
						<a class="nav-link" href="/index.php">
							<i class="fas fa-home me-1"></i>Home
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="/pages/announcements.php">
							<i class="fas fa-bullhorn me-1"></i>Announcements
						</a>
					</li>
					<?php if (isset($_SESSION['email']) && isset($_SESSION['role'])): ?>
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
								<div class="d-flex align-items-center"></div>
							</a>
							<ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
								<li></li>
								<li></li>
								<li></li>
								<li><hr class="dropdown-divider"></li>
								<li></li>
							</ul>
						</li>
					<?php else: ?>
						<li class="nav-item">
							<a class="nav-link" href="/auth/login.php">
								<i class="fas fa-sign-in-alt me-1"></i>Login
							</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="/auth/register.php">
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
