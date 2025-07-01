<?php
// Start the session
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

// Check if the user is logged in and is a Patient
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Patient') {
    header("Location: login.php");
    exit;
}

// Include the database connection file
require_once 'db_connection.php';

// Get the logged-in patient's ID
$user_id = $_SESSION['user_id'];

// Fetch patient details
$user_query = "SELECT name, email FROM users WHERE user_id = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$patient = $user_result->fetch_assoc();

// Handle delete request for appointment and feedback
if (isset($_GET['delete_appointment'])) {
    $appointment_id = intval($_GET['delete_appointment']);

    // Delete associated feedback
    $delete_feedback_query = "DELETE FROM feedback WHERE appointment_id = ?";
    $stmt = $conn->prepare($delete_feedback_query);
    $stmt->bind_param("i", $appointment_id);
    $stmt->execute();

    // Delete the appointment
    $delete_appointment_query = "DELETE FROM appointments WHERE appointment_id = ?";
    $stmt = $conn->prepare($delete_appointment_query);
    $stmt->bind_param("i", $appointment_id);

    if ($stmt->execute()) {
        echo "<script>alert('Appointment and feedback deleted successfully.');</script>";
    } else {
        echo "<script>alert('Error deleting appointment.');</script>";
    }
    $stmt->close();
}

// Fetch the patient’s appointment history
$appointments_query = "SELECT a.appointment_id, a.date, a.time, a.status, s.service_name, a.rejection_reason 
                       FROM appointments a 
                       JOIN services s ON a.service_id = s.service_id 
                       WHERE a.user_id = ? 
                       ORDER BY a.date DESC, a.time DESC";
$stmt = $conn->prepare($appointments_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$appointments_result = $stmt->get_result();

// Handle feedback submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $appointment_id = intval($_POST['appointment_id']);
    $feedback_text = htmlspecialchars(trim($_POST['feedback_text']));

    if (!empty($feedback_text)) {
        $insert_feedback_query = "INSERT INTO feedback (user_id, appointment_id, feedback_text) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insert_feedback_query);
        $stmt->bind_param("iis", $user_id, $appointment_id, $feedback_text);

        if ($stmt->execute()) {
            echo "<script>alert('Feedback submitted successfully.');</script>";
        } else {
            echo "<script>alert('Error submitting feedback.');</script>";
        }
    } else {
        echo "<script>alert('Feedback cannot be empty.');</script>";
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

    <title>Patient Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* General Styles */

        /* Header */
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

        /* Dashboard Section */
        .dashboard {
            padding: 1rem;
            max-width: 90%;
            margin: 0 auto;
        }

        .dashboard h1 {
            font-size: 2rem;
            color: #007bff;
            margin-bottom: 1rem;
        }

        .dashboard p {
            font-size: 1rem;
            color: #555;
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            background-color: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        table th,
        table td {
            padding: 0.8rem;
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

        /* Feedback Form */
        textarea {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        textarea:focus {
            border-color: #007bff;
            outline: none;
        }

        /* Buttons */
        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn-submit {
            background-color: #28a745;
            color: #fff;
        }

        .btn-submit:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }

        .btn-delete {
            background-color: #dc3545; /* Red color */
            color: #fff;
        }

        .btn-delete:hover {
            background-color: #c82333; /* Darker red on hover */
            transform: translateY(-2px); /* Slight lift effect */
        }


        /* Responsive Design */
        @media (max-width: 768px) {
            .header {
                padding: 1rem;
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

            .dashboard {
                padding: 1rem;
            }

            .dashboard h1 {
                font-size: 1.8rem;
            }

            .dashboard p {
                font-size: 0.9rem;
            }

            table {
                display: block;
                overflow-x: auto;
            }

            table th, table td {
                padding: 0.5rem;
                font-size: 0.9rem;
            }

            textarea {
                font-size: 0.9rem;
            }

            .btn {
                padding: 0.4rem 0.8rem;
                font-size: 0.8rem;
            }
        }

        @media (max-width: 480px) {
            .header .logo {
                font-size: 1.2rem;
            }

            .header ul li a {
                font-size: 0.8rem;
            }

            .dashboard h1 {
                font-size: 1.5rem;
            }

            .dashboard p {
                font-size: 0.8rem;
            }

            table th, table td {
                padding: 0.4rem;
                font-size: 0.8rem;
            }

            textarea {
                font-size: 0.8rem;
            }

            .btn {
                padding: 0.3rem 0.6rem;
                font-size: 0.7rem;
            }
        }

        .rejection-reason {
            color: red;
            font-style: italic;
        }
    </style>
</head>
<body>
<!-- Header -->
<header class="header">
    <div class="logo">Your Smile Clinic</div>
    <ul>
        <li><a href="appointment.php">Book Appointment</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</header>

<!-- Main Dashboard Section -->
<section class="dashboard">
    <h1>Welcome, <?php echo htmlspecialchars($patient['name']); ?>!</h1>
    <p>Email: <?php echo htmlspecialchars($patient['email']); ?></p>

    <!-- Appointment History -->
    <h2>Your Appointments</h2>
    <?php if ($appointments_result->num_rows > 0) { ?>
        <table>
            <thead>
            <tr>
                <th>Date</th>
                <th>Time</th>
                <th>Service</th>
                <th>Status</th>
                <th>Feedback / Rejection Reason</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($appointment = $appointments_result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($appointment['date']); ?></td>
                    <td><?php echo htmlspecialchars($appointment['time']); ?></td>
                    <td><?php echo htmlspecialchars($appointment['service_name']); ?></td>
                    <td><?php echo htmlspecialchars($appointment['status']); ?></td>
                    <td>
                        <?php if ($appointment['status'] == 'Approved') { ?>
                            <form method="POST">
                                <input type="hidden" name="appointment_id" value="<?php echo $appointment['appointment_id']; ?>">
                                <textarea name="feedback_text" rows="2" placeholder="Leave your feedback"></textarea>
                                <button type="submit" class="btn btn-submit">Submit</button>
                            </form>
                        <?php } elseif ($appointment['status'] == 'Rejected' && !empty($appointment['rejection_reason'])) { ?>
                            <div class="rejection-reason">
                                <strong>Rejection Reason:</strong> <?php echo htmlspecialchars($appointment['rejection_reason']); ?>
                            </div>
                        <?php } else { ?>
                            N/A
                        <?php } ?>
                    </td>
                    <td>
                        <a href="patient_dashboard.php?delete_appointment=<?php echo $appointment['appointment_id']; ?>"
                           class="btn btn-delete"
                           onclick="return confirm('Are you sure you want to delete this appointment and its feedback?');">
                            Delete
                        </a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <p>No appointments found.</p>
    <?php } ?>
</section>

<!-- Footer -->
<footer>
    <p>&copy; 2025 Your Smile Clinic. All Rights Reserved.</p>
</footer>
</body>
</html>