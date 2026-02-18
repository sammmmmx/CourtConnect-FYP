<?php
session_start();
include('./includes/dbconfig.php');


if (isset($_SESSION['admin_loggedin']) && $_SESSION['admin_loggedin'] === true) {
    header("Location: admin_dashboard.php");
    exit;
}

$login_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($link, trim($_POST['email']));
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {
        $sql = "SELECT id, name, password, is_admin FROM users WHERE email = '$email' AND is_admin = 1";
        $result = mysqli_query($link, $sql);

        if (mysqli_num_rows($result) == 1) {
            $admin = mysqli_fetch_assoc($result);
            
            if (password_verify($password, $admin['password'])) {
                
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_name'] = $admin['name'];
                $_SESSION['admin_loggedin'] = true;
                
                header("Location: admin_dashboard.php");
                exit();
            } else {
                $login_error = "Invalid password.";
            }
        } else {
            $login_error = "No admin account found with this email.";
        }
    } else {
        $login_error = "Please enter both email and password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CourtConnect - Admin Login</title>
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
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: #dc3545; 
            outline: none;
        }
        button {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); 

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
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3); 

        }
        .user-notice {
            text-align: center;
            margin-top: 20px;
            padding: 15px;
            background-color: #e8f5e8;
            border-radius: 8px;
            color: #2e7d32;
            font-size: 0.9rem;
        }
        .user-notice a {
            color: #4caf50;
            font-weight: 500;
            text-decoration: none;
        }
        .user-notice a:hover {
            text-decoration: underline;
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
            user-select: none;
        }
    </style>
</head>
<body>
    
    <div class="header">
        <h1 class="logo-text"><a href="index.php" style="color: inherit; text-decoration: none;">CourtConnect</a></h1>
        <img src="logo.png" alt="Super Sports Logo" class="logo-image">
    </div>

    <div class="container">
        <h2>Admin Access</h2>
        
        <?php if (!empty($login_error)): ?>
            <div class="alert alert-error"><?php echo $login_error; ?></div>
        <?php endif; ?>

              <form method="POST" action="admin_login.php">
            <div class="form-group">
                <label for="email">Admin Email:</label>
                <input type="email" id="email" name="email" required placeholder="Enter admin email" 
                       style="width: 100%; padding: 15px; border: 2px solid #e0e0e0; border-radius: 8px; box-sizing: border-box; font-size: 1rem;">
            </div>
            <div class="form-group">
                <label for="password">Admin Password:</label>
                <div style="position: relative;">
                    <input type="password" id="password" name="password" required placeholder="Enter admin password" 
                           style="width: 100%; padding: 15px; padding-right: 45px; border: 2px solid #e0e0e0; border-radius: 8px; box-sizing: border-box; font-size: 1rem;">
                    <span class="toggle-password" onclick="togglePassword('password')" 
                          style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; font-size: 1.2em; z-index: 2; background: white; padding: 0 5px;">
                        👁️
                    </span>
                </div>
            </div>
            <button type="submit" style="width: 100%; box-sizing: border-box;">Admin Login</button>
        </form>
        
        <div class="user-notice">
            <strong>Regular Users:</strong> <a href="login.php">Click here for user login</a>
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
