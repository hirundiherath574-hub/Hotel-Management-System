<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Red Lion Hotel - Luxury Beach Resort'; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<!-- Header -->
<header class="header">
    <nav class="nav">
        <div class="nav-container">
            <div class="logo">
                <div class="logo-icon">
                    <img style="width: 60px; height: 60px; object-fit: cover" src="/assets/images/logo.png" alt="Red Lion Logo">
                </div>
            </div>
            <ul class="nav-menu">
                <li><a href="/public/index.php">Home</a></li>
                <li><a href="/public/index.php#about">About</a></li>
                <li><a href="/public/index.php#rooms">Rooms</a></li>
                <li><a href="/public/index.php#facilities">Facilities</a></li>
                <li><a href="/public/index.php#contact">Contact</a></li>
                <li><a href="booking.php" class="btn-nav">Book Now</a></li>
            </ul>
            <div class="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </nav>
</header>