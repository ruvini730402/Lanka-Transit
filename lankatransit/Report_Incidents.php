<?php
// Database connection parameters
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'lankatrasit';

// Establish a new database connection
$conn = new mysqli($host, $user, $pass, $db);

// Check for connection errors and terminate if unsuccessful
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// --- Fetching Incidents Data ---

// Initialize an array to store fetched incident rows
$incidentRows = [];

// Query to select all incidents, ordered by creation date (newest first)
$result = $conn->query("SELECT * FROM incidents ORDER BY created_at DESC");

// Check if the query was successful and returned rows
if ($result && $result->num_rows > 0) {
    // Loop through each fetched row and add it to the incidentRows array
    while ($row = $result->fetch_assoc()) {
        $incidentRows[] = $row;
    }
}

// Close the database connection to free up resources
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Incident Reporting - LankaTransit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Base body styles */
        body {
            background-color: #800000; /* Maroon background */
            font-family: 'Segoe UI', sans-serif;
            position: relative; /* Needed for positioning the back-icon */
        }

        /* Main content container styling */
        .container {
            max-width: 900px;
            margin-top: 20px;
            padding: 0 15px;
        }

        /* Styling for form sections (reporting form and tracking table) */
        .form-section {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 0 12px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }

        /* Heading style within form sections */
        .form-section h3 {
            color: #800000;
        }

        /* Custom button styling */
        .btn-custom {
            background-color: #fff;
            color: white;
        }

        .btn-custom:hover {
            background-color: #fff;
        }

        /* Styling for status pills in the incident list */
        .status-pill {
            padding: 5px 12px;
            border-radius: 50px; /* Makes them pill-shaped */
            font-size: 0.85rem;
            color: white;
            display: inline-block;
        }

        /* Specific background colors for different incident statuses */
        .Pending { background-color: #f0ad4e; } /* Orange */
        .InProgress { background-color: #5bc0de; } /* Light Blue */
        .Resolved { background-color: #5cb85c; } /* Green */

        /* Styling for attachment links */
        .attachment-link {
            text-decoration: underline;
            color: #004080;
        }

        /* Styling for small explanatory notes */
        .note {
            font-size: 0.85rem;
            color: gray;
        }

        /* Back icon shown inside header only for feedback form */
            .back-in-header {
            color: #800000;
            display: inline-flex;
            align-items: center;
        }

        .back-in-header:hover {
        color: #600000;
        }        


        /* --- Responsive Styles for smaller screens (max-width: 576px) --- */
        @media (max-width: 576px) {
            .form-section {
                padding: 20px; /* Reduce padding on smaller screens */
            }

            .back-icon {
                position: fixed; /* Fixes position on scroll */
                top: 80px;
                left: 30px;
                width: 40px; /* Smaller icon size */
                height: 40px;
                z-index: 999;
            }

            .back-icon i {
                font-size: 24px; /* Smaller icon font size */
            }

            .container {
                padding-top: 30px; /* Add padding to prevent content overlap with fixed icon */
            }
        }
    </style>
</head>
<body>
    <?php
    $showBackIcon = true;

    // Include the common header for the page
    include 'Header.php';
    ?>


    <div class="container">
        <div class="form-section">
            <h3>📝 Report an Incident</h3>
            <p>Please fill in the details about the incident you experienced while using LankaTransit services.</p>

            <form action="submit_incidents.php" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Your Name</label>
                    <input type="text" class="form-control" name="user_name" placeholder="(Optional)">
                </div>

                <div class="mb-3">
                    <label class="form-label">Incident Type</label>
                    <select class="form-select" name="incident_type" required>
                        <option selected disabled value="">Select a type</option>
                        <option value="Driver Misconduct">Driver Misconduct</option>
                        <option value="Overcharging">Overcharging</option>
                        <option value="Vehicle Condition">Vehicle Condition</option>
                        <option value="Harassment">Harassment</option>
                        <option value="Late Arrival / Delay">Late Arrival / Delay</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Date & Time of Incident</label>
                    <input type="datetime-local" class="form-control" name="incident_datetime" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Incident Location</label>
                    <input type="text" class="form-control" name="incident_location" required>
                </div>

                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label class="form-label">Bus Number</label>
                        <input type="text" class="form-control" name="bus_number" placeholder="e.g. NB-3412" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Route</label>
                        <input type="text" class="form-control" name="route" placeholder="e.g. Matara to Badulla" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="4" placeholder="Describe what happened in detail..." required></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Optional Attachment</label>
                    <input type="file" class="form-control" name="attachment" accept=".jpg,.jpeg,.png,.pdf">
                    <div class="note mt-1">You may upload image or document evidence.</div>
                </div>

                <button type="submit" class="btn btn-custom" style="background-color: #800000; color: white;">Submit Incident</button>
            </form>
        </div>

        <div class="form-section">
            <h3>📋 Track Your Reported Incidents</h3>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Tracking ID</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Bus No</th>
                            <th>Route</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Attachment</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Check if there are no incidents reported
                        if (count($incidentRows) === 0) {
                            echo "<tr><td colspan='9' class='text-center'>No incidents reported yet.</td></tr>";
                        } else {
                            $i = 1; // Initialize row counter
                            // Loop through each fetched incident to display in the table
                            foreach ($incidentRows as $incident) {
                                // Determine the CSS class for the status pill (e.g., 'Pending', 'InProgress')
                                $statusClass = ucfirst(str_replace(' ', '', $incident['status']));
                                // Prepare attachment link or "N/A" if no attachment
                                $attachmentLink = $incident['attachment']
                                    ? "<a class='attachment-link' href='{$incident['attachment']}' target='_blank'>View</a>"
                                    : "N/A";

                                // Output table row with incident data
                                echo "<tr>
                                    <td>{$i}</td>
                                    <td>{$incident['tracking_id']}</td>
                                    <td>" . date('Y-m-d H:i', strtotime($incident['incident_datetime'])) . "</td>
                                    <td>{$incident['incident_type']}</td>
                                    <td>{$incident['bus_number']}</td>
                                    <td>{$incident['route']}</td>
                                    <td>{$incident['location']}</td>
                                    <td><span class='status-pill {$statusClass}'>{$incident['status']}</span></td>
                                    <td>{$attachmentLink}</td>
                                </tr>";
                                $i++; // Increment row counter
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php
    // Include the common footer for the page
    include 'Footer.php';
    ?>
</body>
</html>