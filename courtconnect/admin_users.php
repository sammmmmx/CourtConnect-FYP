<?php
session_start();
include('./includes/dbconfig.php');


if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("Location: admin_login.php");
    exit;
}

$action_message = '';


if (isset($_GET['delete_user'])) {
    $user_id = mysqli_real_escape_string($link, $_GET['user_id']);
    
    
    $delete_bookings_sql = "DELETE FROM bookings WHERE user_id = $user_id";
    mysqli_query($link, $delete_bookings_sql);
    
    
    $delete_user_sql = "DELETE FROM users WHERE id = $user_id";
    
    if (mysqli_query($link, $delete_user_sql)) {
        $action_message = "User and their associated bookings deleted successfully!";
    } else {
        $action_message = "Error deleting user: " . mysqli_error($link);
    }
}


$users_query = "SELECT id, name, email, phone, created_at FROM users ORDER BY created_at DESC";
$users_result = mysqli_query($link, $users_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CourtConnect - Manage Users</title>
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
        .users-table {
            width: 100%;
            background: white;
            border-collapse: collapse;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .users-table th, .users-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .users-table th {
            background-color: #2e7d32;
            color: white;
        }
        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 0.9em;
        }
        .btn-delete { 
            background-color: #dc3545; 
            color: white; 
        }
        .btn-delete:hover {
            background-color: #c82333;
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
        .user-role {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 0.8em;
            font-weight: bold;
        }
        .role-admin { 
            background-color: #dc3545; 
            color: white; 
        }
        .role-user { 
            background-color: #6c757d; 
            color: white; 
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Manage Users</h1>
        <p>Welcome, <?php echo $_SESSION['admin_name']; ?>! | <a href="logout.php">Logout</a></p>
    </div>

    <div class="nav-menu">
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="admin_bookings.php">Manage Bookings</a>
        <a href="admin_courts.php">Manage Courts</a>
        <a href="admin_users.php">Manage Users</a>
    </div>

    <?php if (!empty($action_message)): ?>
        <div class="alert alert-success"><?php echo $action_message; ?></div>
    <?php endif; ?>

    <table class="users-table">
        <thead>
            <tr>
                <th>User ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Role</th>
                <th>Joined Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($user = mysqli_fetch_assoc($users_result)): 
                
                $is_admin = ($user['email'] == 'admin@gmail.com' || $user['email'] == 'admin.ss@gmail.com') ? true : false;
            ?>
            <tr>
                <td><?php echo $user['id']; ?></td>
                <td><?php echo htmlspecialchars($user['name']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo htmlspecialchars($user['phone']); ?></td>
                <td>
                    <span class="user-role <?php echo $is_admin ? 'role-admin' : 'role-user'; ?>">
                        <?php echo $is_admin ? 'Administrator' : 'User'; ?>
                    </span>
                </td>
                <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                <td>
                    <?php if (!$is_admin): // Only allow deletion of regular users ?>
                        <a href="admin_users.php?user_id=<?php echo $user['id']; ?>&delete_user=1" 
                           class="btn btn-delete" 
                           onclick="return confirm('WARNING: This will permanently delete this user AND all their bookings. Continue?')">
                            Delete
                        </a>
                    <?php else: ?>
                        <em>Protected</em>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
