<?php
require_once '../config/database.php';

$success_message = '';
$error_message = '';
$selected_room_id = isset($_GET['room_id']) ? (int)$_GET['room_id'] : 0;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = clean_input($_POST['full_name']);
    $email = clean_input($_POST['email']);
    $phone = clean_input($_POST['phone']);
    $room_id = (int)$_POST['room_id'];
    $check_in = clean_input($_POST['check_in']);
    $check_out = clean_input($_POST['check_out']);
    $num_guests = (int)$_POST['num_guests'];
    $num_rooms = (int)$_POST['num_rooms'];
    $special_requests = clean_input($_POST['special_requests']);

    // Calculate total price
    $room_query = "SELECT price FROM rooms WHERE id = $room_id";
    $room_result = mysqli_query($conn, $room_query);
    $room = mysqli_fetch_assoc($room_result);

    $check_in_date = new DateTime($check_in);
    $check_out_date = new DateTime($check_out);
    $nights = $check_in_date->diff($check_out_date)->days;
    $total_price = $room['price'] * $nights * $num_rooms;

    // Insert booking
    $insert_query = "INSERT INTO bookings (full_name, email, phone, room_id, check_in, check_out, 
                     num_guests, num_rooms, special_requests, total_price, status) 
                     VALUES ('$full_name', '$email', '$phone', $room_id, '$check_in', '$check_out', 
                     $num_guests, $num_rooms, '$special_requests', $total_price, 'pending')";

    if (mysqli_query($conn, $insert_query)) {
        $success_message = "Booking request submitted successfully! We'll contact you soon to confirm.";
    } else {
        $error_message = "Error: " . mysqli_error($conn);
    }
}

// Fetch rooms
$rooms_query = "SELECT * FROM rooms ORDER BY price ASC";
$rooms_result = mysqli_query($conn, $rooms_query);

$page_title = "Book Your Stay - Red Lion Hotel";
include '../includes/header.php';
?>

    <style>
        .booking-page {
            padding-top: 100px;
            min-height: 100vh;
            background: var(--light);
        }

        .booking-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .booking-form {
            background: var(--white);
            padding: 3rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .booking-form h2 {
            color: var(--primary);
            margin-bottom: 2rem;
            text-align: center;
            font-family: 'Playfair Display', serif;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--dark);
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 5px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .price-info {
            background: var(--light);
            padding: 1rem;
            border-radius: 5px;
            margin: 1rem 0;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="booking-page">
        <div class="booking-container">
            <div class="booking-form">
                <h2>Book Your Stay</h2>

                <?php if ($success_message): ?>
                    <div class="alert alert-success"><?php echo $success_message; ?></div>
                <?php endif; ?>

                <?php if ($error_message): ?>
                    <div class="alert alert-error"><?php echo $error_message; ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="full_name">Full Name *</label>
                            <input type="text" id="full_name" name="full_name" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone">Phone Number *</label>
                            <input type="tel" id="phone" name="phone" required>
                        </div>

                        <div class="form-group">
                            <label for="room_id">Room Type *</label>
                            <select id="room_id" name="room_id" required>
                                <option value="">Select Room Type</option>
                                <?php
                                mysqli_data_seek($rooms_result, 0);
                                while($room = mysqli_fetch_assoc($rooms_result)):
                                    ?>
                                    <option value="<?php echo $room['id']; ?>"
                                            data-price="<?php echo $room['price']; ?>"
                                        <?php echo ($selected_room_id == $room['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($room['room_type']); ?> - $<?php echo number_format($room['price'], 2); ?>/night
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="check_in">Check-in Date *</label>
                            <input type="date" id="check_in" name="check_in" min="<?php echo date('Y-m-d'); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="check_out">Check-out Date *</label>
                            <input type="date" id="check_out" name="check_out" min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="num_guests">Number of Guests *</label>
                            <select id="num_guests" name="num_guests" required>
                                <option value="1">1 Guest</option>
                                <option value="2">2 Guests</option>
                                <option value="3">3 Guests</option>
                                <option value="4">4 Guests</option>
                                <option value="5">5+ Guests</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="num_rooms">Number of Rooms *</label>
                            <input type="number" id="num_rooms" name="num_rooms" min="1" max="10" value="1" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="special_requests">Special Requests</label>
                        <textarea id="special_requests" name="special_requests" rows="4" placeholder="Any special requirements or requests..."></textarea>
                    </div>

                    <div class="price-info" id="priceInfo" style="display: none;">
                        <strong>Estimated Total:</strong> $<span id="totalPrice">0.00</span>
                        <br>
                        <small>(<span id="nights">0</span> night(s) × <span id="roomsCount">1</span> room(s) × $<span id="pricePerNight">0</span>/night)</small>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px;">
                        Submit Booking Request
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Calculate price
        function calculatePrice() {
            const roomSelect = document.getElementById('room_id');
            const checkIn = document.getElementById('check_in').value;
            const checkOut = document.getElementById('check_out').value;
            const numRooms = parseInt(document.getElementById('num_rooms').value) || 1;

            if (roomSelect.value && checkIn && checkOut) {
                const pricePerNight = parseFloat(roomSelect.options[roomSelect.selectedIndex].dataset.price);
                const date1 = new Date(checkIn);
                const date2 = new Date(checkOut);
                const nights = Math.ceil((date2 - date1) / (1000 * 60 * 60 * 24));

                if (nights > 0) {
                    const total = pricePerNight * nights * numRooms;
                    document.getElementById('priceInfo').style.display = 'block';
                    document.getElementById('totalPrice').textContent = total.toFixed(2);
                    document.getElementById('nights').textContent = nights;
                    document.getElementById('roomsCount').textContent = numRooms;
                    document.getElementById('pricePerNight').textContent = pricePerNight.toFixed(2);
                }
            }
        }

        document.getElementById('room_id').addEventListener('change', calculatePrice);
        document.getElementById('check_in').addEventListener('change', calculatePrice);
        document.getElementById('check_out').addEventListener('change', calculatePrice);
        document.getElementById('num_rooms').addEventListener('input', calculatePrice);
    </script>

<?php include '../includes/footer.php'; ?>