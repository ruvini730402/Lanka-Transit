<?php
// Get booking data from URL parameters
$ref = $_GET['ref'] ?? 'LT-000000';
$name = $_GET['name'] ?? 'Passenger Name';
$phone = $_GET['phone'] ?? '0000000000';
$origin = $_GET['origin'] ?? 'Origin';
$destination = $_GET['destination'] ?? 'Destination';
$date = $_GET['date'] ?? date('Y-m-d');
$bus = $_GET['bus'] ?? 'BUS-000';
$seat = $_GET['seat'] ?? '00';
$fare = $_GET['fare'] ?? '0.00';

// Format date
$formatted_date = date('F d, Y', strtotime($date));
$generated_time = date('d M Y, H:i');

// Set headers for PDF download
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="Lanka-Transit-' . $ref . '.pdf"');
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

// Create PDF content with improved but simpler styling
$pdf_content = '
%PDF-1.4
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
/MediaBox [0 0 612 792]
/Resources <<
  /Font <<
    /F1 4 0 R
    /F2 5 0 R
  >>
>>
/Contents 6 0 R
>>
endobj

4 0 obj
<<
/Type /Font
/Subtype /Type1
/BaseFont /Helvetica-Bold
>>
endobj

5 0 obj
<<
/Type /Font
/Subtype /Type1
/BaseFont /Helvetica
>>
endobj

6 0 obj
<<
/Length 2800
>>
stream
BT

% Header Section
/F1 24 Tf
0.6 0.1 0.1 rg
50 720 Td
(LANKA TRANSIT) Tj

/F1 14 Tf
0 0 0 rg
0 -25 Td
(Official E-Ticket) Tj

% Booking Reference Box
0.1 0.7 0.1 rg
50 650 512 30 re
f
1 1 1 rg
/F1 16 Tf
60 662 Td
(Booking Reference: ' . $ref . ') Tj

% Main Content
0 0 0 rg
/F1 14 Tf
50 610 Td
(PASSENGER INFORMATION) Tj

/F2 12 Tf
0 -25 Td
(Name: ' . $name . ') Tj
0 -20 Td
(Phone: ' . $phone . ') Tj

% Travel Details
/F1 14 Tf
0 -35 Td
(TRAVEL DETAILS) Tj

/F2 12 Tf
0 -25 Td
(Route: ' . $origin . ' to ' . $destination . ') Tj
0 -20 Td
(Travel Date: ' . $formatted_date . ') Tj
0 -20 Td
(Bus Number: ' . $bus . ') Tj

% Seat and Fare
/F1 14 Tf
0 -35 Td
(BOOKING DETAILS) Tj

/F2 12 Tf
0 -25 Td
(Seat Number: ' . $seat . ') Tj
0 -20 Td
(Fare: Rs. ' . number_format($fare, 2) . ') Tj

% Status
/F1 16 Tf
0.1 0.7 0.1 rg
0 -35 Td
(STATUS: CONFIRMED) Tj

% Instructions
0 0 0 rg
/F1 12 Tf
0 -40 Td
(BOARDING INSTRUCTIONS:) Tj

/F2 10 Tf
0 -20 Td
(• Arrive 15 minutes before departure time) Tj
0 -15 Td
(• Present this ticket and valid ID to conductor) Tj
0 -15 Td
(• Keep this ticket safe during your journey) Tj
0 -15 Td
(• Contact customer service for any queries) Tj

% Footer
/F2 8 Tf
0.5 0.5 0.5 rg
0 -40 Td
(Generated: ' . $generated_time . ') Tj

% Contact Info
0 -15 Td
(Lanka Transit | www.lankatransit.lk | 011-2345678) Tj

ET
endstream
endobj

xref
0 7
0000000000 65535 f 
0000000009 00000 n 
0000000058 00000 n 
0000000115 00000 n 
0000000245 00000 n 
0000000317 00000 n 
0000000384 00000 n 
trailer
<<
/Size 7
/Root 1 0 R
>>
startxref
3236
%%EOF';

echo $pdf_content;
?>