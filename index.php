<?php
require_once '../config/database.php';

// Fetch rooms from database
$rooms_query = "SELECT * FROM rooms ORDER BY price ASC";
$rooms_result = mysqli_query($conn, $rooms_query);

$page_title = "Red Lion Hotel - Luxury Beach Resort in Trincomalee";
include '../includes/header.php';
?>

    <!-- Hero Section -->
    <section id="home" class="hero">
        <div class="hero-content">
            <div class="hero-logo">
                <div class="main-logo">
                    <img style="width: 150px; height: 150px; object-fit: cover" src="/assets/images/logo.png" alt="Red Lion Logo">
                </div>
                <h1>RED LION</h1>
                <p class="owner-name">Hotel & Resort | Owner: Chamil Lakshan</p>
                <p class="tagline">Luxury Beach Resort in Beautiful Trincomalee</p>
            </div>
            <div class="hero-buttons">
                <a href="booking.php" class="btn btn-primary">Book Now</a>
                <a href="#contact" class="btn btn-secondary">Contact Us</a>
            </div>
        </div>
        <div class="hero-overlay"></div>
    </section>

    <!-- About Section -->
    <section id="about" class="about">
        <div class="container">
            <h2 class="section-title">About Our Hotel</h2>
            <div class="about-content">
                <div class="about-text">
                    <p>Welcome to <strong>RED LION</strong> Hotel, established in 2023 in the beautiful coastal town of Kappalthurai, Trincomalee. We offer luxurious accommodation with breathtaking views of the Indian Ocean.</p>
                    <div class="features">
                        <div class="feature">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Kappalthurai, Trincomalee</span>
                        </div>
                        <div class="feature">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Established 2023</span>
                        </div>
                        <div class="feature">
                            <i class="fas fa-clock"></i>
                            <span>Open 8AM - 10PM Daily</span>
                        </div>
                    </div>
                </div>
                <div class="about-image">
                    <div class="beach-image">
                        <img src="https://images.unsplash.com/photo-1552733407-5d5c46c3bb3b?ixlib=rb-4.0.3&auto=format&fit=crop&w=1080&q=80" alt="Red Lion Hotel Beachside Resort">
                        <div class="image-overlay">
                            <h3>Our Beachside Location</h3>
                            <p>Enjoy direct access to pristine beaches</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Rooms Section -->
    <section id="rooms" class="rooms">
        <div class="container">
            <h2 class="section-title">Our Luxury Rooms & Suites</h2>
            <div class="rooms-grid">
                <?php while($room = mysqli_fetch_assoc($rooms_result)): ?>
                    <div class="room-card">
                        <div class="room-image">
                            <img src="<?php echo $room['image_url'] ? $room['image_url'] : 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&w=800&q=80'; ?>" alt="<?php echo htmlspecialchars($room['room_type']); ?>">
                            <div class="room-overlay">
                                <h3><?php echo htmlspecialchars($room['room_type']); ?></h3>
                            </div>
                        </div>
                        <div class="room-info">
                            <p class="price">$<?php echo number_format($room['price'], 2); ?>/night</p>
                            <p><?php echo htmlspecialchars($room['description']); ?></p>
                            <ul>
                                <?php
                                $amenities = explode(',', $room['amenities']);
                                foreach($amenities as $amenity):
                                    ?>
                                    <li><i class="fas fa-check"></i> <?php echo trim(htmlspecialchars($amenity)); ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <a href="booking.php?room_id=<?php echo $room['id']; ?>" class="btn btn-primary">Book Now</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact">
        <div class="container">
            <h2 class="section-title">Contact Us</h2>
            <div class="contact-content">
                <div class="contact-info">
                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <h3>Address</h3>
                            <p>Kappalthurai, Trincomalee<br>Sri Lanka</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <div>
                            <h3>Phone</h3>
                            <p>+60 17-427 3826</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <h3>Email</h3>
                            <p>lakshchamil351@gmail.com</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-clock"></i>
                        <div>
                            <h3>Opening Hours</h3>
                            <p>8:00 AM - 10:00 PM<br>Monday - Sunday</p>
                        </div>
                    </div>
                </div>
                <div class="contact-form-box">
                    <h3>Quick Inquiry</h3>
                    <p>For booking, please use our <a href="booking.php" style="color: var(--primary);">booking page</a></p>
                    <div class="contact-buttons">
                        <a href="booking.php" class="btn btn-primary">Make a Booking</a>
                        <a href="https://wa.me/60174273826" class="btn btn-secondary">
                            <i class="fab fa-whatsapp"></i> WhatsApp Us
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php
include '../includes/footer.php';
?>