<?php
session_start();
include('./includes/dbconfig.php');


if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("Location: admin_login.php");
    exit;
}

$action_message = '';


if (isset($_GET['success']) && $_GET['success'] == 1) {
    $action_message = "Court added successfully!";
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_court'])) {
    $name = mysqli_real_escape_string($link, $_POST['name']);
    $type = mysqli_real_escape_string($link, $_POST['type']);
    $price = mysqli_real_escape_string($link, $_POST['price']);
    $description = mysqli_real_escape_string($link, $_POST['description']);
    $status = mysqli_real_escape_string($link, $_POST['status']);

    $sql = "INSERT INTO courts (name, type, price, description, status) 
            VALUES ('$name', '$type', '$price', '$description', '$status')";
    
    if (mysqli_query($link, $sql)) {
        
        header("Location: admin_courts.php?success=1");
        exit();
    } else {
        $action_message = "Error adding court: " . mysqli_error($link);
    }
}


if (isset($_GET['delete_court'])) {
    $court_id = mysqli_real_escape_string($link, $_GET['court_id']);
    
    $delete_sql = "DELETE FROM courts WHERE id = $court_id";
    if (mysqli_query($link, $delete_sql)) {
        $action_message = "Court deleted successfully!";
    } else {
        $action_message = "Error deleting court: " . mysqli_error($link);
    }
}


$courts_query = "SELECT * FROM courts ORDER BY id";
$courts_result = mysqli_query($link, $courts_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CourtConnect - Manage Courts</title>
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
        .courts-table {
            width: 100%;
            background: white;
            border-collapse: collapse;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .courts-table th, .courts-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .courts-table th {
            background-color: #2e7d32;
            color: white;
        }
        .add-court-form {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        input, select, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 2px;
            text-decoration: none;
            display: inline-block;
        }
        .btn-add { background-color: #28a745; color: white; }
        .btn-delete { background-color: #dc3545; color: white; }
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
        <h1>Manage Courts</h1>
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

    <h2>Existing Courts</h2>
    <table class="courts-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Type</th>
                <th>Price/Hour</th>
                <th>Description</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($court = mysqli_fetch_assoc($courts_result)): ?>
            <tr>
                <td><?php echo $court['id']; ?></td>
                <td><?php echo $court['name']; ?></td>
                <td><?php echo $court['type']; ?></td>
                <td>RM <?php echo number_format($court['price'], 2); ?></td>
                <td><?php echo $court['description']; ?></td>
                <td><?php echo $court['status']; ?></td>
                <td>
                    <a href="admin_courts.php?court_id=<?php echo $court['id']; ?>&delete_court=1" class="btn btn-delete" onclick="return confirm('Are you sure? This will also delete all bookings for this court!')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="add-court-form">
        <h2>Add New Court</h2>
        <form method="POST" action="admin_courts.php">
            <div class="form-group">
                <label for="name">Court Name:</label>
                <input type="text" id="name" name="name" required placeholder="e.g., Court 4">
            </div>
            <div class="form-group">
                <label for="type">Court Type:</label>
                <select id="type" name="type" required>
                    <option value="VVIP">VVIP</option>
                    <option value="Normal">Normal</option>
                </select>
            </div>
            <div class="form-group">
                <label for="price">Price per Hour (RM):</label>
                <input type="number" id="price" name="price" step="0.01" min="0" required placeholder="e.g., 25.00">
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" placeholder="e.g., Blue court, air-conditioned"></textarea>
            </div>
            <div class="form-group">
                <label for="status">Status:</label>
                <select id="status" name="status" required>
                    <option value="Available">Available</option>
                    <option value="Not Available">Not Available</option>
                </select>
            </div>
            <button type="submit" name="add_court" class="btn btn-add">Add Court</button>
        </form>
    </div>
</body>
</html>
