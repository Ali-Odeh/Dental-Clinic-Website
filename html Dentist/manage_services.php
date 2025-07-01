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

// Handle adding a new service
if (isset($_POST['add_service'])) {
    $service_name = htmlspecialchars(trim($_POST['service_name']));
    $description = htmlspecialchars(trim($_POST['description']));

    if (!empty($service_name) && !empty($description)) {
        $add_query = "INSERT INTO services (service_name, description) VALUES (?, ?)";
        $stmt = $conn->prepare($add_query);
        $stmt->bind_param("ss", $service_name, $description);

        if ($stmt->execute()) {
            echo "<script>alert('Service added successfully.');</script>";
        } else {
            echo "<script>alert('Error adding service.');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Both fields are required.');</script>";
    }
}

// Handle deleting a service
if (isset($_GET['delete_service'])) {
    $service_id = intval($_GET['delete_service']);

    $delete_query = "DELETE FROM services WHERE service_id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $service_id);

    if ($stmt->execute()) {
        echo "<script>alert('Service deleted successfully.');</script>";
    } else {
        echo "<script>alert('Error deleting service.');</script>";
    }
    $stmt->close();
}

// Fetch all services
$services_query = "SELECT * FROM services";
$result = $conn->query($services_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <title>Manage Services</title>
    <link rel="stylesheet" href="style.css">
    <style>/* Reset and base styles */

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
        /* Main content styles */
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


        /* Form styles */
        .add-service-form {
            background-color: #fff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .add-service-form h2 {
            font-size: 1.5rem;
            color: #2c3e50;
            margin-bottom: 1rem;
        }

        .field {
            margin-bottom: 1rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #34495e;
        }

        input[type="text"],
        textarea {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #bdc3c7;
            border-radius: 4px;
            font-size: 1rem;
        }

        textarea {
            resize: vertical;
        }

        .btn input[type="submit"] {
            background-color: #3498db;
            color: #fff;
            border: none;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn input[type="submit"]:hover {
            background-color: #2980b9;
        }

        /* Table styles */
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #ecf0f1;
        }

        th {
            background-color: #007bff;
            color: #ecf0f1;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .btn-edit, .btn-delete {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            text-decoration: none;
            color: #fff;
            font-size: 0.9rem;
            transition: background-color 0.3s ease;
        }

        .btn-edit {
            background-color: #2ecc71;
            margin-right: 0.5rem;
        }

        .btn-delete {
            background-color: #e74c3c;
        }

        .btn-edit:hover {
            background-color: #27ae60;
        }

        .btn-delete:hover {
            background-color: #c0392b;
        }



        /* Responsive design */
        @media (max-width: 768px) {
            .header nav {
                flex-direction: column;
                align-items: flex-start;
            }

            .header ul {
                margin-top: 1rem;
            }

            .header ul li {
                margin-left: 0;
                margin-right: 1rem;
            }

            .manage-section {
                padding: 0 1rem;
            }

            table {
                font-size: 0.9rem;
            }

            th, td {
                padding: 0.75rem;
            }

            .btn-edit, .btn-delete {
                padding: 0.4rem 0.8rem;
            }
        }</style>
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

<!-- Manage Services Section -->
<section class="manage-section">
    <h1>Manage Services</h1>

    <!-- Add Service Form -->
    <form method="POST" class="add-service-form">
        <h2>Add New Service</h2>
        <div class="field">
            <label for="service_name">Service Name</label>
            <input type="text" id="service_name" name="service_name" required>
        </div>
        <div class="field">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="3" required></textarea>
        </div>
        <div class="field btn">
            <input type="submit" name="add_service" value="Add Service">
        </div>
    </form>

    <!-- Services Table -->
    <table>
        <thead>
        <tr>
            <th>Service ID</th>
            <th>Service Name</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($service = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($service['service_id']); ?></td>
                <td><?php echo htmlspecialchars($service['service_name']); ?></td>
                <td><?php echo htmlspecialchars($service['description']); ?></td>
                <td>
                    <a href="edit_service.php?service_id=<?php echo $service['service_id']; ?>" class="btn-edit">Edit</a>
                    <a href="manage_services.php?delete_service=<?php echo $service['service_id']; ?>"
                       class="btn-delete"
                       onclick="return confirm('Are you sure you want to delete this service?');">Delete</a>
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

