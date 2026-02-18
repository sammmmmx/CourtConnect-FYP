<?php
session_start();
include('./includes/dbconfig.php');

$message = "";
$error = "";
$valid_token = false;
$user_id = null;


if (isset($_GET['token'])) {
    $token = mysqli_real_escape_string($link, $_GET['token']);
    
    
    $sql = "SELECT id, reset_expiry FROM users WHERE reset_token = '$token' AND reset_expiry > NOW()";
    $result = mysqli_query($link, $sql);
    
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        $user_id = $user['id'];
        $valid_token = true;
        
        
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $new_password = trim($_POST['new_password']);
            $confirm_password = trim($_POST['confirm_password']);
            
            if (!empty($new_password) && !empty($confirm_password)) {
                if ($new_password === $confirm_password) {
                    
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    
                    
                    $update_sql = "UPDATE users SET password = '$hashed_password', reset_token = NULL, reset_expiry = NULL WHERE id = $user_id";
                    
                    if (mysqli_query($link, $update_sql)) {
                        $message = "Password reset successfully! You can now <a href='login.php'>login</a> with your new password.";
                        $valid_token = false; 
                    } else {
                        $error = "Error updating password. Please try again.";
                    }
                } else {
                    $error = "Passwords do not match.";
                }
            } else {
                $error = "Please fill in both password fields.";
            }
        }
    } else {
        $error = "Invalid or expired reset link. Please request a new reset link.";
    }
} else {
    $error = "No reset token provided.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CourtConnect - Reset Password</title>
    <style>
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
        input[type="password"] {
            width: 100%;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        input[type="password"]:focus {
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
            top: 50%;
            transform: translateY(-50%);
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
        <h2>Reset Your Password</h2>
        
        <?php if (!empty($message)): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($valid_token): ?>
        <form method="POST" action="reset_password.php?token=<?php echo htmlspecialchars($_GET['token']); ?>">
            <div class="form-group">
                <label for="new_password">New Password:</label>
                <div class="password-container">
                    <input type="password" id="new_password" name="new_password" required placeholder="Enter new password" 
                           style="width: 100%; padding: 15px; padding-right: 45px; border: 2px solid #e0e0e0; border-radius: 8px; box-sizing: border-box; font-size: 1rem;">
                    <span class="toggle-password" onclick="togglePassword('new_password')" 
                          style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; font-size: 1.2em; z-index: 2; background: white; padding: 0 5px;">
                        👁️
                    </span>
                </div>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm New Password:</label>
                <div class="password-container">
                    <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirm new password" 
                           style="width: 100%; padding: 15px; padding-right: 45px; border: 2px solid #e0e0e0; border-radius: 8px; box-sizing: border-box; font-size: 1rem;">
                    <span class="toggle-password" onclick="togglePassword('confirm_password')" 
                          style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; font-size: 1.2em; z-index: 2; background: white; padding: 0 5px;">
                        👁️
                    </span>
                </div>
            </div>
            <button type="submit">Reset Password</button>
        </form>
        <?php elseif (empty($message) && empty($error)): ?>
            <p style="text-align: center;">Loading reset link...</p>
        <?php endif; ?>
        
        <p class="login-link"><a href="login.php">Back to Login</a></p>
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
