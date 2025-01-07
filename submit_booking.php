<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Booking</title>
    <script src="sweetalert2.js"></script>
</head>

<body>
    <?php
    session_start();


    include 'config.php';

    // Assuming user_id is stored in session after successful login
    $user_id = $_SESSION['id']; // Update this with the appropriate session variable for user ID

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $fullname = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['number'];
        $accommodation_type = $_POST['accommodationType'];
        $checkin = $_POST['checkin'];
        $checkout = $_POST['checkout'];
        $guest = $_POST['guests'];
        $total_price = $_POST['price'];
        $status = "pending";
        $paystatus = "unpaid";

        // Insert booking directly
        $stmt_insert = $con->prepare("INSERT INTO bookings (user_id, full_name, email, phone, payment_status, room, check_in_date, check_out_date, guest, price, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt_insert->bind_param("issssssssis", $user_id, $fullname, $email, $phone, $paystatus, $accommodation_type, $checkin, $checkout, $guest, $total_price, $status);

        if ($stmt_insert->execute()) {
            $_SESSION['message'] = 'Booking submitted successfully!';
            echo "<script>
            Swal.fire({
                title: 'Success!',
                text: 'Your booking has been successfully submitted.',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'newpage.php';
                }
            });
          </script>";
        } else {
            $_SESSION['error'] = 'Error: Failed to insert booking.';
            echo "<script>
            Swal.fire({
                title: 'Error!',
                text: 'An error occurred. Please try again.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
          </script>";
        }

        $stmt_insert->close();
        $con->close();
    }
    ?>
</body>

</html>