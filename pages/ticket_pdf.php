<?php
/**
 * Lanka Transit PDF Ticket Generator
 * Themed single-page PDF ticket matching the website design
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
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

// Generate themed PDF content
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
/F2 6 0 R
>>
>>
>>
endobj

4 0 obj
<<
/Length 1600
>>
stream
BT

% Header section with maroon theme
/F1 20 Tf
0.29 0 0 rg
50 780 Td
(LANKA TRANSIT) Tj

% Subtitle
/F2 10 Tf
0.5 0.5 0.5 rg
0 -22 Td
(Official E-Ticket) Tj

% Reset to black
0 0 0 rg

% Booking reference
/F1 12 Tf
0 -30 Td
(Booking Reference: " . $ticketData['reference'] . ") Tj

% Route section
/F1 16 Tf
0 -35 Td
(" . strtoupper($ticketData['origin']) . " → " . strtoupper($ticketData['destination']) . ") Tj

% Passenger details section
/F1 12 Tf
0.29 0 0 rg
0 -40 Td
(PASSENGER INFORMATION) Tj

0 0 0 rg
/F2 10 Tf
0 -22 Td
(Name: " . $ticketData['passenger_name'] . ") Tj
0 -16 Td
(Phone: " . $ticketData['phone'] . ") Tj

% Travel details section
/F1 12 Tf
0.29 0 0 rg
0 -35 Td
(TRAVEL INFORMATION) Tj

0 0 0 rg
/F2 10 Tf
0 -22 Td
(Travel Date: " . date('d M Y', strtotime($ticketData['travel_date'])) . ") Tj
0 -16 Td
(Bus Number: " . $ticketData['bus_number'] . ") Tj
0 -16 Td
(Seat Number: " . $ticketData['seat_number'] . ") Tj
0 -16 Td
(Fare Amount: Rs. " . number_format($ticketData['fare'], 2) . ") Tj

% Status
/F1 12 Tf
0.13 0.59 0.95 rg
0 -30 Td
(STATUS: CONFIRMED) Tj

% Instructions section
/F1 11 Tf
0.29 0 0 rg
0 -35 Td
(BOARDING INSTRUCTIONS) Tj

0 0 0 rg
/F2 9 Tf
0 -18 Td
(• Arrive at bus station 15 minutes before departure) Tj
0 -14 Td
(• Present this ticket and valid ID when boarding) Tj
0 -14 Td
(• Boarding starts 10 minutes before departure time) Tj
0 -14 Td
(• Keep this ticket safe during your journey) Tj

% Footer information
/F2 8 Tf
0.5 0.5 0.5 rg
0 -30 Td
(Generated: " . date('d M Y, H:i') . " | Valid for one journey only) Tj

% Company information
/F1 10 Tf
0.29 0 0 rg
0 -25 Td
(Lanka Transit - Connecting Sri Lanka) Tj

0 0 0 rg
/F2 8 Tf
0 -15 Td
(www.lankatransit.lk | Customer Service: 0115-123-456) Tj

ET
endstream
endobj

5 0 obj
<<
/Type /Font
/Subtype /Type1
/BaseFont /Helvetica-Bold
>>
endobj

6 0 obj
<<
/Type /Font
/Subtype /Type1
/BaseFont /Helvetica
>>
endobj

xref
0 7
0000000000 65535 f 
0000000010 00000 n 
0000000053 00000 n 
0000000125 00000 n 
0000000279 00000 n 
0000001929 00000 n 
0000001999 00000 n 
trailer
<<
/Size 7
/Root 1 0 R
>>
startxref
2064
%%EOF";

echo $pdfContent;
?>
