<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reservation Summary</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

    <h2 class="text-center mb-5">Reservation Overview</h2>

    <div class="row g-4">
        <!-- Booking Details Card -->
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Booking Details</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Seat</th>
                                    <th>Name</th>
                                    <th>NIC</th>
                                    <th>Phone</th>
                                    <th>Gender</th>
                                    <th>Booked At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>101</td>
                                    <td>A1</td>
                                    <td>Devindi</td>
                                    <td>993456789V</td>
                                    <td>0712345678</td>
                                    <td>Female</td>
                                    <td>2025-07-14 10:23 AM</td>
                                </tr>
                                <tr>
                                    <td>102</td>
                                    <td>B3</td>
                                    <td>Thilina</td>
                                    <td>993452312V</td>
                                    <td>0771234567</td>
                                    <td>Male</td>
                                    <td>2025-07-14 11:45 AM</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Details Card -->
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Payment Details</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>NIC</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Paid At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>P201</td>
                                    <td>Devindi</td>
                                    <td>993456789V</td>
                                    <td>Rs. 350.00</td>
                                    <td>Card</td>
                                    <td>2025-07-14 10:25 AM</td>
                                </tr>
                                <tr>
                                    <td>P202</td>
                                    <td>Thilina</td>
                                    <td>993452312V</td>
                                    <td>Rs. 420.00</td>
                                    <td>Cash</td>
                                    <td>2025-07-14 11:50 AM</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
