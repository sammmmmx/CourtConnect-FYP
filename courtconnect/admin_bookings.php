<?php
session_start();
include('./includes/dbconfig.php');


if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("Location: admin_login.php");
    exit;
}


if (isset($_GET['change_status'])) {
    $booking_id = mysqli_real_escape_string($link, $_GET['booking_id']);
    $new_status = mysqli_real_escape_string($link, $_GET['new_status']);
    
    $update_sql = "UPDATE bookings SET status = '$new_status' WHERE id = $booking_id";
    if (mysqli_query($link, $update_sql)) {
        $status_message = "Booking status updated successfully!";
    } else {
        $status_message = "Error updating status: " . mysqli_error($link);
    }
}


if (isset($_GET['delete_booking'])) {
    $booking_id = mysqli_real_escape_string($link, $_GET['booking_id']);
    
    $delete_sql = "DELETE FROM bookings WHERE id = $booking_id";
    if (mysqli_query($link, $delete_sql)) {
        $status_message = "Booking deleted successfully!";
    } else {
        $status_message = "Error deleting booking: " . mysqli_error($link);
    }
}


$bookings_query = "SELECT b.*, u.name as user_name, u.email, u.phone, c.name as court_name, c.type as court_type 
                   FROM bookings b 
                   JOIN users u ON b.user_id = u.id 
                   JOIN courts c ON b.court_id = c.id 
                   ORDER BY b.booking_date DESC, b.start_time DESC";
$bookings_result = mysqli_query($link, $bookings_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CourtConnect - Manage Bookings</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        .header {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .nav-menu {
            background: #2e7d32;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
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
        .bookings-table {
            width: 100%;
            background: white;
            border-collapse: collapse;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .bookings-table th, .bookings-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .bookings-table th {
            background-color: #2e7d32;
            color: white;
        }
        .status-pending { background-color: #fff3cd; color: #856404; padding: 5px 10px; border-radius: 3px; }
        .status-confirmed { background-color: #d4edda; color: #155724; padding: 5px 10px; border-radius: 3px; }
        .status-cancelled { background-color: #f8d7da; color: #721c24; padding: 5px 10px; border-radius: 3px; }
        .btn {
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            margin: 2px;
            text-decoration: none;
            display: inline-block;
            font-size: 0.8em;
        }
        .btn-confirm { background-color: #28a745; color: white; }
        .btn-cancel { background-color: #dc3545; color: white; }
        .btn-delete { background-color: #6c757d; color: white; }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .alert-success { background-color: #d4edda; color: #155724; }
        .alert-error { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Manage Bookings</h1>
        <p>Welcome, <?php echo $_SESSION['admin_name']; ?>! | <a href="logout.php">Logout</a></p>
    </div>

    <div class="nav-menu">
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="admin_bookings.php">Manage Bookings</a>
        <a href="admin_courts.php">Manage Courts</a>
        <a href="admin_users.php">Manage Users</a>
    </div>

    <?php if (isset($status_message)): ?>
        <div class="alert alert-success"><?php echo $status_message; ?></div>
    <?php endif; ?>

    <table class="bookings-table">
        <thead>
            <tr>
                <th>Booking ID</th>
                <th>User</th>
                <th>Court</th>
                <th>Date & Time</th>
                <th>Duration</th>
                <th>Total Price</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($booking = mysqli_fetch_assoc($bookings_result)): ?>
            <tr>
                <td><?php echo $booking['id']; ?></td>
                <td><?php echo $booking['user_name']; ?><br><small><?php echo $booking['email']; ?></small></td>
                <td><?php echo $booking['court_name']; ?> (<?php echo $booking['court_type']; ?>)</td>
                <td><?php echo $booking['booking_date']; ?><br><?php echo date('h:i A', strtotime($booking['start_time'])); ?></td>
                <td><?php echo $booking['duration_hours']; ?> hours</td>
                <td>RM <?php echo number_format($booking['total_price'], 2); ?></td>
                <td>
                    <span class="status-<?php echo strtolower($booking['status']); ?>">
                        <?php echo $booking['status']; ?>
                    </span>
                </td>
                <td>
                    <?php if ($booking['status'] == 'Pending'): ?>
                        <a href="admin_bookings.php?booking_id=<?php echo $booking['id']; ?>&new_status=Confirmed&change_status=1" class="btn btn-confirm">Confirm</a>
                        <a href="admin_bookings.php?booking_id=<?php echo $booking['id']; ?>&new_status=Cancelled&change_status=1" class="btn btn-cancel">Cancel</a>
                    <?php endif; ?>
                    <a href="admin_bookings.php?booking_id=<?php echo $booking['id']; ?>&delete_booking=1" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this booking?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
