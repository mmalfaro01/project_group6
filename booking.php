<?php
session_start(); // Start session
include "config.php";

$userId = $_SESSION['id']; // Get logged-in user's ID

// Fetch bookings for the logged-in user
$sql = "SELECT * FROM bookings WHERE user_id = ?";
$stmt = mysqli_prepare($con, $sql);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    die("Error preparing statement: " . mysqli_error($con));
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Status</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        h1 {
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }

        .booking-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        .booking-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            transition: transform 0.2s;
        }

        .booking-card:hover {
            transform: translateY(-5px);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .user-info {
            margin-bottom: 15px;
        }

        .info-row {
            display: flex;
            margin-bottom: 8px;
        }

        .info-label {
            font-weight: 600;
            width: 120px;
            color: #666;
        }

        .info-value {
            color: #333;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.85em;
            font-weight: 500;
        }

        .status-confirmed {
            background-color: #d4edda;
            color: #155724;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .payment-paid {
            color: #28a745;
        }

        .payment-pending {
            color: #ffc107;
        }

        .card-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }

        .action-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9em;
            flex: 1;
        }

        .edit-btn {
            background-color: #007bff;
            color: white;
        }

        .delete-btn {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Booking Status</h1>
        <div class="booking-grid">


            <?php while ($row = mysqli_fetch_assoc($result)) {
            ?>

                <div class="booking-card">
                    <div class="card-header">
                        <span class="status-badge status-confirmed"><?php echo htmlspecialchars($row['status']) ?></span>
                    </div>
                    <div class="user-info">
                        <div class="info-row">
                            <span class="info-label">Name:</span>
                            <span class="info-value"><?php echo htmlspecialchars($row['full_name']) ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Email:</span>
                            <span class="info-value"><?php echo htmlspecialchars($row['email']) ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Phone:</span>
                            <span class="info-value"><?php echo htmlspecialchars($row['phone']) ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Guests:</span>
                            <span class="info-value"><?php echo htmlspecialchars($row['guest']) ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Check In:</span>
                            <span class="info-value"><?php echo htmlspecialchars($row['check_in_date']) ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Check Out:</span>
                            <span class="info-value"><?php echo htmlspecialchars($row['check_out_date']) ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Price:</span>
                            <span class="info-value"><?php echo htmlspecialchars($row['price']) ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Payment Status:</span>
                            <span class="info-value payment-paid"><?php echo htmlspecialchars($row['payment_status']) ?></span>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <script>
        function editBooking(userId) {
            alert(`Edit booking ${userId}`);
        }

        function deleteBooking(userId) {
            if (confirm(`Are you sure you want to delete booking ${userId}?`)) {
                console.log(`Deleting booking ${userId}`);
            }
        }
    </script>
</body>

</html>