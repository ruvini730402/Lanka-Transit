<?php
/**
 * Simple PDF Ticket Generator - Debug Version
 * Creates a basic PDF to test if details are showing
 */

// Get ticket data from URL parameters
$ticketData = [
    'reference' => $_GET['ref'] ?? 'LT-000000',
    'passenger_name' => $_GET['name'] ?? 'Demo User',
    'phone' => $_GET['phone'] ?? '0771234567',
    'origin' => $_GET['origin'] ?? 'Badulla',
    'destination' => $_GET['destination'] ?? 'Matara',
    'travel_date' => $_GET['date'] ?? date('Y-m-d'),
    'bus_number' => $_GET['bus'] ?? 'NB-1001',
    'seat_number' => $_GET['seat'] ?? 'A1',
    'fare' => (float)($_GET['fare'] ?? 1500.00)
];

// Set headers for PDF download
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="Lanka-Transit-' . $ticketData['reference'] . '.pdf"');

// Simple PDF with just text to test
$pdfContent = "%PDF-1.4
1 0 obj
<<
/Type /Catalog
/Pages 2 0 R
>>
endobj

2 0 obj
<<
/Type /Pages
/Kids [3 0 R]
/Count 1
>>
endobj

3 0 obj
<<
/Type /Page
/Parent 2 0 R
/MediaBox [0 0 595 842]
/Contents 4 0 R
/Resources <<
/Font <<
/F1 5 0 R
>>
>>
>>
endobj

4 0 obj
<<
/Length 800
>>
stream
BT
/F1 12 Tf
50 800 Td
(LANKA TRANSIT - E-TICKET) Tj
0 -30 Td
(Booking Reference: " . $ticketData['reference'] . ") Tj
0 -25 Td
(Route: " . $ticketData['origin'] . " to " . $ticketData['destination'] . ") Tj
0 -25 Td
(Passenger: " . $ticketData['passenger_name'] . ") Tj
0 -20 Td
(Phone: " . $ticketData['phone'] . ") Tj
0 -20 Td
(Travel Date: " . date('F j, Y', strtotime($ticketData['travel_date'])) . ") Tj
0 -20 Td
(Bus Number: " . $ticketData['bus_number'] . ") Tj
0 -20 Td
(Seat Number: " . $ticketData['seat_number'] . ") Tj
0 -20 Td
(Fare: Rs. " . number_format($ticketData['fare'], 2) . ") Tj
0 -30 Td
(STATUS: CONFIRMED) Tj
0 -30 Td
(BOARDING INSTRUCTIONS:) Tj
0 -20 Td
(- Arrive 15 minutes before departure) Tj
0 -15 Td
(- Present this ticket and valid ID) Tj
0 -15 Td
(- Keep ticket safe during journey) Tj
0 -30 Td
(Generated: " . date('d M Y, H:i') . ") Tj
ET
endstream
endobj

5 0 obj
<<
/Type /Font
/Subtype /Type1
/BaseFont /Helvetica
>>
endobj

xref
0 6
0000000000 65535 f 
0000000010 00000 n 
0000000053 00000 n 
0000000125 00000 n 
0000000279 00000 n 
0000001128 00000 n 
trailer
<<
/Size 6
/Root 1 0 R
>>
startxref
1193
%%EOF";

echo $pdfContent;
?>