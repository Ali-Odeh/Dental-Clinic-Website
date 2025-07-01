<?php
// Start the session
session_start();

// Check if the user is logged in and is an Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin') {
    header("Location: login.php");
    exit;
}

// Include the database connection file
require_once 'db_connection.php';

// Check if service_id is provided in the URL
if (!isset($_GET['service_id']) || !is_numeric($_GET['service_id'])) {
    echo "Invalid service ID.";
    exit;
}

$service_id = intval($_GET['service_id']);

// Fetch the service details
$query = "SELECT * FROM services WHERE service_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $service_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Service not found.";
    exit;
}

$service = $result->fetch_assoc();

// Handle form submission for updating service details
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $service_name = htmlspecialchars(trim($_POST['service_name']));
    $description = htmlspecialchars(trim($_POST['description']));

    // Validate inputs
    if (empty($service_name) || empty($description)) {
        echo "All fields are required.";
        exit;
    }

    // Update service details in the database
    $update_query = "UPDATE services SET service_name = ?, description = ? WHERE service_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ssi", $service_name, $description, $service_id);

    if ($stmt->execute()) {
        echo "<script>alert('Service updated successfully.');</script>";
        header("Location: manage_services.php");
        exit;
    } else {
        echo "Error updating service.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Service</title>
    <link rel="stylesheet" href="style.css">
    <style>
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

<!-- Edit Service Section -->
<section class="edit-service-section">
    <h1>Edit Service</h1>
    <form method="POST">
        <div class="field">
            <label for="service_name">Service Name</label>
            <input type="text" id="service_name" name="service_name"
                   value="<?php echo htmlspecialchars($service['service_name']); ?>" required>
        </div>
        <div class="field">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="3" required>
                <?php echo htmlspecialchars($service['description']); ?>
            </textarea>
        </div>
        <div class="field btn">
            <input type="submit" value="Update">
        </div>
    </form>
</section>

<!-- Footer -->
<footer>
    <p>&copy; 2025 Admin Dashboard. All Rights Reserved.</p>
</footer>
</body>
</html>
