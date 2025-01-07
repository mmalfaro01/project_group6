<?php

include '../components/connect.php';

if(isset($_COOKIE['admin_id'])){
   $admin_id = $_COOKIE['admin_id'];
}else{
   $admin_id = '';
   header('location:login.php');
}

if(isset($_POST['delete'])){

   $delete_id = $_POST['delete_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

   $verify_delete = $conn->prepare("SELECT * FROM `bookings` WHERE id = ?");
   $verify_delete->execute([$delete_id]);

   if($verify_delete->rowCount() > 0){
      $delete_bookings = $conn->prepare("DELETE FROM `bookings` WHERE id = ?");
      $delete_bookings->execute([$delete_id]);
      $success_msg[] = 'Booking deleted!';
   }else{
      $warning_msg[] = 'Booking deleted already!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Bookings</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">
    <link rel="icon" href="../../images/favicon4.png" type="image/png">
    <style>

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            z-index: 10000;
            align-items: center;
            padding: 1rem;
        }

        .modal-overlay.active {
         display: flex;
        }

        .modal {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            width: 100%;
            max-width: 600px;
            box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .modal-title {
            font-size: 1.5rem;
            font-weight: 600;
            text-align: center;
            color: #1e293b;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #64748b;
            cursor: pointer;
            padding: 0.5rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .form-group {
            margin-bottom: .1rem;
        }

        .form-group.full-width {
            grid-column: span 2;
        }

        /* label {
            display: block;
            margin-bottom: 0.2rem;
            color: #475569;
            font-weight: 500;
        } */

        input,
        select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            font-size: 1rem;
            color: #1e293b;
            transition: border-color 0.2s;
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .submit-btn {
            background-color: #2563eb;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            font-weight: 500;
            width: 100%;
            margin-top: 1rem;
            transition: background-color 0.2s;
        }

        .submit-btn:hover {
            background-color: #1d4ed8;
        }

    </style>
</head>
<body>
   
<!-- header section starts  -->
<?php include '../components/admin_header.php'; ?>
<!-- header section ends -->

<!-- bookings section starts  -->

<section class="grid">

   <h1 class="heading">bookings</h1>

   <div class="box-container">

   <?php
      $select_bookings = $conn->prepare("SELECT * FROM `bookings`");
      $select_bookings->execute();
      if($select_bookings->rowCount() > 0){
         while($fetch_bookings = $select_bookings->fetch(PDO::FETCH_ASSOC)){
   ?>
   <div class="box">
      <p>booking id : <span><?= $fetch_bookings['id']; ?></span></p>
      <p>name : <span><?= $fetch_bookings['full_name']; ?></span></p>
      <p>email : <span><?= $fetch_bookings['email']; ?></span></p>
      <p>number : <span><?= $fetch_bookings['phone']; ?></span></p>
      <p>check in : <span><?= $fetch_bookings['check_in_date']; ?></span></p>
      <p>check out : <span><?= $fetch_bookings['check_out_date']; ?></span></p>
      <p>rooms : <span><?= $fetch_bookings['room']; ?></span></p>
      <p>adults : <span><?= $fetch_bookings['guest']; ?></span></p>
      <p>payment_status : <span><?= $fetch_bookings['payment_status']; ?></span></p>
      <p>status : <span><?= $fetch_bookings['status']; ?></span></p>

      <button class="btn"
                                        type="button"
                                        data-id="<?php echo $fetch_bookings['id']; ?>"
                                        data-fullname="<?php echo htmlspecialchars($fetch_bookings['full_name']); ?>"
                                        data-email="<?php echo htmlspecialchars($fetch_bookings['email']); ?>"
                                        data-phone="<?php echo htmlspecialchars($fetch_bookings['phone']); ?>"
                                        data-type="<?php echo htmlspecialchars($fetch_bookings['room']); ?>"
                                        data-guest="<?php echo htmlspecialchars($fetch_bookings['guest']); ?>"
                                        data-checkin="<?php echo htmlspecialchars($fetch_bookings['check_in_date']); ?>"
                                        data-checkout="<?php echo htmlspecialchars($fetch_bookings['check_out_date']); ?>"
                                        data-price="<?php echo htmlspecialchars($fetch_bookings['price']); ?>"
                                        data-paystatus="<?php echo htmlspecialchars($fetch_bookings['payment_status']); ?>"
                                        data-status="<?php echo htmlspecialchars($fetch_bookings['status']); ?>"
                                        onclick="openModal(this)">
                                        Edit Booking
                                    </button>

      <form action="" method="POST">
            
         <input type="hidden" name="delete_id" value="<?= $fetch_bookings['id']; ?>">
         <input type="submit" value="delete booking" onclick="return confirm('delete this booking?');" name="delete" class="btn">
      </form>
   </div>
   <?php
      }
   }else{
   ?>
   <div class="box" style="text-align: center;">
      <p>no bookings found!</p>
      <a href="dashboard.php" class="btn">go to home</a>
   </div>
   <?php
      }
   ?>

   </div>
   <div class="modal-overlay" id="modalOverlay">
        <div class="modal">
            <div class="modal-header">
                <h2 class="modal-title">Edit Booking</h2>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <form action="submit_booking.php" method="POST">
                <div class="form-grid">
                 
                    <div class="form-group">
                        <input type="hidden" name="id" id="serviceId">

                        <label for="accommodationType">Accommodation Type</label>
                         <select name="accommodationType" class="input" required id="accommodationType">
               <option value="royal-palm-ac">Royal Palm Room (2 persons Aircondition) - ₱1,500</option>
               <option value="royal-palm-fan">Royal Palm Room (2 persons Fan) - ₱1,000</option>
               <option value="bayfront-5">Bayfront Room 5 (2 persons) - ₱1,500</option>
               <option value="bayfront-6">Bayfront Room 6 (4 persons) - ₱2,500</option>
               <option value="suite-7-2">Suite Room 7 (2 persons) - ₱1,800</option>
               <option value="suite-7-4">Suite Room 7 (4 persons) - ₱3,500</option>
               <option value="suite-8-2">Suite Room 8 (2 persons) - ₱1,800</option>
               <option value="suite-8-4">Suite Room 8 (4 persons) - ₱3,500</option>
            </select>
                    </div>
                    <div class="form-group">
                        <label for="guests">Number of Guests</label>
                        <input type="number" id="guest" min="1" max="99999" required name="guest">
                    </div>
                    <div class="form-group">
                        <label for="checkin">Check-in Date</label>
                        <input type="date" id="checkin" required name="checkin">
                    </div>
                    <div class="form-group">
                        <label for="checkout">Check-out Date</label>
                        <input type="date" id="checkout" required name="checkout">
                    </div>
                    <div class="form-group">
                        <label for="fullName">Full Name</label>
                        <input type="text" id="fullName" required name="fullname">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" required name="email">
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="tel" id="phone" required name="phone">
                    </div>
                    <div class="form-group">
                        <label for="price">Price</label>
                        <input type="number" id="price" readonly value="0" name="price">
                    </div>
                    <div class="form-group">
                        <label for="payment">Payment Status</label>
                        <select id="payment" required name="payment_status">
                            <option value="" hidden>Select payment status</option>
                            <option value="paid">Paid</option>
                            <option value="unpaid">UnPaid</option>
                        </select>
                    </div>
                     <div class="form-group">
                        <label for="payment"> Status</label>
                        <select id="status" required name="status">
                            <option value="" hidden>Select status</option>
                            <option value="Confirmed">Confirmed</option>
                            <option value="Pending">Pending</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="submit-btn">Confirm Booking</button>
            </form>
        </div>
    </div>


</section>

<!-- bookings section ends -->

<script>
     function openModal(button) {
            document.getElementById('modalOverlay').classList.add('active');

            const id = button.getAttribute("data-id");
            const fullname = button.getAttribute("data-fullname");
            const email = button.getAttribute("data-email");
            const phone = button.getAttribute("data-phone");
            const type = button.getAttribute("data-type");
            const guest = button.getAttribute("data-guest");
            const checkin = button.getAttribute("data-checkin");
            const checkout = button.getAttribute("data-checkout");
            const price = button.getAttribute("data-price");
            const paystatus = button.getAttribute("data-paystatus");
            const status = button.getAttribute("data-status");

            // Populate modal fields
            document.getElementById("serviceId").value = id;
            document.getElementById("accommodationType").value = type;
            document.getElementById("guest").value = guest;
            document.getElementById("checkin").value = checkin;
            document.getElementById("checkout").value = checkout;
            document.getElementById("fullName").value = fullname;
            document.getElementById("email").value = email;
            document.getElementById("phone").value = phone;
            document.getElementById("price").value = price;
            document.getElementById("payment").value = paystatus;
            document.getElementById("status").value = status;
        }

        function closeModal() {
            document.getElementById('modalOverlay').classList.remove('active');
        }

        // Close modal when clicking outside
        document.getElementById('modalOverlay').addEventListener('click', function(event) {
            if (event.target === this) {
                closeModal();
            }
        });
</script>

<!-- custom js file link  -->
<script src="../js/admin_script.js"></script>

<?php include '../components/message.php'; ?>

</body>
</html>