<?php
// Start the session
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

// Include the database connection file
require_once 'db_connection.php';

// Fetch messages from the database
$query = "SELECT * FROM messages ORDER BY created_at DESC";
$result = $conn->query($query);

// Handle message deletion
if (isset($_GET['delete'])) {
    $message_id = intval($_GET['delete']); // Sanitize the input

    // Delete the message from the database
    $delete_query = "DELETE FROM messages WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $message_id);

    if ($stmt->execute()) {
        echo "<script>alert('Message deleted successfully!'); window.location.href='manage_msgs.php';</script>";
    } else {
        echo "<script>alert('Error deleting message. Please try again.'); window.location.href='manage_msgs.php';</script>";
    }

    $stmt->close();
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

    <title>Manage Messages</title>
    <link rel="stylesheet" href="style.css">
    <style>

        .manage-section {
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .manage-section h1 {
            font-size: 2.5rem;
            color: #007bff;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .header {
            background: linear-gradient(90deg, #0047ab, #89cff0);
            color: white;
            padding: 10px 20px;
            position: relative;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            color: white;
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

        /* Container */
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            background-color: #fff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .container h2 {
            color: #007bff;
            margin-bottom: 1.5rem;
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1.5rem;
            background-color: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        table th, table td {
            padding: 1rem;
            text-align: left;
        }

        table th {
            background-color: #007bff;
            color: #fff;
            font-weight: bold;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tr:hover {
            background-color: #eef4ff;
        }

        /* Delete Button */
        .btn-delete {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            background-color: #dc3545;
            color: #fff;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn-delete:hover {
            background-color: #c82333;
            transform: translateY(-2px);
        }
    </style>
</head>


<body>

<!-- Header -->
<header class="header">
    <nav>
        <div class="logo">Admin Dashboard</div>
        <ul>
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
</header>

<section class="manage-section">
    <h1>Messages</h1>
<div class="container">
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Message</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($message = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($message['id']); ?></td>
                <td><?php echo htmlspecialchars($message['name']); ?></td>
                <td><?php echo htmlspecialchars($message['email']); ?></td>
                <td><?php echo htmlspecialchars($message['phone']); ?></td>
                <td><?php echo htmlspecialchars($message['message']); ?></td>
                <td><?php echo htmlspecialchars($message['created_at']); ?></td>
                <td>
                    <a href="manage_msgs.php?delete=<?php echo $message['id']; ?>"
                       class="btn-delete"
                       onclick="return confirm('Are you sure you want to delete this message?');">
                        Delete
                    </a>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
</section>
<!-- Footer -->
<footer>
    <p>&copy; 2025 Admin Dashboard. All Rights Reserved.</p>
</footer>
</body>
</html>
