<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Booking</title>
    <script src="sweetalert2.js"></script>
</head>

<body>
<?php
session_start();

include '../components/connect.php'; // Assuming this establishes a PDO connection in $conn

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $accommodation_type = $_POST['accommodationType'];
    $checkin = $_POST['checkin'];
    $checkout = $_POST['checkout'];
    $guest = $_POST['guest'];
    $total_price = $_POST['price'];
    $status = $_POST['status'];
    $payment_status = $_POST['payment_status'];

    if ($payment_status === "paid" && $status === "Confirmed") {
        $current_date = strtotime($checkin);
        $end_date = strtotime($checkout);

        $conn->beginTransaction();

        try {
            while ($current_date < $end_date) {
                $date = date("Y-m-d", $current_date);

                $stmt_fetch = $conn->prepare("SELECT available_rooms FROM room_inventory WHERE accommodation_type = ? AND date = ?");
                $stmt_fetch->execute([$accommodation_type, $date]);
                $available_rooms = $stmt_fetch->fetchColumn();

                if ($available_rooms > 0) {
                    $new_available_rooms = $available_rooms - 1;

                    $stmt_update = $conn->prepare("UPDATE room_inventory SET available_rooms = ? WHERE accommodation_type = ? AND date = ?");
                    $stmt_update->execute([$new_available_rooms, $accommodation_type, $date]);
                } else {
                    throw new Exception("No available rooms for date: $date");
                }

                $current_date = strtotime("+1 day", $current_date);
            }

            // Update the existing booking
            $stmt_update_booking = $conn->prepare("UPDATE bookings SET full_name = ?, email = ?, phone = ?, payment_status = ?, room = ?, check_in_date = ?, check_out_date = ?, guest = ?, price = ?, status = ? WHERE email = ? AND phone = ? AND check_in_date = ?");
            $stmt_update_booking->execute([$fullname, $email, $phone, $payment_status, $accommodation_type, $checkin, $checkout, $guest, $total_price, $status, $email, $phone, $checkin]);

            $conn->commit();

            echo "<script>
            Swal.fire({
                title: 'Success!',
                text: 'Your booking has been successfully updated.',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'bookings.php';
                }
            });
            </script>";
        } catch (Exception $e) {
            $conn->rollBack();
            echo "<script>
            Swal.fire({
                title: 'Error!',
                text: 'An error occurred. Please try again.',
                icon: 'error',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'bookings.php';
                }
            });
            </script>";
        }
    } else {
        // Update booking without changing room inventory
        $stmt_update_booking = $conn->prepare("UPDATE bookings SET full_name = ?, email = ?, phone = ?, payment_status = ?, room = ?, check_in_date = ?, check_out_date = ?, guest = ?, price = ?, status = ? WHERE email = ? AND phone = ? AND check_in_date = ?");
        if ($stmt_update_booking->execute([$fullname, $email, $phone, $payment_status, $accommodation_type, $checkin, $checkout, $guest, $total_price, $status, $email, $phone, $checkin])) {
            echo "<script>
            Swal.fire({
                title: 'Success!',
                text: 'Your booking has been updated.',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'bookings.php';
                }
            });
            </script>";
        } else {
            echo "<script>
            Swal.fire({
                title: 'Error!',
                text: 'Failed to update your booking.',
                icon: 'error',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'bookings.php';
                }
            });
            </script>";
        }
    }
}
?>

</body>

</html>
