<?php
/**
 * Announcements Page - Display all public announcements
 */
require_once __DIR__ . "/../includes/session_config.php";
require_once '../classes/Announcement.php';

$announcement = new Announcement();
$allAnnouncements = $announcement->getAllAnnouncements();
$totalCount = $announcement->getAnnouncementsCount();

// Include header
include '../includes/header.php';
?>

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

    <!-- Main Content -->
    <div class="main-content">
        <!-- Stats Section -->
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-4 col-md-6 col-sm-8">
                    <div class="search-card">
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
        <section class="announcements-section">
            <div class="container">
                <?php if (!empty($allAnnouncements)): ?>
                    <div class="row">
                        <div class="col-12">
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
    </div>

<?php include '../includes/footer.php'; ?>

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
