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

// Handle delete request
if (isset($_GET['delete_user'])) {
    $user_id = intval($_GET['delete_user']);

    $delete_query = "DELETE FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        echo "<script>alert('User deleted successfully.');</script>";
    } else {
        echo "<script>alert('Error deleting user.');</script>";
    }
    $stmt->close();
}

// Fetch all users
$users_query = "SELECT user_id, name, email, role FROM users";
$result = $conn->query($users_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <title>Manage Users</title>
    <style>

        /* Existing CSS styles */
        body {
            font-family: "Cambria Math", serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
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

        /* Rest of your CSS styles */
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            background-color: #fff;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        table th,
        table td {
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
            background-color: #f1f1f1;
        }

        .btn-edit,
        .btn-delete {
            text-decoration: none;
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
            border-radius: 4px;
            margin-right: 0.5rem;
            transition: background-color 0.3s ease, transform 0.2s ease;
            display: inline-block;
        }

        .btn-edit {
            background-color: #28a745;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        .btn-edit:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }

        .btn-delete {
            background-color: #dc3545;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        .btn-delete:hover {
            background-color: #c82333;
            transform: translateY(-2px);
        }

        footer {
            font-family: "Times New Roman", Times, serif;
            text-align: center;
            padding: 0.5rem 2rem;
            background: #0066cc;
            color: white;
            font-size: 0.9rem;
            font-weight: 500;
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

<!-- Manage Users Section -->
<section class="manage-section">
    <h1>Manage Users</h1>
    <table>
        <thead>
        <tr>
            <th>User ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($user = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                <td><?php echo htmlspecialchars($user['name']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo htmlspecialchars($user['role']); ?></td>
                <td>
                    <!-- Link to Manage Users, for this line: -->
                    <a href="edit_user.php?user_id=<?php echo $user['user_id']; ?>" class="btn-edit">Edit</a>
                    <a href="manage_users.php?delete_user=<?php echo $user['user_id']; ?>"
                       class="btn-delete"
                       onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</section>

<!-- Footer -->
<footer>
    <p>&copy; 2025 Admin Dashboard. All Rights Reserved.</p>
</footer>

</body>
</html>
