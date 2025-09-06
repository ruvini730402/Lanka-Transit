<?php
/**
 * Announcements Page - Display all public announcements
 */
session_start();
require_once '../classes/Announcement.php';

$announcement = new Announcement();
$allAnnouncements = $announcement->getAllAnnouncements();
$totalCount = $announcement->getAnnouncementsCount();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements - Transit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #4B0000 0%, #800000 100%);
            color: white;
            padding: 100px 0 80px 0;
            position: relative;
        }
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="white" opacity="0.1"><polygon points="0,0 1000,100 1000,0"/></svg>') no-repeat;
            background-size: cover;
        }
        .announcement-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-bottom: 30px;
            border-left: 5px solid #800000;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .announcement-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, rgba(128, 0, 0, 0.05) 0%, transparent 50%);
            border-radius: 0 0 0 100px;
        }
        .announcement-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }
        .announcement-title {
            color: #800000;
            font-weight: bold;
            margin-bottom: 15px;
            font-size: 1.4rem;
        }
        .announcement-message {
            color: #555;
            line-height: 1.7;
            margin-bottom: 20px;
            font-size: 1rem;
        }
        .announcement-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 15px;
            border-top: 1px solid #e9ecef;
        }
        .announcement-date {
            color: #6c757d;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
        }
        .announcement-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #800000 0%, #4B0000 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            margin-bottom: 20px;
            float: left;
            margin-right: 20px;
        }
        .no-announcements {
            text-align: center;
            color: #6c757d;
            padding: 60px;
            background: white;
            border-radius: 15px;
            margin: 40px 0;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        .stats-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            padding: 25px;
            text-align: center;
            margin-top: -40px;
            position: relative;
            z-index: 10;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        .content-section {
            padding: 60px 0;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            position: relative;
        }
        
        /* Enhanced Navigation Styles */
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
        
        /* Responsive Navigation */
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
        
        /* Scroll Effect */
        .navbar.scrolled {
            background-color: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(20px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body style="padding-top: 80px;">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top" id="mainNavbar">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="../index.php">
                <span class="fw-bold" style="color: #800000;">Transit</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">
                            <i class="fas fa-home me-1"></i>Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="announcements.php">
                            <i class="fas fa-bullhorn me-1"></i>Announcements
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../auth/login.php">
                            <i class="fas fa-sign-in-alt me-1"></i>Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../auth/register.php">
                            <i class="fas fa-user-plus me-1"></i>Sign Up
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container text-center">
            <div class="mb-4">
                <i class="fas fa-bullhorn fa-4x" style="opacity: 0.9;"></i>
            </div>
            <h1 class="display-4 fw-bold mb-3">Announcements</h1>
            <p class="lead mb-4">Stay informed with our latest updates and important notices</p>
        </div>
    </section>

    <!-- Stats Section -->
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="stats-card">
                    <div class="mb-3">
                        <i class="fas fa-chart-bar fa-2x" style="color: #800000; opacity: 0.8;"></i>
                    </div>
                    <h3 class="mb-2" style="color: #800000; font-weight: 700;"><?php echo $totalCount; ?></h3>
                    <p class="text-muted mb-0 fw-500">
                        <i class="fas fa-newspaper me-2"></i>
                        Total Announcements
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Announcements Content -->
    <section class="content-section">
        <div class="container">
            <?php if (!empty($allAnnouncements)): ?>
                <div class="row">
                    <div class="col-lg-8 mx-auto">
                        <?php foreach ($allAnnouncements as $announcement_item): ?>
                            <div class="announcement-card">
                                <div class="announcement-icon">
                                    <i class="fas fa-info-circle"></i>
                                </div>
                                
                                <div class="announcement-content" style="position: relative; z-index: 2;">
                                    <h3 class="announcement-title">
                                        <?php echo htmlspecialchars($announcement_item['title']); ?>
                                    </h3>
                                    
                                    <div class="announcement-message">
                                        <?php echo nl2br(htmlspecialchars($announcement_item['message'])); ?>
                                    </div>
                                    
                                    <div class="announcement-meta">
                                        <div class="announcement-date">
                                            <i class="fas fa-calendar-alt me-2"></i>
                                            <?php 
                                            $createdDate = new DateTime($announcement_item['created_at']);
                                            echo 'Published: ' . $createdDate->format('F j, Y g:i A');
                                            
                                            // Show updated date if different from created date
                                            if ($announcement_item['updated_at'] !== $announcement_item['created_at']) {
                                                $updatedDate = new DateTime($announcement_item['updated_at']);
                                                echo '<br><i class="fas fa-edit me-2"></i>Updated: ' . $updatedDate->format('F j, Y g:i A');
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Back to Home -->
                <div class="text-center mt-5">
                    <a href="../index.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-home me-2"></i>Back to Home
                    </a>
                </div>
                
            <?php else: ?>
                <div class="row">
                    <div class="col-12">
                        <div class="no-announcements">
                            <i class="fas fa-bullhorn fa-4x text-muted mb-4"></i>
                            <h4 class="text-muted">No Announcements Available</h4>
                            <p class="mb-4">There are currently no announcements to display. Please check back later for updates.</p>
                            <a href="../index.php" class="btn btn-primary">
                                <i class="fas fa-home me-2"></i>Return to Home
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="text-white py-5" style="background: linear-gradient(135deg, #4B0000 0%, #800000 100%); margin-top: 60px;">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <div class="mb-3">
                        <i class="fas fa-bus fa-2x" style="opacity: 0.8;"></i>
                    </div>
                    <p class="mb-0 fw-500">&copy; 2025 Transit. All rights reserved.</p>
                    <p class="mb-0 small text-white-50 mt-2">Your trusted partner for bus travel</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Enhanced Navigation Scroll Effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('mainNavbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
        
        // Smooth scrolling for internal links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>
