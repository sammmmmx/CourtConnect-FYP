<?php
session_start();
include('./includes/dbconfig.php');


if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}


include('./includes/header.php');

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];


$user_query = "SELECT * FROM users WHERE id = $user_id";
$user_result = mysqli_query($link, $user_query);
$user = mysqli_fetch_assoc($user_result);


$bookings_query = "SELECT b.*, c.name as court_name, c.type as court_type, c.price as court_price 
                   FROM bookings b 
                   JOIN courts c ON b.court_id = c.id 
                   WHERE b.user_id = $user_id 
                   ORDER BY b.booking_date DESC, b.start_time DESC";
$bookings_result = mysqli_query($link, $bookings_query);


$update_success = '';
$update_error = '';
$name = $user['name'];
$email = $user['email'];
$phone = $user['phone'];


if (isset($_GET['success']) && $_GET['success'] == 1) {
    $update_success = "Profile updated successfully!";
    // Update session name
    $_SESSION['user_name'] = $name;
    // Refresh user data
    $user_result = mysqli_query($link, $user_query);
    $user = mysqli_fetch_assoc($user_result);
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $name = mysqli_real_escape_string($link, trim($_POST['name']));
    $email = mysqli_real_escape_string($link, trim($_POST['email']));
    $phone = mysqli_real_escape_string($link, trim($_POST['phone']));
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    
    if (!empty($name) && !empty($email) && !empty($phone)) {
        
        if (!empty($new_password) && $new_password !== $confirm_password) {
            $update_error = "New password and confirmation password do not match.";
        } else {
            
            $check_email_sql = "SELECT id FROM users WHERE email = '$email' AND id != $user_id";
            $email_result = mysqli_query($link, $check_email_sql);

            if (mysqli_num_rows($email_result) > 0) {
                $update_error = "This email is already registered by another user.";
            } else {
                // Build update query
                $update_sql = "UPDATE users SET name = '$name', email = '$email', phone = '$phone'";
                
                
                if (!empty($new_password)) {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $update_sql .= ", password = '$hashed_password'";
                }
                
                $update_sql .= " WHERE id = $user_id";

                if (mysqli_query($link, $update_sql)) {
                  

                    header("Location: profile.php?success=1");
                    exit();
                } else {
                    $update_error = "Error updating profile: " . mysqli_error($link);
                }
            }
        }
    } else {
        $update_error = "Please fill in all required fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CourtConnect - My Profile</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f8f0;
            padding: 20px;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        h1, h2 {
            color: #2e7d32;
        }
        .profile-section, .bookings-section {
            margin-bottom: 40px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        input[type="text"], input[type="email"], input[type="password"], input[type="tel"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            margin-bottom: 10px;
        }
        button {
            background-color: #4caf50;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #45a049;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
        }
        .booking-card {
            border: 1px solid #ddd;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .booking-status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
            font-weight: bold;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-confirmed {
            background-color: #d4edda;
            color: #155724;
        }
        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }
        .password-container {
            position: relative;
        }
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 12px;
            cursor: pointer;
            font-size: 1.2em;
            z-index: 2;
            background: white;
            padding: 0 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome, <?php echo $user_name; ?>!</h1>
        
        <!-- Profile Update Section -->
        <div class="profile-section">
            <h2>My Profile</h2>
            
            <?php if ($update_success): ?>
                <div class="alert alert-success"><?php echo $update_success; ?></div>
            <?php endif; ?>
            
            <?php if ($update_error): ?>
                <div class="alert alert-error"><?php echo $update_error; ?></div>
            <?php endif; ?>

            <form method="POST" action="profile.php">
                <div class="form-group">
                    <label for="name">Full Name:</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number:</label>
                    <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="new_password">New Password (leave blank to keep current):</label>
                    <div class="password-container">
                        <input type="password" id="new_password" name="new_password" placeholder="Enter new password">
                        <span class="toggle-password" onclick="togglePassword('new_password')">👁️</span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm New Password:</label>
                    <div class="password-container">
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm new password">
                        <span class="toggle-password" onclick="togglePassword('confirm_password')">👁️</span>
                    </div>
                </div>
                
                <button type="submit" name="update_profile">Update Profile</button>
            </form>
        </div>

        
        <div class="bookings-section">
            <h2>My Bookings</h2>
            
            <?php if (mysqli_num_rows($bookings_result) > 0): ?>
                <?php while ($booking = mysqli_fetch_assoc($bookings_result)): ?>
                    <div class="booking-card">
                        <h3> <?php echo $booking['court_name']; ?> (<?php echo $booking['court_type']; ?>)</h3>
                        <p><strong>Date:</strong> <?php echo $booking['booking_date']; ?></p>
                        <p><strong>Time:</strong> <?php echo date('h:i A', strtotime($booking['start_time'])); ?></p>
                        <p><strong>Duration:</strong> <?php echo $booking['duration_hours']; ?> hours</p>
                        <p><strong>Total Price:</strong> RM <?php echo number_format($booking['total_price'], 2); ?></p>
                        <p><strong>Status:</strong> 
                            <span class="booking-status status-<?php echo strtolower($booking['status']); ?>">
                                <?php echo $booking['status']; ?>
                            </span>
                        </p>
                        <?php if (!empty($booking['special_requests'])): ?>
                            <p><strong>Special Requests:</strong> <?php echo htmlspecialchars($booking['special_requests']); ?></p>
                        <?php endif; ?>
                        <p><strong>Booked on:</strong> <?php echo date('M j, Y h:i A', strtotime($booking['created_at'])); ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>You haven't made any bookings yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
    function togglePassword(fieldId) {
        const passwordField = document.getElementById(fieldId);
        const toggleIcon = passwordField.nextElementSibling;
        
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            toggleIcon.textContent = '🔒';
        } else {
            passwordField.type = 'password';
            toggleIcon.textContent = '👁️';
        }
    }
    </script>
</body>
</html>