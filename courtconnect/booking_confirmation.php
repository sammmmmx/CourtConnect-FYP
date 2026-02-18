<?php
session_start();
include('./includes/dbconfig.php');
include('./includes/header.php');


if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['court_id'])) {
    $court_id = mysqli_real_escape_string($link, $_POST['court_id']);
    $booking_date = mysqli_real_escape_string($link, $_POST['booking_date']);
    $start_time = mysqli_real_escape_string($link, $_POST['start_time']);
    $duration = mysqli_real_escape_string($link, $_POST['duration_hours']);
    $user_id = $_SESSION['user_id'];

    
    $court_query = "SELECT * FROM courts WHERE id = $court_id";
    $court_result = mysqli_query($link, $court_query);
    $court = mysqli_fetch_assoc($court_result);

    if (!$court) {
        die("Court not found!");
    }

    
    $total_price = $court['price'] * $duration;

    
    $expiry_datetime = date('Y-m-d H:i:s', strtotime('+7 minutes'));

    
    $sql = "INSERT INTO bookings (user_id, court_id, booking_date, start_time, duration_hours, total_price, status, created_at, expiry_time) 
            VALUES ($user_id, $court_id, '$booking_date', '$start_time', $duration, $total_price, 'Pending', NOW(), '$expiry_datetime')";

    if (mysqli_query($link, $sql)) {
        $booking_id = mysqli_insert_id($link);
        $_SESSION['last_booking_id'] = $booking_id;

        
        header("Location: booking_confirmation.php?success=1");
        exit;
    } else {
        die("Booking failed: " . mysqli_error($link));
    }
}


$booking = null;
if (isset($_GET['success']) && $_GET['success'] == 1 && isset($_SESSION['last_booking_id'])) {
    $booking_id = $_SESSION['last_booking_id'];

    $booking_query = "SELECT b.*, c.name, c.type, c.price 
                      FROM bookings b 
                      JOIN courts c ON b.court_id = c.id 
                      WHERE b.id = $booking_id";
    $booking_result = mysqli_query($link, $booking_query);
    $booking = mysqli_fetch_assoc($booking_result);

    if (!$booking) {
        die("Booking not found.");
    }

    $court = [
        'name' => $booking['name'],
        'type' => $booking['type'],
        'price' => $booking['price']
    ];
    $booking_date = $booking['booking_date'];
    $start_time = $booking['start_time'];
    $duration = $booking['duration_hours'];
    $total_price = $booking['total_price'];
    $expiry_time = date('h:i A', strtotime($booking['expiry_time']));
    $booking_id = $booking['id'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CourtConnect - Booking Confirmation</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f8f0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 0 25px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2e7d32;
            text-align: center;
        }
        /* Change these headings to black */
        .booking-summary h2,
        .payment-info h2,
        .urgent-notice h2 {
            color: #000000;
            text-align: center;
        }
        .booking-summary {
            background: #e8f5e9;
            padding: 25px;
            border-radius: 10px;
            margin: 20px 0;
            border-left: 5px solid #4caf50;
        }
        .payment-info {
            background: #fff3e0;
            padding: 25px;
            border-radius: 10px;
            margin: 20px 0;
            border-left: 5px solid #ff9800;
        }
        .urgent-notice {
            background: #ffebee;
            padding: 25px;
            border-radius: 10px;
            margin: 20px 0;
            border-left: 5px solid #f44336;
            text-align: center;
        }
        .info-box {
            margin: 15px 0;
            padding: 15px;
            background: #f1f8e9;
            border-radius: 8px;
            border: 2px solid #c8e6c9;
        }
        .bank-details {
            background: #e3f2fd;
            padding: 20px;
            border-radius: 8px;
            margin: 15px 0;
            border: 2px solid #bbdefb;
        }
        .whatsapp-btn {
            display: inline-block;
            background: #25D366;
            color: white;
            padding: 15px 30px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            margin-top: 15px;
            transition: background 0.3s ease;
        }
        .whatsapp-btn:hover {
            background: #128C7E;
        }
        .booking-detail {
            margin: 10px 0;
            font-size: 1.1rem;
            color: #333333;
        }
        .price-highlight {
            font-size: 1.5rem;
            color: #d32f2f;
            font-weight: bold;
        }
        /* Style for the View My Bookings link */
        .view-bookings-link {
            color: #000000;
            text-decoration: none;
            font-weight: bold;
            font-size: 1.1rem;
            transition: color 0.3s ease;
        }
        .view-bookings-link:hover {
            color: #4caf50;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($booking): ?>
        <h1>🎉 Booking Successful!</h1>
        
        <div class="booking-summary">
            <h2>Your Booking Details</h2>
            <div class="booking-detail"><strong>Court:</strong> <?php echo $court['name']; ?> (<?php echo $court['type']; ?>)</div>
            <div class="booking-detail"><strong>Date:</strong> <?php echo date('F j, Y', strtotime($booking_date)); ?></div>
            <div class="booking-detail"><strong>Time:</strong> <?php echo date('g:i A', strtotime($start_time)); ?> for <?php echo $duration; ?> hour(s)</div>
            <div class="booking-detail"><strong>Total Amount:</strong> <span class="price-highlight">RM <?php echo number_format($total_price, 2); ?></span></div>
            <div class="booking-detail"><strong>Booking ID:</strong> #<?php echo $booking_id; ?></div>
        </div>

        <div class="payment-info">
            <h2>💳 Payment Instructions</h2>
            <div class="bank-details">
                <h3 style="color: #000000;">Bank Transfer Details:</h3>
                <p><strong>Bank:</strong> Maybank</p>
                <p><strong>Account Name:</strong> SUPER SPORTS BADMINTON</p>
                <p><strong>Account Number:</strong> 1512 8533 5919</p>
                <p><strong>Amount:</strong> RM <?php echo number_format($total_price, 2); ?></p>
            </div>
        </div>

        <div class="urgent-notice">
            <h2>⏰ Important Notice!</h2>
            <p>Your booking will be <strong>automatically cancelled</strong> if payment is not confirmed by:</p>
            <h3 style="color: #d32f2f; margin: 10px 0;"><?php echo $expiry_time; ?> (7 minutes from now)</h3>
            
            <div class="info-box">
                <p>📞 <strong>WhatsApp your payment receipt to:</strong></p>
                <p style="font-size: 1.2rem; margin: 10px 0;">
                    <a href="https://wa.me/60177726043?text=Payment%20Confirmation%20for%20Booking%20ID%20#<?php echo $booking_id; ?>"
                       target="_blank" class="whatsapp-btn">
                       📱 WhatsApp +60 17-772-6043
                    </a>
                </p>
                <p>Include your Booking ID (#<?php echo $booking_id; ?>) and your registered email in the message.</p>
            </div>
        </div>

        <div style="text-align: center; margin-top: 30px;">
            <a href="profile.php" class="view-bookings-link">← View My Bookings</a>
        </div>

        <?php else: ?>
        <div class="urgent-notice">
            <h2>❌ Booking Failed</h2>
            <p>There was an error processing your booking. Please try again.</p>
            <a href="booking.php" style="color: #2e7d32; text-decoration: none; font-weight: bold;">
                ← Try Again
            </a>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>