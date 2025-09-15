<?php
/**
 * Sample Announcements Creator
 * Run this script to add sample announcements for testing
 */
require_once '../classes/Announcement.php';

$announcement = new Announcement();

// Initialize table if it doesn't exist
$announcement->initializeTable();

// Sample announcements data
$sampleAnnouncements = [
    [
        'title' => 'New Bus Routes Available',
        'message' => 'We are excited to announce new bus routes connecting Colombo to Kandy via scenic mountain roads. These routes offer comfortable seating and air conditioning for a pleasant journey. Booking is now open for all new routes starting from next week.'
    ],
    [
        'title' => 'Holiday Schedule Changes',
        'message' => 'Please note that during the upcoming Vesak holiday period, our bus schedules will be modified. Some routes may have reduced frequency while others will have additional services to popular destinations. Check our updated schedules before planning your travel.'
    ],
    [
        'title' => 'Online Payment System Upgrade',
        'message' => 'Our online payment system has been upgraded to provide better security and faster processing. You can now pay using multiple payment methods including credit cards, debit cards, and mobile wallets. All transactions are secured with the latest encryption technology.'
    ],
    [
        'title' => 'COVID-19 Safety Measures',
        'message' => 'Your safety is our priority. All our buses are regularly sanitized and we encourage passengers to wear masks during travel. Hand sanitizers are available at all bus stations. We have also reduced seating capacity to ensure proper social distancing.'
    ],
    [
        'title' => 'Student Discount Program',
        'message' => 'Attention students! We are now offering special discounted rates for students with valid student IDs. Get up to 20% off on regular fares for intercity routes. Visit our website or contact our customer service for more details on how to apply for student discounts.'
    ]
];

echo "<h2>Adding Sample Announcements...</h2>";

foreach ($sampleAnnouncements as $index => $announcementData) {
    $result = $announcement->createAnnouncement($announcementData['title'], $announcementData['message']);
    
    if ($result) {
        echo "<p>✅ Added: " . htmlspecialchars($announcementData['title']) . "</p>";
    } else {
        echo "<p>❌ Failed to add: " . htmlspecialchars($announcementData['title']) . "</p>";
    }
}

echo "<h3>Sample announcements have been added successfully!</h3>";
echo "<p><a href='../index.php'>← Back to Home Page</a></p>";
echo "<p><a href='announcements.php'>→ View All Announcements</a></p>";
?>
