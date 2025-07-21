<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <!-- Viewport meta tag for responsive behavior -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Feedback Form - LankaTransit</title>

    <!-- Bootstrap CSS for responsive layout and components -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <!-- Bootstrap Icons for various icons used on the page -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"/>

    <!-- Custom CSS for specific page styling -->
    <link rel="stylesheet" href="UserFeedbackForm.css" />
</head>
<body>
    <?php
    $showBackIcon = true;
    include 'Header.php';
    ?>

    
    <!-- Wrapper to center the feedback form on the page -->
    <div class="feedback-wrapper">
        <!-- Main container for the feedback form -->
        <div class="feedback-container">
            <h4 class="mb-3 text-center">Feedback Form</h4>
            <p class="text-muted mb-4 text-center">
                We value your opinion! Help us improve the LankaTransit experience by sharing your thoughts.
            </p>

            <!-- Feedback Submission Form -->
            <form action="submit_feedback.php" method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label">Your Name</label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="Enter your name" required />
                </div>

                <div class="mb-3">
                    <label for="feedback_type" class="form-label">Feedback Type</label>
                    <select name="feedback_type" id="feedback_type" class="form-select" required>
                        <option value="">-- Select Type --</option>
                        <option value="Service Quality">Service Quality</option>
                        <option value="UI/UX Experience">UI/UX Experience</option>
                        <option value="Booking Process">Booking Process</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="rating" class="form-label">Rating</label>
                    <select name="rating" id="rating" class="form-select" required>
                        <option value="">-- Select Rating --</option>
                        <option value="5">Excellent (5)</option>
                        <option value="4">Very Good (4)</option>
                        <option value="3">Good (3)</option>
                        <option value="2">Fair (2)</option>
                        <option value="1">Poor (1)</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="message" class="form-label">Comments (Optional)</label>
                    <textarea name="message" id="message" rows="5" class="form-control" placeholder="Write your comments here..."></textarea>
                </div>

                <div class="text-end">
                    <!-- Submit button for the form -->
                    <button type="submit" class="btn-lanka">Submit Feedback</button>
                </div>
            </form>
        </div>
    </div>

    <?php
    include 'Footer.php';
    ?>
</body>
</html>