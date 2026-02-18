<?php

include('./includes/dbconfig.php');


$name = $email = $phone = $password = $confirm_password = "";
$registration_success = "";
$error = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and sanitize input values from the form
    $name = mysqli_real_escape_string($link, trim($_POST['name']));
    $email = mysqli_real_escape_string($link, trim($_POST['email']));
    $phone = mysqli_real_escape_string($link, trim($_POST['phone']));
    $raw_password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    
    if (!empty($name) && !empty($email) && !empty($phone) && !empty($raw_password) && !empty($confirm_password)) {
        
        if ($raw_password !== $confirm_password) {
            $error = "Passwords do not match. Please make sure both passwords are identical.";
        } else {
            
            $check_email_sql = "SELECT id FROM users WHERE email = '$email'";
            $result = mysqli_query($link, $check_email_sql);

            if (mysqli_num_rows($result) > 0) {
                $error = "This email is already registered. Please use a different email or <a href='login.php'>login here</a>.";
            } else {
                
                $hashed_password = password_hash($raw_password, PASSWORD_DEFAULT);

                // SQL query to insert data into the users table
                $sql = "INSERT INTO users (name, email, phone, password, created_at) VALUES ('$name', '$email', '$phone', '$hashed_password', NOW())";

                
                if (mysqli_query($link, $sql)) {
                    // FIX: Redirect after successful registration to prevent form resubmission
                    header("Location: register.php?success=1");
                    exit();
                } else {
                    $error = "Error: " . $sql . "<br>" . mysqli_error($link);
                }
            }
        }
    } else {
        $error = "All fields are required. Please fill in all the details.";
    }
}

if (isset($_GET['success']) && $_GET['success'] == 1) {
    $registration_success = "Registration successful! You can now <a href='login.php'>login</a>.";
    
    $name = $email = $phone = $password = $confirm_password = "";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CourtConnect - Register</title>
    <style>
        /* Import the same font as other pages */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');
        
        body {
            font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f8f0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo-text {
            font-family: 'Poppins', sans-serif;
            font-size: 2.8rem;
            font-weight: 700;
            color: #2e7d32;
            margin: 0;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .logo-image {
            width: 150px;
            height: auto;
            margin-top: 10px;
        }
        .container {
            background-color: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
        h2 {
            text-align: center;
            color: #2e7d32;
            margin-bottom: 30px;
            font-weight: 600;
            font-size: 1.8rem;
        }
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            text-align: center;
            font-size: 0.95rem;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background-color: #ffebee;
            color: #c62828;
            border: 1px solid #ffcdd2;
        }
        .form-group {
            margin-bottom: 25px;
        }
        label {
            display: block;
            margin-bottom: 10px;
            color: #555;
            font-weight: 500;
            font-size: 1rem;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="tel"] {
            width: 100%;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        input:focus {
            border-color: #4caf50;
            outline: none;
        }
        button {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #4caf50 0%, #45a049 100%);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.1rem;
            font-weight: 600;
            transition: transform 0.2s ease;
        }
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(76, 175, 80, 0.3);
        }
        .login-link {
            text-align: center;
            display: block;
            margin-top: 25px;
            color: #666;
            font-size: 0.95rem;
        }
        .login-link a {
            color: #4caf50;
            text-decoration: none;
            font-weight: 500;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
        .password-container {
            position: relative;
        }
        .toggle-password {
            position: absolute;
            right: 15px;
            top: 15px;
            cursor: pointer;
            font-size: 1.2em;
            z-index: 2;
            background: white;
            padding: 0 5px;
        }
    </style>
</head>
<body>
    
   <div class="header">
    <h1 class="logo-text"><a href="index.php" style="color: inherit; text-decoration: none;">CourtConnect</a></h1>
    <img src="logo.png" alt="Super Sports Logo" class="logo-image">
</div>
    <div class="container">
        <h2>Create Your Account</h2>
        
        
        <?php if (!empty($registration_success)): ?>
            <div class="alert alert-success"><?php echo $registration_success; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="register.php" method="post">
            <div class="form-group">
                <label for="name">Full Name:</label>
                <input type="text" id="name" name="name" required placeholder="Enter your full name" value="<?php echo htmlspecialchars($name); ?>">
            </div>
            <div class="form-group">
                <label for="email">Email Address:</label>
                <input type="email" id="email" name="email" required placeholder="Enter your email" value="<?php echo htmlspecialchars($email); ?>">
            </div>
            <div class="form-group">
                <label for="phone">Phone Number:</label>
                <input type="tel" id="phone" name="phone" required placeholder="Enter your phone number" value="<?php echo htmlspecialchars($phone); ?>">
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <div class="password-container">
                    <input type="password" id="password" name="password" required placeholder="Enter your password" 
                           style="width: 100%; padding: 15px; padding-right: 45px; border: 2px solid #e0e0e0; border-radius: 8px; box-sizing: border-box; font-size: 1rem;">
                    <span class="toggle-password" onclick="togglePassword('password')" 
                          style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; font-size: 1.2em; z-index: 2; background: white; padding: 0 5px;">
                        👁️
                    </span>
                </div>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <div class="password-container">
                    <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirm your password" 
                           style="width: 100%; padding: 15px; padding-right: 45px; border: 2px solid #e0e0e0; border-radius: 8px; box-sizing: border-box; font-size: 1rem;">
                    <span class="toggle-password" onclick="togglePassword('confirm_password')" 
                          style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; font-size: 1.2em; z-index: 2; background: white; padding: 0 5px;">
                        👁️
                    </span>
                </div>
            </div>
            <button type="submit">Create Account</button>
        </form>
        <p class="login-link">Already have an account? <a href="login.php">Login here</a></p>
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
