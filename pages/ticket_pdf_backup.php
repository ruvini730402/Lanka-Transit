<?php
/**
 * Lanka Transit PDF Ticket Generator
 * Modern design matching confirmation page layout with visual improvements
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

// Generate modern PDF content matching confirmation page design
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
/F3 7 0 R
>>
>>
>>
endobj

4 0 obj
<<
/Length 2800
>>
stream
BT

% Draw header background rectangle (maroon gradient effect)
q
0.29 0 0 rg
50 750 495 80 re
f
Q

% Header section - Lanka Transit branding
/F1 24 Tf
1 1 1 rg
70 800 Td
(LANKA TRANSIT) Tj

% Subtitle
/F2 12 Tf
1 1 1 rg
0 -22 Td
(Official E-Ticket) Tj

% Booking reference in green box (like confirmation page)
q
0.83 0.93 0.85 rg
50 650 495 40 re
f
Q

% Booking reference text
/F1 16 Tf
0.08 0.34 0.14 rg
70 668 Td
(Booking Reference: " . $ticketData['reference'] . ") Tj

% Route section with arrow (matching confirmation design)
/F1 20 Tf
0.5 0 0 rg
100 600 Td
(" . strtoupper($ticketData['origin']) . " -> " . strtoupper($ticketData['destination']) . ") Tj

% Main ticket content area (dashed border like confirmation)
q
[3 3] 0 d
0.87 0.87 0.87 RG
2 w
50 200 495 380 re
S
Q

% Lanka Transit E-Ticket header inside box
/F1 14 Tf
0 0 0 rg
220 550 Td
(Lanka Transit E-Ticket) Tj

% Passenger Information section
/F1 12 Tf
0.29 0 0 rg
70 500 Td
(PASSENGER INFORMATION) Tj

/F2 10 Tf
0 0 0 rg
0 -20 Td
(Passenger Name:) Tj
/F1 10 Tf
130 0 Td
(" . $ticketData['passenger_name'] . ") Tj

/F2 10 Tf
-130 -18 Td
(Phone Number:) Tj
/F1 10 Tf
130 0 Td
(" . $ticketData['phone'] . ") Tj

% Travel Information section
/F1 12 Tf
0.29 0 0 rg
-130 -35 Td
(TRAVEL INFORMATION) Tj

/F2 10 Tf
0 0 0 rg
0 -20 Td
(Travel Date:) Tj
/F1 10 Tf
130 0 Td
(" . date('F j, Y', strtotime($ticketData['travel_date'])) . ") Tj

/F2 10 Tf
-130 -18 Td
(Bus Number:) Tj
/F1 10 Tf
130 0 Td
(" . $ticketData['bus_number'] . ") Tj

% Seat number with badge style (like confirmation page)
/F2 10 Tf
250 0 Td
(Seat Number:) Tj

% Seat badge background
q
0.13 0.11 0.99 rg
380 405 60 20 re
f
Q

/F1 10 Tf
1 1 1 rg
395 410 Td
(" . $ticketData['seat_number'] . ") Tj

% Fare information
/F2 10 Tf
0 0 0 rg
-380 -18 Td
(Fare:) Tj
/F1 10 Tf
130 0 Td
(Rs. " . number_format($ticketData['fare'], 2) . ") Tj

% Status confirmation
/F1 14 Tf
0.16 0.65 0.58 rg
180 330 Td
(STATUS: CONFIRMED) Tj

% Important notice box (like confirmation page warning)
q
1 0.96 0.8 rg
50 250 495 60 re
f
Q

/F1 11 Tf
0.72 0.55 0 rg
70 280 Td
(IMPORTANT:) Tj

/F2 9 Tf
0 0 0 rg
0 -15 Td
(Please arrive at the bus terminal at least 15 minutes before departure time.) Tj
0 -12 Td
(Keep this confirmation handy for verification during boarding.) Tj

% Boarding instructions
/F1 11 Tf
0.29 0 0 rg
70 200 Td
(BOARDING INSTRUCTIONS) Tj

/F2 9 Tf
0 0 0 rg
0 -16 Td
(- Present this ticket and valid ID when boarding) Tj
0 -12 Td
(- Boarding starts 10 minutes before departure time) Tj
0 -12 Td
(- Keep your seat number visible during the journey) Tj

% Footer section
q
0.5 0 0 rg
50 80 495 40 re
f
Q

/F1 12 Tf
1 1 1 rg
200 95 Td
(Lanka Transit - Connecting Sri Lanka) Tj

/F2 8 Tf
1 1 1 rg
170 50 Td
(Generated: " . date('d M Y, H:i') . " | Valid for one journey only) Tj

/F2 8 Tf
0.7 0.7 0.7 rg
50 25 Td
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

7 0 obj
<<
/Type /Font
/Subtype /Type1
/BaseFont /Helvetica-Oblique
>>
endobj

xref
0 8
0000000000 65535 f 
0000000010 00000 n 
0000000053 00000 n 
0000000125 00000 n 
0000000279 00000 n 
0000003129 00000 n 
0000003199 00000 n 
0000003264 00000 n 
trailer
<<
/Size 8
/Root 1 0 R
>>
startxref
3336
%%EOF";

echo $pdfContent;
?>
