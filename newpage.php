<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   setcookie('user_id', create_unique_id(), time() + 60*60*24*30, '/');
   header('location:index.php');
}

if(isset($_POST['check'])){

   $check_in = $_POST['check_in'];
   $check_in = filter_var($check_in, FILTER_SANITIZE_STRING);

   $total_rooms = 0;

   $check_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE check_in = ?");
   $check_bookings->execute([$check_in]);

   while($fetch_bookings = $check_bookings->fetch(PDO::FETCH_ASSOC)){
      $total_rooms += $fetch_bookings['rooms'];
   }

   // if the hotel has total 30 rooms 
   if($total_rooms >= 30){
      $warning_msg[] = 'rooms are not available';
   }else{
      $success_msg[] = 'rooms are available';
   }

}


if(isset($_POST['send'])){

   $id = create_unique_id();
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $message = $_POST['message'];
   $message = filter_var($message, FILTER_SANITIZE_STRING);

   $verify_message = $conn->prepare("SELECT * FROM `messages` WHERE name = ? AND email = ? AND number = ? AND message = ?");
   $verify_message->execute([$name, $email, $number, $message]);

   if($verify_message->rowCount() > 0){
      $warning_msg[] = 'message sent already!';
   }else{
      $insert_message = $conn->prepare("INSERT INTO `messages`(id, name, email, number, message) VALUES(?,?,?,?,?)");
      $insert_message->execute([$id, $name, $email, $number, $message]);
      $success_msg[] = 'message send successfully!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Casa Royal Beach Front</title>

   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">
   <link rel="icon" href="images/favicon4.png" type="image/png">
   <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>


</head>
<body>

<?php include 'components/user_header_new.php'; ?>

<!-- home section starts  -->

<section class="home" id="home">

   <div class="swiper home-slider">

      <div class="swiper-wrapper">

         <div class="box swiper-slide">
            <img src="images/casa-img-1.png" alt="">
            <div class="flex">
               <h3>Aerial Beach Front Panorama</h3>
               <a href="#availability" class="btn">check availability</a>
            </div>
         </div>

         <div class="box swiper-slide">
            <img src="images/casa-img-2.jpg" alt="">
            <div class="flex">
               <h3>Garden Patio by the Sea</h3>
               <a href="#reservation" class="btn">make a reservation</a>
            </div>
         </div>

         <div class="box swiper-slide">
            <img src="images/casa-img-3.jpg" alt="">
            <div class="flex">
               <h3>Tropical Terrace and Stairway</h3>
               <a href="#contact" class="btn">contact us</a>
            </div>
         </div>
         <div class="box swiper-slide">
                    <img src="images/casa-img-4.jpg" alt="">
                    <div class="flex">
                        <h3>Coastal Serenity</h3>
                        <a href="#contact" class="btn">contact us</a>
                    </div>
                </div>

                <div class="box swiper-slide">
                    <img src="images/casa-img-5.jpg" alt="">
                    <div class="flex">
                        <h3>Ocean View Terrace</h3>
                        <a href="#contact" class="btn">contact us</a>
                    </div>
                </div>
      </div>

      <div class="swiper-button-next"></div>
      <div class="swiper-button-prev"></div>

   </div>

</section>

<!-- home section ends -->

<!-- availability section starts  -->

<section class="availability" id="availability">

  <!-- <form action="bookings.php" method="post">-->
   <form action="check_availability.php" method="POST">
      <div class="flex">
         <div class="box">
            <p>check in <span>*</span></p>
            <input type="date" name="checkin" class="input" required>
         </div>
         <div class="box">
            <p>check out <span>*</span></p>
            <input type="date" name="checkout" class="input" required>
         </div>
         <div class="box">
            <p>adults <span>*</span></p>
            <select name="adults" class="input" required>
               <option value="1">1 adult</option>
               <option value="2">2 adults</option>
               <option value="3">3 adults</option>
               <option value="4">4 adults</option>
               <option value="5">5 adults</option>
               <option value="6">6 adults</option>
            </select>
         </div>
         
         <div class="box">
            <p>rooms <span>*</span></p>
            <select name="rooms" class="input" required>
               <option value="Royal Palm Room (2 persons)">Royal Palm Room </option>
               <option value="Bayfront Room 5 (2 persons)">Bayfront Room 5 </option>
               <option value="Bayfront Room 6 (4 persons)">Bayfront Room 6 </option>
               <option value="Suite Room 7 (4 persons)</">Suite Room 7 </option>
               <option value="Suite Room 8 (4 persons)">Suite Room 8 </option>
            </select>
         </div>
      </div>
      <input type="submit" value="check availability" name="check" class="btn">
   </form>

</section>

<!-- availability section ends -->

<!-- about section starts  -->

<section class="about" id="about">

   <div class="row">
            <div class="content">
                <h3>About</h3>
                <p>
                    Casa Royal Beach Front is a Mediterranean-inspired
                    beach resort offering a perfect blend of style and
                    tropical charm. Located just 5 minutes from Siquijor
                    Port, the resort features spacious accommodations
                    with serene garden or stunning sea views, providing
                    an idyllic escape to white sand beaches and
                    turquoise waters.
                </p>
            </div>
            <div id="map" style="width: 100%; height: 300px;"></div>

        </div>
        </div>


   <<div class="row">
            <div class="image">
                <img src="images/rp-img-1.jpg" alt="">
            </div>
            <div class="content">
                <h3>Royal Palm Room</h3>
                <p>
                    Good for two (2) Persons<br>
                    Rate: Php 1,500 - Airconditioned<br>
                    Rate: Php 1,000 - Fan Room<br>
                    Php 300 - Additional Person<br>
                    <br>
                    Private Bathroom<br>
                    Hot & Cold Shower<br>
                    Toiletries
                </p>
                <a href="#reservation" class="btn">make a reservation</a>
            </div>
        </div>

        <div class="row revers">
            <div class="image">
                <img src="images/BayfrontRoom5-img-1.png" alt="">
            </div>
            <div class="content">
                <h3>Bayfront Room 5</h3>
                <p>
                    Good for Two (2) Persons<br>
                    Rate: Php 1,500<br>
                    Php 300 - Additional Person<br>
                    <br>
                    Airconditioned<br>
                    Private Bathroom<br>
                    Hot & Cold Shower<br>
                    Toiletries<br>
                </p>
                <a href="#reservation" class="btn">make a reservation</a>
            </div>
        </div>

        <div class="row">
            <div class="image">
                <img src="images/BayfrontRoom6-img-1.png" alt="">
            </div>
            <div class="content">
                <h3>Bayfront Room 6</h3>
                <p>
                    Good for four (4) Persons<br>
                    Rate: Php 2,500<br>
                    Php 300 - Additional Person<br>
                    <br>
                    Airconditioned<br>
                    Private Bathroom<br>
                    Hot & Cold Shower<br>
                    Toiletries
                </p>
                <a href="#reservation" class="btn">make a reservation</a>
            </div>
        </div>

        <div class="row revers">
            <div class="image">
                <img src="images/SuiteRoom7-img-1.png" alt="">
            </div>
            <div class="content">
                <h3>Suite Room 7</h3>
                <p>
                    Good for four (4) Persons<br>
                    Rate: Php 3,500<br>
                    Good for two (2) Persons<br>
                    Rate: Php 1,800<br>
                    Php 300 - Additional Person<br>
                    <br>
                    Airconditioned<br>
                    Private Bathroom<br>
                    Hot & Cold Shower<br>
                    Toiletries<br>
                    Ocean-view balcony<br>
                </p>
                <a href="#reservation" class="btn">make a reservation</a>
            </div>
        </div>

        <div class="row">
            <div class="image">
                <img src="images/SuiteRoom8-img-1.png" alt="">
            </div>
            <div class="content">
                <h3>Suite Room 8</h3>
                <p>
                    Good for four (4) Persons<br>
                    Rate: Php 3,500<br>
                    Good for two (2) Persons<br>
                    Rate: Php 1,800<br>
                    Php 300 - Additional Person<br>
                    <br>
                    Airconditioned<br>
                    Private<br>
                    Bathroom<br>
                    Hot & Cold<br>
                    Shower<br>
                    Toiletries<br>
                    Ocean-view<br>
                    balcony<br>
                </p>
                <a href="#reservation" class="btn">make a reservation</a>
            </div>
        </div>

</section>

<!-- about section ends -->

<!-- services section starts  -->

<section class="services">

   <div class="box-container">

      <div class="box">
         <img src="images/icon-1.png" alt="">
         <h3>food & drinks</h3>
         <p>Savor the flavor, indulge in delight!</p>
      </div>

      <div class="box">
         <img src="images/icon-2.png" alt="">
         <h3>outdoor dining</h3>
         <p>Gather 'round, dine outside!</p>
      </div>

      <div class="box">
         <img src="images/icon-3.png" alt="">
         <h3>beach view</h3>
         <p>Sunset serenade by the sea.</p>
      </div>

      <div class="box">
         <img src="images/icon-4.png" alt="">
         <h3>decorations</h3>
         <p>Mediterranean magic, every time.</p>
      </div>

      <div class="box">
         <img src="images/icon-5.png" alt="">
         <h3>oceanfront swimming</h3>
         <p>Splish, splash, fun dash!</p>
      </div>

      <div class="box">
         <img src="images/icon-6.png" alt="">
         <h3>resort beach</h3>
         <p>Where sea meets soul.</p>
      </div>

   </div>

</section>

<!-- services section ends -->

<!-- reservation section starts  -->

<section class="reservation" id="reservation">
   <form action="submit_booking.php" method="POST">
      <h3>MAKE A RESERVATION</h3>
      <div class="flex">
         <div class="box">
            <p>FULL NAME <span>*</span></p>
            <input type="text" name="name"required placeholder="enter your name" class="input">
         </div>
         <div class="box">
            <p>EMAIL ADDRESS <span>*</span></p>
            <input type="email" name="email"  required placeholder="enter your email" class="input">
         </div>
         <div class="box">
            <p>CONTACT NUMBER <span>*</span></p>
            <input type="number" name="number" maxlength="10" min="1" max="9999999999" required placeholder="enter your number" class="input">
         </div>
         <div class="box">
            <p>ROOMS <span>*</span></p>
            <select name="accommodationType" class="input" required>
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
         <div class="box">
            <p>CHECK IN <span>*</span></p>
            <input type="date" name="checkin" class="input" required>
         </div>
         <div class="box">
            <p>CHECK OUT <span>*</span></p>
            <input type="date" name="checkout" class="input" required>
         </div>
         <div class="box">
            <p>GUESTS <span>*</span></p>
            <input type="number" name="guests" maxlength="10" min="1" required class="input">
         </div>
         <div class="box">
            <p>PRICE <span>*</span></p>
            <input type="number" name="price" readonly class="input">
         </div>
      </div>
      <input type="submit" value="book now" class="btn">
      
   </form>
</section>

<!-- reservation section ends -->

<!-- gallery section starts  -->

<section class="gallery" id="gallery">

   <div class="swiper gallery-slider">
      <div class="swiper-wrapper">
         <img src="images/gallery-img-1.jpg" class="swiper-slide" alt="">
         <img src="images/gallery-img-2.jpg" class="swiper-slide" alt="">
         <img src="images/gallery-img-3.jpg" class="swiper-slide" alt="">
         <img src="images/gallery-img-4.jpg" class="swiper-slide" alt="">
         <img src="images/gallery-img-5.jpg" class="swiper-slide" alt="">
         <img src="images/gallery-img-6.jpg" class="swiper-slide" alt="">
         <img src="images/gallery-img-7.jpg" class="swiper-slide" alt="">
         <img src="images/gallery-img-8.jpg" class="swiper-slide" alt="">
         <img src="images/gallery-img-9.jpg" class="swiper-slide" alt="">
        <img src="images/gallery-img-10.jpg" class="swiper-slide" alt="">
       <img src="images/gallery-img-11.jpg" class="swiper-slide" alt="">
       <img src="images/gallery-img-12.jpg" class="swiper-slide" alt="">
      </div>
      <div class="swiper-pagination"></div>
   </div>

</section>

<!-- gallery section ends -->

<!-- contact section starts  -->

<section class="contact" id="contact">

   <div class="row">

      <form action="" method="post">
         <h3>send us message</h3>
         <input type="text" name="name" required maxlength="50" placeholder="enter your name" class="box">
         <input type="email" name="email" required maxlength="50" placeholder="enter your email" class="box">
         <input type="number" name="number" required maxlength="10" min="0" max="9999999999" placeholder="enter your number" class="box">
         <textarea name="message" class="box" required maxlength="1000" placeholder="enter your message" cols="30" rows="10"></textarea>
         <input type="submit" value="send message" name="send" class="btn">
      </form>

      <div class="faq">
         <h3 class="title">frequently asked questions</h3>
         <div class="box active">
            <h3>how to cancel?</h3>
            <p>
               <br> 1. Visit to your account on our website.
               <br>2. Go to 'My Bookings' and select the relevant reservation.
              <br> 3. Click 'Cancel Booking.'
               <br>4. Confirm cancellation.
               <br>5. Our team will contact you to finalize.
               <br>
               <br>- 72 hours or more prior to arrival: Full refund
               <br>- 48-72 hours prior to arrival: 50% refund
               <br>- Less than 48 hours prior to arrival: No refund

               <br>To initiate cancellation, please:
               <br>- Call us: 0966 055 6429
               <br>- Email: casaroyalbeachfrontsiquijor@gmail.com
              <br> - Live chat: available on our website

               <br>Please provide your booking reference number and cancellation reason. We'll process your request promptly.

               </p>
         </div>
         <div class="box">
            <h3>is there any vacancy?</h3>
            <p>Check our website's booking engine for real-time availability and rates.
               <br>Call us: 0966 055 6429
               <br>- Email: casaroyalbeachfrontsiquijor@gmail.com
              <br> - Live chat: available on our website</p>
         </div>
         <div class="box">
            <h3>what are payment methods?</h3>
            <p>Online
            <br>Payment options: Credit/Debit cards, PayPal, Bank Transfer.
            <br>Offline
            <br>Bank Transfer: Wire transfer (international)
            <br>
            Please note:
                <br>No hidden fees: All prices include taxes.
                <br>Payment terms: Full payment due upon booking.
         </p>
         </div>
         <div class="box">
            <h3>how to claim coupons codes?</h3>
            <p>To claim your coupon code:
                 <br> 1. Log in to your account or continue as guest.
                  <br>2. Enter travel dates and room selection.
                  <br>3. Click 'Book Now' or 'Proceed to Checkout.'
                  <br>4. Enter coupon code in the 'Discount Code' field.
                  <br>5. Click 'Apply' to receive discount.</p>
         </div>
         <div class="box">
            <h3>what are the age requirements?</h3>
            <p>Our services are available to individuals 18 years and older.
               <br>Guests under 18 must be accompanied by an adult.</p>
         </div>
      </div>

   </div>

</section>

<!-- contact section ends -->

<!-- reviews section starts  -->

<section class="reviews" id="reviews">

   <div class="swiper reviews-slider">

      <div class="swiper-wrapper">
         <div class="swiper-slide box">
            <img src="images/sample7.jpg" alt="">
            <h3>Anne Siangco</h3>
            <p>Casaroyal Beach Siquijor is a place that I have seen people write favorable reviews about. I also feel that it wouldn’t disappoint me. With that said, I have booked my first stay at Agoda, a two nights and three days stay. I went ahead booking a standard room which I found to be well kept and nice. The room had a bathroom and cabinets which were clean too. The manager of the place Kim was very nice and helpful as well and even brought us some tasty carbonara. Thank you very much. Our stay was really nice during the time we spent there.</p>
         </div>
         <div class="swiper-slide box">
            <img src="images/sample.jpg" alt="">
            <h3>charlou laurente</h3>
            <p>Just left Casa Royal Beachfront and I'm already missing the sun, sand, and serenity!</p>
         </div>
         <div class="swiper-slide box">
            <img src="images/sample1.jpg" alt="">
            <h3>ligones, charlie</h3>
            <p>Yown oh! gwapaha nimo Casa Royal Beach Front!</p>
         </div>
         <div class="swiper-slide box">
            <img src="images/sample3.jpg" alt="">
            <h3>marc cedrick laurie</h3>
            <p>Ganda mo Casa Royal Beach Front, deserve ko ulit mag Siquijor trip!</p>
         </div>
         <div class="swiper-slide box">
            <img src="images/sample4.jpg" alt="">
            <h3>michelle macahis</h3>
            <p>Nalingaw jud ko sa Casa very formal ug hospitable ang mga staff labi na ang manager!</p>
         </div>
         <div class="swiper-slide box">
            <img src="images/sample5.jpg" alt="">
            <h3>kyla claire liberato</h3>
            <p>Maganda po sobra! Na amaze talaga kami dahil ang babait nila <3!</p>
         </div>
         <div class="swiper-slide box">
            <img src="images/sample6.jpg" alt="">
            <h3>lopez, jason</h3>
            <p>Yey! Nasa Siquijor na kami, pero mas deserve talaga namin ang Casa Royal Beach Front , sobrang nakakabighani ang lugar at yung sunset!</p>
         </div>
      </div>

      <div class="swiper-pagination"></div>
   </div>

</section>

<!-- reviews section ends  -->





<?php include 'components/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

<!-- custom js file link  -->
<script src="js/script.js"></script>

<?php include 'components/message.php'; ?>

<script>
    // Create the map and set its initial view to specific coordinates and zoom level
    const map = L.map('map').setView([9.22325, 123.53971], 13);

    // Add OpenStreetMap tiles to the map
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Add a marker to the map
    const marker = L.marker([9.22325, 123.53971]).addTo(map);

    // Add a popup to the marker
    marker.bindPopup('<b>Hello! Casa Royal Beach Resort here.').openPopup();

    const circle = L.circle([9.22325, 123.53971], {
    color: 'red',
    fillColor: '#f03',
    fillOpacity: 0.5,
    radius: 500
}).addTo(map);

circle.bindPopup("I am a circle.");
map.on('click', function (e) {
    alert(`You clicked the map at ${e.latlng}`);
});
const customIcon = L.icon({
    iconUrl: 'path/to/custom-icon.png',
    iconSize: [32, 32]
});

L.marker([51.5, -0.09], { icon: customIcon }).addTo(map);


 // Room configuration and pricing constants
const BASE_PRICES = {
    "royal-palm-ac": 1500,
    "royal-palm-fan": 1000,
    "bayfront-5": 1500,
    "bayfront-6": 2500,
    "suite-7-2": 1800,
    "suite-7-4": 3500,
    "suite-8-2": 1800,
    "suite-8-4": 3500
};

const GUEST_LIMITS = {
    "royal-palm-ac": 2,
    "royal-palm-fan": 2,
    "bayfront-5": 2,
    "bayfront-6": 4,
    "suite-7-2": 2,
    "suite-7-4": 4,
    "suite-8-2": 2,
    "suite-8-4": 4
};

const EXTRA_GUEST_FEE = 300;

// Calculate number of nights between two dates
function calculateNights(checkin, checkout) {
    if (!checkin || !checkout) return 0;
    const checkInDate = new Date(checkin);
    const checkOutDate = new Date(checkout);
    const timeDiff = checkOutDate.getTime() - checkInDate.getTime();
    return Math.ceil(timeDiff / (1000 * 3600 * 24));
}

// Calculate total price
function calculatePrice(accommodationType, guests, checkin, checkout) {
    if (!accommodationType || !guests || !checkin || !checkout) {
        return 0;
    }

    const basePrice = BASE_PRICES[accommodationType];
    const guestLimit = GUEST_LIMITS[accommodationType];
    const nights = calculateNights(checkin, checkout);

    if (!basePrice || nights < 1) {
        return 0;
    }

    let totalPrice = basePrice * nights;

    if (guests > guestLimit) {
        const extraGuests = guests - guestLimit;
        totalPrice += (extraGuests * EXTRA_GUEST_FEE * nights);
    }

    return totalPrice;
}

// Initialize when document is ready
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.reservation form');
    const accommodationSelect = form.querySelector('[name="accommodationType"]');
    const guestsInput = form.querySelector('[name="guests"]');
    const checkinInput = form.querySelector('[name="checkin"]');
    const checkoutInput = form.querySelector('[name="checkout"]');
    const priceInput = form.querySelector('[name="price"]');

    // Set minimum date to today
    const today = new Date().toISOString().split('T')[0];
    checkinInput.min = today;

    function updatePrice() {
        const price = calculatePrice(
            accommodationSelect.value,
            parseInt(guestsInput.value) || 0,
            checkinInput.value,
            checkoutInput.value
        );
        priceInput.value = price;
    }

    function updateGuestLimit() {
        const selectedRoom = accommodationSelect.value;
        const maxGuests = GUEST_LIMITS[selectedRoom];
        guestsInput.max = maxGuests * 2;
        guestsInput.min = 1;
        guestsInput.title = `Standard capacity: ${maxGuests} guests (max ${maxGuests * 2} with extra fee)`;
    }

    // Event Listeners for real-time updates
    ['input', 'change'].forEach(eventType => {
        accommodationSelect.addEventListener(eventType, () => {
            updateGuestLimit();
            updatePrice();
        });

        guestsInput.addEventListener(eventType, updatePrice);
        checkinInput.addEventListener(eventType, () => {
            checkoutInput.min = checkinInput.value;
            if (checkoutInput.value && checkoutInput.value <= checkinInput.value) {
                checkoutInput.value = '';
            }
            updatePrice();
        });
        checkoutInput.addEventListener(eventType, updatePrice);
    });

    // Form submission


    // Initialize form
    updateGuestLimit();
    updatePrice();
});
</script>


</body>
</html>