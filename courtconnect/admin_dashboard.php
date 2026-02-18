<?php
session_start();
include('./includes/dbconfig.php');


if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("Location: admin_login.php");
    exit;
}


$total_users = mysqli_fetch_assoc(mysqli_query($link, "SELECT COUNT(*) as count FROM users"))['count'];
$total_bookings = mysqli_fetch_assoc(mysqli_query($link, "SELECT COUNT(*) as count FROM bookings"))['count'];
$total_courts = mysqli_fetch_assoc(mysqli_query($link, "SELECT COUNT(*) as count FROM courts"))['count'];
$recent_bookings = mysqli_query($link, "SELECT b.*, u.name as user_name, c.name as court_name 
                                      FROM bookings b 
                                      JOIN users u ON b.user_id = u.id 
                                      JOIN courts c ON b.court_id = c.id 
                                      ORDER BY b.created_at DESC 
                                      LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CourtConnect - Admin Dashboard</title>
    <style>
        
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        .main-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: white;
            padding: 15px 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .branding {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
        }
        .logo-text {
            font-family: 'Poppins', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: #2e7d32;
            margin: 0;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .logo-image {
            width: 100px;
            height: auto;
        }
        .page-title {
            text-align: center;
            flex-grow: 1;
        }
        .user-info {
            text-align: right;
        }
        .stats-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-number {
            font-size: 2.5em;
            font-weight: bold;
            color: #2e7d32;
            margin: 10px 0;
        }
        .recent-bookings {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .booking-item {
            border: 1px solid #ddd;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .nav-menu {
            background: #2e7d32;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
        }
        .nav-menu a {
            color: white;
            text-decoration: none;
            margin-right: 20px;
            padding: 10px 15px;
            border-radius: 5px;
        }
        .nav-menu a:hover {
            background: #45a049;
        }
    </style>
</head>
<body>
    
    <div class="main-header">
        <div class="branding">
            <h1 class="logo-text">CourtConnect</h1>
            <img src="logo.png" alt="Super Sports Logo" class="logo-image">
        </div>
        <div class="page-title">
            <h1>Admin Dashboard</h1>
        </div>
        <div class="user-info">
            <p>Welcome, <?php echo $_SESSION['admin_name']; ?>!<br><a href="logout.php">Logout</a></p>
        </div>
    </div>

    <div class="nav-menu">
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="admin_bookings.php">Manage Bookings</a>
        <a href="admin_courts.php">Manage Courts</a>
        <a href="admin_users.php">Manage Users</a>
    </div>

    <div class="stats-container">
        <div class="stat-card">
            <h3>Total Users</h3>
            <div class="stat-number"><?php echo $total_users; ?></div>
        </div>
        <div class="stat-card">
            <h3>Total Bookings</h3>
            <div class="stat-number"><?php echo $total_bookings; ?></div>
        </div>
        <div class="stat-card">
            <h3>Total Courts</h3>
            <div class="stat-number"><?php echo $total_courts; ?></div>
        </div>
    </div>

    <div class="recent-bookings">
        <h2>Recent Bookings</h2>
        <?php while ($booking = mysqli_fetch_assoc($recent_bookings)): ?>
            <div class="booking-item">
                <strong>Court:</strong> <?php echo $booking['court_name']; ?> | 
                <strong>User:</strong> <?php echo $booking['user_name']; ?> | 
                <strong>Date:</strong> <?php echo $booking['booking_date']; ?> | 
                <strong>Time:</strong> <?php echo date('h:i A', strtotime($booking['start_time'])); ?> | 
                <strong>Status:</strong> <?php echo $booking['status']; ?>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>
