<?php
/**
 * Cleanup script for test payment data
 * Removes test payment records from the database
 */
header('Content-Type: application/json');

try {
    require_once '../classes/Database.php';
    
    $db = new Database();
    $conn = $db->getConnection();
    
    // Delete test payment records
    $stmt = $conn->prepare("
        DELETE FROM Payment 
        WHERE OrderID LIKE 'LT-TEST-%' 
           OR OrderID LIKE 'LT-PENDING-%' 
           OR OrderID LIKE 'LT-FAILED-%' 
           OR OrderID LIKE 'LT-NOTFOUND-%'
    ");
    
    $stmt->execute();
    $deletedCount = $stmt->affected_rows;
    
    echo json_encode([
        'success' => true,
        'count' => $deletedCount,
        'message' => "Successfully removed {$deletedCount} test payment records."
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
