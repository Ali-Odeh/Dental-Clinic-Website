<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

// Check if the user is logged in and is an Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin') {
    header("Location: login.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* General Styles */
        body {
            font-family: "Cambria Math", serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            color: #333;
        }
        .header nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            gap: 1rem; /* Space between buttons */
        }

        .header nav ul li {
            display: inline-block;
        }

        .header nav ul li a {
            text-decoration: none;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            font-size: 1.2rem;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }


        .header nav ul li a:hover {
            background-color:#ffcc00;
            color: black;
        }

        /* Dashboard Section */
        .dashboard {
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .dashboard h1 {
            font-size: 2.5rem;
            color: #0047ab;
            margin-bottom: 2rem;
            text-align: center;
        }

        .admin-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .admin-card {
            background-color: #fff;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-decoration: none;
            color: #333;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .admin-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .admin-card h3 {
            font-size: 1.5rem;
            color: #0047ab;
            margin-bottom: 0.5rem;
        }

        .admin-card p {
            font-size: 1rem;
            color: #666;
        }


    </style>
</head>
<body>
<!-- Header -->
<header class="header">
    <nav>
        <div class="logo">Admin Dashboard</div>
        <ul>
           <!-- <li><a href="index.html">Logout</a></li>  -->
            <li><a href="logout.php">Logout</a></li>

        </ul>
    </nav>
</header>

<!-- Dashboard Section -->
<section class="dashboard">
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h1>
    <div class="admin-actions">
        <a href="manage_users.php" class="admin-card">
            <h3>Manage Users</h3>
            <p>Edit or delete user accounts.</p>
        </a>
        <a href="manage_appointments.php" class="admin-card">
            <h3>Manage Appointments</h3>
            <p>View, approve, or update appointments.</p>
        </a>
        <a href="manage_services.php" class="admin-card">
            <h3>Manage Services</h3>
            <p>Add, update, or remove clinic services.</p>
        </a>
        <a href="manage_gallery.php" class="admin-card">
            <h3>Manage Gallery</h3>
            <p>Upload or delete gallery images.</p>
        </a>
        <a href="manage_msgs.php" class="admin-card">
            <h3>Manage Messages</h3>
            <p>Review and process consultation requests.</p>
        </a>
    </div>
</section>

<!-- Footer -->
<footer>
    <p>&copy; 2025 Admin Dashboard. All Rights Reserved.</p>
</footer>
</body>
</html>
