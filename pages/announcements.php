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
    <title>Announcements - Lanka Transit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #4B0000 0%, #800000 100%);
            color: white;
            padding: 80px 0;
        }
        .announcement-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-bottom: 30px;
            border-left: 5px solid #800000;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .announcement-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
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
            justify-content: between;
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
            background: #f8f9fa;
            border-radius: 15px;
            margin: 40px 0;
        }
        .stats-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
            margin-top: -30px;
            position: relative;
            z-index: 10;
        }
        .content-section {
            padding: 60px 0;
            background: #f8f9fa;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="../index.php">
                <span class="fw-bold" style="color: #800000;">Lanka Transit</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="announcements.php">Announcements</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="booking.php">
                            <i class="fas fa-ticket-alt me-1"></i>Book Ticket
                        </a>
                    </li>
                    <?php if (isset($_SESSION['email']) && isset($_SESSION['role'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $_SESSION['role'] === 'admin' ? '../Admin/admin.html' : 'dashboard.php'; ?>">
                                <i class="fas fa-user me-1"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../auth/Logout.php">
                                <i class="fas fa-sign-out-alt me-1"></i>Logout
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login-form.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register-form.php">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container text-center">
            <div class="mb-4">
                <i class="fas fa-bullhorn fa-4x"></i>
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
                    <h3 class="text-primary mb-2"><?php echo $totalCount; ?></h3>
                    <p class="text-muted mb-0">
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
                                
                                <div class="announcement-content">
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
    <footer class="text-white py-4" style="background-color: #800000;">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <p>&copy; 2025 Lanka Transit. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
