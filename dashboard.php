<?php
session_start();
require_once '../../config/database.php';

// Check if logged in
if (!is_admin_logged_in()) {
    header('Location: ../login.php');
    exit();
}

// Handle booking status update
if (isset($_POST['update_status'])) {
    $booking_id = (int)$_POST['booking_id'];
    $new_status = clean_input($_POST['status']);

    $update_query = "UPDATE bookings SET status = '$new_status' WHERE id = $booking_id";
    mysqli_query($conn, $update_query);
}

// Handle booking deletion
if (isset($_POST['delete_booking'])) {
    $booking_id = (int)$_POST['booking_id'];
    $delete_query = "DELETE FROM bookings WHERE id = $booking_id";
    mysqli_query($conn, $delete_query);
}

// Fetch statistics
$total_bookings_query = "SELECT COUNT(*) as total FROM bookings";
$total_bookings = mysqli_fetch_assoc(mysqli_query($conn, $total_bookings_query))['total'];

$pending_query = "SELECT COUNT(*) as total FROM bookings WHERE status = 'pending'";
$pending_bookings = mysqli_fetch_assoc(mysqli_query($conn, $pending_query))['total'];

$confirmed_query = "SELECT COUNT(*) as total FROM bookings WHERE status = 'confirmed'";
$confirmed_bookings = mysqli_fetch_assoc(mysqli_query($conn, $confirmed_query))['total'];

$total_revenue_query = "SELECT SUM(total_price) as revenue FROM bookings WHERE status = 'confirmed'";
$total_revenue = mysqli_fetch_assoc(mysqli_query($conn, $total_revenue_query))['revenue'] ?? 0;

// Fetch all bookings with room details
$bookings_query = "SELECT b.*, r.room_type 
                   FROM bookings b 
                   JOIN rooms r ON b.room_id = r.id 
                   ORDER BY b.booking_date DESC";
$bookings_result = mysqli_query($conn, $bookings_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Red Lion Hotel</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background: #f5f6fa;
        }

        .dashboard {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background: #2c3e50;
            color: white;
            padding: 2rem 0;
            position: fixed;
            height: 100vh;
        }

        .sidebar-header {
            padding: 0 1.5rem;
            margin-bottom: 2rem;
        }

        .sidebar-header h2 {
            color: #c41e3a;
        }

        .sidebar-header p {
            font-size: 0.9rem;
            color: #bdc3c7;
        }

        .sidebar-menu {
            list-style: none;
        }

        .sidebar-menu li {
            margin-bottom: 0.5rem;
        }

        .sidebar-menu a {
            display: block;
            padding: 1rem 1.5rem;
            color: white;
            text-decoration: none;
            transition: background 0.3s;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: #34495e;
            border-left: 3px solid #c41e3a;
        }

        .sidebar-menu i {
            margin-right: 10px;
            width: 20px;
        }

        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 2rem;
        }

        .header {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            color: #2c3e50;
        }

        .logout-btn {
            padding: 10px 20px;
            background: #c41e3a;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
        }

        .logout-btn:hover {
            background: #a51c2c;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .stat-card i {
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .stat-card.blue { color: #3498db; }
        .stat-card.orange { color: #f39c12; }
        .stat-card.green { color: #2ecc71; }
        .stat-card.red { color: #e74c3c; }

        .stat-card h3 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            color: #2c3e50;
        }

        .stat-card p {
            color: #7f8c8d;
            font-size: 0.9rem;
        }

        .content-box {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .content-box h2 {
            color: #2c3e50;
            margin-bottom: 1.5rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th,
        table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ecf0f1;
        }

        table th {
            background: #34495e;
            color: white;
            font-weight: 500;
        }

        table tr:hover {
            background: #f8f9fa;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-confirmed {
            background: #d4edda;
            color: #155724;
        }

        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        .action-btn {
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 0.85rem;
            margin-right: 5px;
        }

        .btn-confirm {
            background: #2ecc71;
            color: white;
        }

        .btn-cancel {
            background: #e74c3c;
            color: white;
        }

        .btn-delete {
            background: #95a5a6;
            color: white;
        }

        .no-data {
            text-align: center;
            padding: 2rem;
            color: #7f8c8d;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            .main-content {
                margin-left: 0;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            table {
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>
<div class="dashboard">
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>RED LION</h2>
            <p>Admin Panel</p>
        </div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="../../public/index.php" target="_blank"><i class="fas fa-globe"></i> View Website</a></li>
            <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="header">
            <div>
                <h1>Dashboard</h1>
                <p>Welcome back, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</p>
            </div>
            <a href="../logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>

        <div class="stats-grid">
            <div class="stat-card blue">
                <i class="fas fa-calendar-check"></i>
                <h3><?php echo $total_bookings; ?></h3>
                <p>Total Bookings</p>
            </div>

            <div class="stat-card orange">
                <i class="fas fa-clock"></i>
                <h3><?php echo $pending_bookings; ?></h3>
                <p>Pending Bookings</p>
            </div>

            <div class="stat-card green">
                <i class="fas fa-check-circle"></i>
                <h3><?php echo $confirmed_bookings; ?></h3>
                <p>Confirmed Bookings</p>
            </div>

            <div class="stat-card red">
                <i class="fas fa-dollar-sign"></i>
                <h3>$<?php echo number_format($total_revenue, 2); ?></h3>
                <p>Total Revenue</p>
            </div>
        </div>

        <div class="content-box">
            <h2><i class="fas fa-list"></i> All Bookings</h2>

            <?php if (mysqli_num_rows($bookings_result) > 0): ?>
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Guest Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Room Type</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Guests</th>
                            <th>Rooms</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php while($booking = mysqli_fetch_assoc($bookings_result)): ?>
                            <tr>
                                <td><?php echo $booking['id']; ?></td>
                                <td><?php echo htmlspecialchars($booking['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($booking['email']); ?></td>
                                <td><?php echo htmlspecialchars($booking['phone']); ?></td>
                                <td><?php echo htmlspecialchars($booking['room_type']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($booking['check_in'])); ?></td>
                                <td><?php echo date('M d, Y', strtotime($booking['check_out'])); ?></td>
                                <td><?php echo $booking['num_guests']; ?></td>
                                <td><?php echo $booking['num_rooms']; ?></td>
                                <td>$<?php echo number_format($booking['total_price'], 2); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $booking['status']; ?>">
                                        <?php echo ucfirst($booking['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($booking['status'] == 'pending'): ?>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                            <input type="hidden" name="status" value="confirmed">
                                            <button type="submit" name="update_status" class="action-btn btn-confirm">Confirm</button>
                                        </form>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                            <input type="hidden" name="status" value="cancelled">
                                            <button type="submit" name="update_status" class="action-btn btn-cancel">Cancel</button>
                                        </form>
                                    <?php endif; ?>

                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this booking?');">
                                        <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                        <button type="submit" name="delete_booking" class="action-btn btn-delete">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="no-data">
                    <i class="fas fa-inbox" style="font-size: 3rem; color: #bdc3c7; margin-bottom: 1rem;"></i>
                    <p>No bookings yet</p>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>
</body>
</html>