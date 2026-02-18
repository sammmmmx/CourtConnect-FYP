<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="main-nav">
    
    <div class="nav-brand">
        <img src="logo.png" alt="CourtConnect Logo" class="nav-logo">
        <span class="brand-text">CourtConnect</span>
    </div>
    
    
    <div class="menu-toggle" onclick="toggleMenu()">☰</div>
    
    
    <div class="nav-menu">
        <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
            
            <a href="index.php">HOME</a>
            <a href="booking.php">BADMINTON</a>
            <a href="profile.php">PROFILE</a>
            <a href="logout.php">LOGOUT</a>
            
        <?php elseif (isset($_SESSION['admin_loggedin']) && $_SESSION['admin_loggedin'] === true): ?>
            
            <a href="admin_dashboard.php">DASHBOARD</a>
            <a href="admin_bookings.php">BOOKINGS</a>
            <a href="admin_courts.php">COURTS</a>
            <a href="admin_users.php">USERS</a>
            <a href="logout.php">LOGOUT</a>
            
        <?php else: ?>
            
            <a href="index.php">HOME</a>
            <a href="login.php">LOGIN</a>
            <a href="register.php">REGISTER</a>
        <?php endif; ?>
    </div>
</nav>

<style>
    .main-nav {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: rgba(255, 255, 255, 0.95);
        padding: 15px 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1000;
    }
    .nav-brand {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .nav-logo {
        width: 45px;
        height: 45px;
    }
    .brand-text {
        font-family: 'Poppins', sans-serif;
        font-size: 1.5rem;
        font-weight: 700;
        color: #2e7d32;
        text-transform: uppercase;
    }
    .nav-menu {
        display: flex;
        gap: 30px;
    }
    .nav-menu a {
        color: #333;
        text-decoration: none;
        font-weight: 600;
        font-size: 1rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: color 0.3s ease;
    }
    .nav-menu a:hover {
        color: #4caf50;
    }

    
    .menu-toggle {
        display: none;
        font-size: 1.8rem;
        cursor: pointer;
        color: #2e7d32;
        user-select: none;
    }

    @media (max-width: 768px) {
        .nav-menu {
            display: none;
            flex-direction: column;
            position: absolute;
            top: 70px;
            right: 20px;
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            gap: 15px;
        }
        .nav-menu.active {
            display: flex;
        }
        .menu-toggle {
            display: block;
        }
    }
</style>

<script>
    function toggleMenu() {
        const navMenu = document.querySelector('.nav-menu');
        const menuToggle = document.querySelector('.menu-toggle');
        navMenu.classList.toggle('active');
        
        
        if (navMenu.classList.contains('active')) {
            menuToggle.textContent = '✖';
        } else {
            menuToggle.textContent = '☰';
        }
    }

    
    document.addEventListener('DOMContentLoaded', function() {
        const navLinks = document.querySelectorAll('.nav-menu a');
        const menuToggle = document.querySelector('.menu-toggle');
        const navMenu = document.querySelector('.nav-menu');

        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                navMenu.classList.remove('active');
                menuToggle.textContent = '☰'; 
            });
        });
    });
</script>
