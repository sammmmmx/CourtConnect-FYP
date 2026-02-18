<?php

session_start();
include('./includes/dbconfig.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CourtConnect - Home</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(rgba(240, 248, 240, 0.9), rgba(240, 248, 240, 0.9)), 
                       url('court-bg.jpg') center/cover;
            margin: 0;
            padding: 80px 20px 20px 20px;
            min-height: 100vh;
        }
        .content {
            display: flex;
            flex-direction: column;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .welcome-message {
            background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.15);
            text-align: center;
            margin-bottom: 30px;
            width: 80%;
            border: 2px solid #4caf50;
        }
        .login-prompt {
            background: linear-gradient(135deg, #f1f8e9 0%, #dcedc8 100%);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.15);
            text-align: center;
            width: 80%;
            border: 2px solid #8bc34a;
        }
        .button {
            display: inline-block;
            padding: 15px 30px;
            background: linear-gradient(135deg, #4caf50 0%, #45a049 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            margin: 15px;
            font-weight: bold;
            font-size: 1.1rem;
            transition: transform 0.2s ease;
        }
        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(76, 175, 80, 0.3);
        }
        .admin-notice {
            margin-top: 20px;
            padding: 15px;
            background-color: #fff3cd;
            border-radius: 8px;
            color: #856404;
        }
        .admin-notice a {
            color: #dc3545;
            font-weight: 500;
        }
        
        .slideshow-container {
            position: relative;
            width: 80%;
            max-width: 800px;
            margin: 30px auto;
            height: 400px; 
            overflow: hidden;
            border-radius: 15px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.15);
        }
        .slide {
            display: none;
            width: 100%;
            height: 100%;
            object-fit: cover; 
 
            border-radius: 15px;
        }
        .slide.active {
            display: block;
        }
        .slideshow-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0, 0, 0, 0.5);
            color: white;
            border: none;
            padding: 15px;
            cursor: pointer;
            border-radius: 50%;
            font-size: 1.5rem;
            transition: background 0.3s ease;
            z-index: 10;
        }
        .slideshow-btn:hover {
            background: rgba(0, 0, 0, 0.8);
        }
        .prev {
            left: 10px;
        }
        .next {
            right: 10px;
        }
        .slideshow-dots {
            position: absolute;
            bottom: 15px;
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
            z-index: 10;
        }
        .dot {
            display: inline-block;
            width: 12px;
            height: 12px;
            margin: 0 5px;
            background: #bbb;
            border-radius: 50%;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .dot.active {
            background: #4caf50;
        }

    </style>
</head>
<body>
    
    <?php include('./includes/header.php'); ?>

    <div class="content">
        <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
            
            <div class="welcome-message">
                <h1>Welcome back, <?php echo $_SESSION['user_name']; ?>! 👋</h1>
                <p>You are successfully logged in to CourtConnect.</p>
                <p>What would you like to do today?</p>
                <div>
                    <a href="booking.php" class="button">Book a Court</a>
                    <a href="profile.php" class="button">View My Profile</a>
                </div>
            </div>

        <?php elseif (isset($_SESSION['admin_loggedin']) && $_SESSION['admin_loggedin'] === true): ?>
            
            <div class="welcome-message">
                <h1>Welcome, <?php echo $_SESSION['admin_name']; ?>! 👋</h1>
                <p>You are logged in as an administrator.</p>
                <div>
                    <a href="admin_dashboard.php" class="button">Go to Admin Dashboard</a>
                </div>
            </div>

        <?php else: ?>
            
            <div class="login-prompt">
                <h1>Welcome to CourtConnect! 🏸</h1>
                <p>Please log in to access the court booking system</p>
                <div>
                    <a href="login.php" class="button">Login</a>
                    <a href="register.php" class="button">Register</a>
                </div>
                <div class="admin-notice">
                    <strong>Administrators:</strong> <a href="admin_login.php">Click here to access admin login</a>
                </div>
            </div>
        <?php endif; ?>

        
        <div class="slideshow-container">
            <img src="court1.jpg" class="slide active" alt="Court Image 1">
            <img src="court2.jpg" class="slide" alt="Court Image 2">
            <img src="court3.jpg" class="slide" alt="Court Image 3">
            <img src="court4.jpg" class="slide" alt="Court Image 4">
            <img src="court5.jpg" class="slide" alt="Court Image 5">
            <img src="court6.jpg" class="slide" alt="Court Image 6">
            
            <button class="slideshow-btn prev" onclick="changeSlide(-1)">❮</button>
            <button class="slideshow-btn next" onclick="changeSlide(1)">❯</button>
            
            <div class="slideshow-dots">
                <span class="dot active" onclick="currentSlide(1)"></span>
                <span class="dot" onclick="currentSlide(2)"></span>
                <span class="dot" onclick="currentSlide(3)"></span>
                <span class="dot" onclick="currentSlide(4)"></span>
                <span class="dot" onclick="currentSlide(5)"></span>
                <span class="dot" onclick="currentSlide(6)"></span>
            </div>
        </div>
    </div>

    <script>
        let slideIndex = 1;
        showSlides(slideIndex);

        function changeSlide(n) {
            showSlides(slideIndex += n);
        }

        function currentSlide(n) {
            showSlides(slideIndex = n);
        }

        function showSlides(n) {
            const slides = document.getElementsByClassName("slide");
            const dots = document.getElementsByClassName("dot");
            
            if (n > slides.length) { slideIndex = 1; }
            if (n < 1) { slideIndex = slides.length; }
            
            
            for (let i = 0; i < slides.length; i++) {
                slides[i].classList.remove("active");
            }
            
            
            for (let i = 0; i < dots.length; i++) {
                dots[i].classList.remove("active");
            }
            
            
            slides[slideIndex - 1].classList.add("active");
            dots[slideIndex - 1].classList.add("active");
        }

        
        setInterval(() => {
            changeSlide(1);
        }, 5000);
    </script>
    <?php include('footer.php'); ?>
</body>
</html>
