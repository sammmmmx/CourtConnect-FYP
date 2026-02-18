<?php
session_start();
include('./includes/dbconfig.php');
include('./includes/header.php');


if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}


$booking_date = $start_time = $duration = '';
$available_courts = [];
$error = '';
$show_courts = false;


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['check_availability'])) {
    $booking_date = mysqli_real_escape_string($link, $_POST['booking_date']);
    $start_time = mysqli_real_escape_string($link, $_POST['start_time']);
    $duration = mysqli_real_escape_string($link, $_POST['duration_hours']);
    
    if (!empty($booking_date) && !empty($start_time) && !empty($duration)) {
        $show_courts = true;
        
        
        $end_time = date('H:i:s', strtotime($start_time) + ($duration * 3600));
        
        $query = "SELECT c.* FROM courts c 
                  WHERE c.status = 'Available' 
                  AND c.id NOT IN (
                      SELECT court_id FROM bookings 
                      WHERE booking_date = '$booking_date' 
                      AND (
                          (start_time < '$end_time' AND ADDTIME(start_time, SEC_TO_TIME(duration_hours * 3600)) > '$start_time')
                      )
                  )";
        
        $result = mysqli_query($link, $query);
        while ($court = mysqli_fetch_assoc($result)) {
            $available_courts[] = $court;
        }
    } else {
        $error = "Please fill in all fields to check availability.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CourtConnect - Book a Court</title>
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
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2e7d32;
            text-align: center;
        }
        /* Change "Available Courts for..." text to black */
        h2 {
            color: #000000;
            text-align: center;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        select, input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        button {
            background-color: #4caf50;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
            width: 100%;
        }
        button:hover {
            background-color: #45a049;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
        }
        .court-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        .court-card {
            border: 2px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
        }
        .court-card.available {
            border-color: #4caf50;
            background-color: #f1f8e9;
        }
        .court-card.unavailable {
            border-color: #f44336;
            background-color: #ffebee;
            opacity: 0.7;
        }
        .availability-label {
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: bold;
            margin-bottom: 10px;
            display: inline-block;
        }
        .available-label {
            background-color: #4caf50;
            color: white;
        }
        .unavailable-label {
            background-color: #f44336;
            color: white;
        }
        .court-image {
            width: 100%;
            height: 150px;
            margin-bottom: 15px;
            border-radius: 8px;
            overflow: hidden;
        }
        .court-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        .court-card:hover .court-img {
            transform: scale(1.05);
        }
        /* Style for the back link */
        .back-link {
            color: #000000;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease;
        }
        .back-link:hover {
            color: #4caf50;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Book a Badminton Court</h1>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (!$show_courts): ?>
        
        <form method="POST" action="booking.php">
            <div class="form-group">
                <label for="booking_date">Booking Date:</label>
                <input type="date" id="booking_date" name="booking_date" 
                       min="<?php echo date('Y-m-d'); ?>" 
                       value="<?php echo $booking_date; ?>" required>
            </div>

            <div class="form-group">
                <label for="start_time">Start Time:</label>
                <select id="start_time" name="start_time" required>
                    <option value="">Select Start Time</option>
                    <?php
                    

                    for ($hour = 8; $hour <= 25; $hour++) {
                        $display_hour = $hour <= 23 ? $hour : $hour - 24;
                        $am_pm = $display_hour >= 12 ? 'PM' : 'AM';
                        $display_hour12 = $display_hour > 12 ? $display_hour - 12 : $display_hour;
                        if ($display_hour12 === 0) $display_hour12 = 12;
                        
                        $time_value = sprintf('%02d:00:00', $display_hour);
                        $time_display = sprintf('%d:00 %s', $display_hour12, $am_pm);
                        $selected = ($start_time == $time_value) ? 'selected' : '';
                        echo "<option value='$time_value' $selected>$time_display</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="duration_hours">Duration (hours):</label>
                <input type="number" id="duration_hours" name="duration_hours" 
                       min="1" max="4" value="<?php echo $duration ?: '1'; ?>" required>
            </div>

            <button type="submit" name="check_availability">Check Available Courts</button>
        </form>
        
        <?php else: ?>
        
        <h2>Available Courts for <?php echo date('M j, Y', strtotime($booking_date)); ?> at <?php echo date('g:i A', strtotime($start_time)); ?></h2>
        
        <div class="court-grid">
            <?php if (!empty($available_courts)): ?>
                <?php foreach ($available_courts as $court): ?>
                    <div class="court-card available">
                        <span class="availability-label available-label">AVAILABLE</span>
                        
                       
<div class="court-image">
    <?php 
    $court_image = "images/courts/court_" . $court['id'] . ".jpg";
    $default_image = "images/courts/default_court.jpg";
    
    if (file_exists($court_image)) {
        echo "<img src='$court_image' alt='Court {$court['id']}' class='court-img'>";
    } else {
        echo "<img src='$default_image' alt='Court Default' class='court-img'>";
    }
    ?>
</div>
                        
                        <h3>Court <?php echo $court['id']; ?></h3>
                        <p><?php echo $court['type']; ?> Court</p>
                        <p><strong>RM <?php echo $court['price']; ?>/hour</strong></p>
                        
                        <form method="POST" action="booking_confirmation.php" style="margin-top: 15px;">
                            <input type="hidden" name="court_id" value="<?php echo $court['id']; ?>">
                            <input type="hidden" name="booking_date" value="<?php echo $booking_date; ?>">
                            <input type="hidden" name="start_time" value="<?php echo $start_time; ?>">
                            <input type="hidden" name="duration_hours" value="<?php echo $duration; ?>">
                            <button type="submit" style="background-color: #2196F3;">Select This Court</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No courts available for the selected time slot. Please choose a different time or date.</p>
            <?php endif; ?>
        </div>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="booking.php" class="back-link">← Choose Different Date/Time</a>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>