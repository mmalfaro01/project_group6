<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking</title>
    <script src="sweetalert2.js"></script>
</head>

<body>
    <?php
    include "config.php";
    
    if (!$con) {
        die('Database connection error: ' . mysqli_connect_error());
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get the posted values
        if (!isset($_POST['rooms'], $_POST['checkin'], $_POST['checkout'])) {
            echo '<script>Swal.fire({icon: "error", title: "Missing Data", text: "Please provide all required information."}).then(() => { window.location.href = "index.php"; });</script>';
            exit;
        }
        
        $accommodationType = $_POST['rooms'];
        $checkin = $_POST['checkin'];
        $checkout = $_POST['checkout'];

        // Validate check-in and check-out dates
        if (strtotime($checkout) <= strtotime($checkin)) {
    ?>
            <script>
                Swal.fire({
                    icon: "error",
                    title: "Invalid Dates",
                    text: "The check-out date must be later than the check-in date. Please select a valid date range.",
                }).then(() => {
                    window.location.href = "index.php";
                });
            </script>
            <?php
            exit;
        }

        // Set timezone
        

        // Check availability for the selected date range
        $sql = "SELECT date, available_rooms FROM room_inventory WHERE accommodation_type = ? AND date BETWEEN ? AND ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("sss", $accommodationType, $checkin, $checkout);

        if (!$stmt->execute()) {
            ?>
            <script>
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "There was an issue checking room availability. Please try again or contact support if the problem persists.",
                }).then(() => {
                    window.location.href = "index.php";
                });
            </script>
            <?php
            exit;
        }

        $result = $stmt->get_result();
        $isAvailable = true;

        while ($row = $result->fetch_assoc()) {
            if ($row['available_rooms'] <= 0) {
                $isAvailable = false;
                break;
            }
        }

        if ($isAvailable) {
            $nights = (strtotime($checkout) - strtotime($checkin)) / (60 * 60 * 24);
            ?>
            <script>
                Swal.fire({
                    icon: "success",
                    title: "Room Available",
                    html: `
                        <h2>Booking Summary</h2>
                        <p><strong>Accommodation Type:</strong> <?php echo htmlspecialchars($accommodationType); ?></p>
                        <p><strong>Check-in:</strong> <?php echo htmlspecialchars($checkin); ?></p>
                        <p><strong>Check-out:</strong> <?php echo htmlspecialchars($checkout); ?></p>
                    `,
                    confirmButtonText: "OKAY"
                }).then(() => {
                    window.location.href = "index.php";
                });
            </script>
        <?php
        } else {
        ?>
            <script>
                Swal.fire({
                    icon: "error",
                    title: "No Rooms Available",
                    text: "Unfortunately, there are no rooms available for the selected dates. Please choose different dates and try again.",
                }).then(() => {
                    window.location.href = "index.php";
                });
            </script>
    <?php
        }

        $stmt->close();
        $con->close();
    }
    ?>
</body>

</html>
