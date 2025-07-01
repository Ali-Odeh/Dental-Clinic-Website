<?php
// Start the session
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Include the database connection file
require_once 'db_connection.php';

// Fetch services for the dropdown
$services_query = "SELECT service_id, service_name FROM services";
$services_result = $conn->query($services_query);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id']; // Get the logged-in user's ID
    $service_id = intval($_POST['service_id']);
    $date = htmlspecialchars(trim($_POST['date']));
    $time = htmlspecialchars(trim($_POST['time']));

    // Validate inputs
    if (empty($service_id) || empty($date) || empty($time)) {
        echo "<script>alert('All fields are required.');</script>";
    } else {
        // Check if the selected date and time are already booked with approved status
        $check_query = "SELECT * FROM appointments WHERE date = ? AND time = ? AND status = 'Approved'";
        $stmt = $conn->prepare($check_query);
        $stmt->bind_param("ss", $date, $time);
        $stmt->execute();
        $check_result = $stmt->get_result();

        if ($check_result->num_rows > 0) {
            echo "<script>alert('The selected time slot is already booked. Please choose a different time.');</script>";
        } else {
            // Insert appointment into the database
            $insert_query = "INSERT INTO appointments (user_id, service_id, date, time, status) VALUES (?, ?, ?, ?, 'Pending')";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param("iiss", $user_id, $service_id, $date, $time);

            if ($stmt->execute()) {
                echo "<script>alert('Appointment booked successfully!');</script>";
            } else {
                echo "<script>alert('Error booking appointment. Please try again later.');</script>";
            }
        }

        $stmt->close();
    }
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

    <title>Book an Appointment</title>
    <link rel="stylesheet" href="style.css">
    <style>

        .header {
            background: linear-gradient(90deg, #007bff, #0056b3);
            color: #fff;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .header .logo {
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 1rem;
        }

        .header ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .header ul li {
            margin: 0;
        }

        .header ul li a {
            color: #fff;
            text-decoration: none;
            font-weight: bold;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .header ul li a:hover {
            background-color:#ffcc00;
            color: black;
        }
        .booking {
            padding: 2rem;
            margin: 0 auto;
            max-width: 500px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }
        .booking h1 {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #333;
        }
        .field {
            margin-bottom: 1rem;
        }
        .field label {
            display: block;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .field input, .field select {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .field .btn input {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 0.7rem 1.5rem;
            border-radius: 4px;
            cursor: pointer;
        }
        .field .btn input:hover {
            background-color: #0056b3;
        }
        .hours-table {
            margin: 2rem auto;
            max-width: 300px;
            background-color: #007bff;
            color: #fff;
            border-radius: 8px;
            padding: 1rem;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }
        .hours-table h2 {
            margin: 0 0 1rem;
            font-size: 1.5rem;
            text-align: center;
            text-transform: uppercase;
        }
        .hours-table table {
            width: 100%;
            border-collapse: collapse;
        }
        .hours-table th, .hours-table td {
            text-align: left;
            padding: 0.5rem;
            font-size: 1rem;
        }
        .hours-table th {
            font-weight: bold;
            color: #f1f1f1;
        }
        .hours-table tr:nth-child(odd) {
            background-color: #0069d9;
        }
        .hours-table tr:nth-child(even) {
            background-color: #0056b3;
        }
        .hours-table td {
            color: #f8f9fa;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .header .logo {
                font-size: 1.5rem;
            }

            .header ul {
                flex-direction: column;
                gap: 0.5rem;
            }

            .header ul li a {
                padding: 0.5rem;
            }

            .booking {
                padding: 1rem;
            }

            .booking h1 {
                font-size: 1.8rem;
            }

            .field input, .field select {
                font-size: 0.9rem;
            }

            .field .btn input {
                font-size: 0.9rem;
            }

            .hours-table {
                margin: 1rem auto;
                padding: 0.8rem;
            }

            .hours-table h2 {
                font-size: 1.2rem;
            }

            .hours-table th, .hours-table td {
                font-size: 0.9rem;
            }
        }

        @media (max-width: 480px) {
            .header .logo {
                font-size: 1.2rem;
            }

            .header ul li a {
                font-size: 0.8rem;
            }

            .booking h1 {
                font-size: 1.5rem;
            }

            .field input, .field select {
                font-size: 0.8rem;
            }

            .field .btn input {
                font-size: 0.8rem;
            }

            .hours-table h2 {
                font-size: 1rem;
            }

            .hours-table th, .hours-table td {
                font-size: 0.8rem;
            }
        }

    </style>
</head>


<body>
<!-- Header -->
<header class="header">
    <div class="logo">Your Smile Clinic</div>
    <ul>
        <li><a href="patient_dashboard.php">Home</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</header>

<!-- Hours Section -->
<div class="hours-table">
    <h2>Hours</h2>
    <table>
        <tbody>
        <tr><td>Monday</td><td>9 AM – 5 PM</td></tr>
        <tr><td>Tuesday</td><td>9 AM – 5 PM</td></tr>
        <tr><td>Wednesday</td><td>9 AM – 5 PM</td></tr>
        <tr><td>Thursday</td><td>9 AM – 5 PM</td></tr>
        <tr><td>Friday</td><td>9 AM – 5 PM</td></tr>
        <tr><td>Weekends</td><td>Closed</td></tr>
        </tbody>
    </table>
</div>

<!-- Booking Section -->
<section class="booking">
    <h1>Book an Appointment</h1>
    <form method="POST">
        <div class="field">
            <label for="service">Select Service</label>
            <select id="service" name="service_id" required>
                <option value="" disabled selected>-- Choose a Service --</option>
                <?php while ($service = $services_result->fetch_assoc()) { ?>
                    <option value="<?php echo htmlspecialchars($service['service_id']); ?>">
                        <?php echo htmlspecialchars($service['service_name']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="field">
            <label for="date">Preferred Date</label>
            <input type="date" id="date" name="date" required>
        </div>
        <div class="field">
            <label for="time">Preferred Time</label>
            <input type="time" id="time" name="time" required>
        </div>
        <div class="field btn">
            <input type="submit" value="Book Appointment">
        </div>
    </form>
</section>

<!-- Footer -->
<footer>
    <p>&copy; 2025 Your Smile Clinic. All Rights Reserved.</p>
</footer>


</body>
</html>
