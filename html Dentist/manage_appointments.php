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

// Handle approve or delete requests
if (isset($_GET['action']) && isset($_GET['appointment_id'])) {
    $appointment_id = intval($_GET['appointment_id']);
    $action = $_GET['action'];

    if ($action == 'approve') {
        $update_query = "UPDATE appointments SET status = 'Approved' WHERE appointment_id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("i", $appointment_id);
        $stmt->execute();
    } elseif ($action == 'delete') {
        // Delete feedback associated with the appointment
        $delete_feedback_query = "DELETE FROM feedback WHERE appointment_id = ?";
        $stmt = $conn->prepare($delete_feedback_query);
        $stmt->bind_param("i", $appointment_id);
        $stmt->execute();

        // Delete the appointment
        $delete_query = "DELETE FROM appointments WHERE appointment_id = ?";
        $stmt = $conn->prepare($delete_query);
        $stmt->bind_param("i", $appointment_id);
        $stmt->execute();
    }
}

// Handle rejection with a reason
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reject_reason'], $_POST['appointment_id'])) {
    $appointment_id = intval($_POST['appointment_id']);
    $reject_reason = htmlspecialchars(trim($_POST['reject_reason']));

    if (!empty($reject_reason)) {
        $update_query = "UPDATE appointments SET status = 'Rejected', rejection_reason = ? WHERE appointment_id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("si", $reject_reason, $appointment_id);
        $stmt->execute();
        echo "<script>alert('Appointment rejected with a reason.');</script>";
    } else {
        echo "<script>alert('Rejection reason is required.');</script>";
    }
}

// Fetch all appointments, feedback, and rejection reasons
$query = "SELECT a.appointment_id, u.name AS patient_name, s.service_name, a.date, a.time, a.status, a.rejection_reason, f.feedback_text 
          FROM appointments a 
          JOIN users u ON a.user_id = u.user_id 
          JOIN services s ON a.service_id = s.service_id 
          LEFT JOIN feedback f ON a.appointment_id = f.appointment_id";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <title>Manage Appointments</title>
    <link rel="stylesheet" href="style.css">
    <style>

        /* Manage Appointments Section */
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
            background-color: #f1f1f1;
        }

        /* Buttons */
        .btn {
            text-decoration: none;
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
            border-radius: 4px;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
            transition: background-color 0.3s ease, transform 0.2s ease;
            display: inline-block;
        }

        .btn-approve {
            background-color: deepskyblue;
            color: #fff;
        }

        .btn-approve:hover {
            transform: translateY(-2px);
        }

        .btn-reject {
            background-color: deepskyblue;
            color: #fff;
            border: solid deepskyblue;
        }

        .btn-reject:hover {
            transform: translateY(-2px);
        }

        .btn-delete {
            background-color: #d13a3a;
            color: white;
        }

        .btn-delete:hover {

            transform: translateY(-2px);
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 400px;
            padding: 1.5rem;
            z-index: 1000;
        }

        .modal.active {
            display: block;
        }

        .modal h2 {
            margin-top: 0;
            color: #007bff;
        }

        .modal .field {
            margin-bottom: 1rem;
        }

        .modal .field textarea {
            width: 100%;
            height: 80px;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .modal .field textarea:focus {
            border-color: #007bff;
            outline: none;
        }

        .modal .actions {
            display: flex;
            justify-content: space-between;
        }

        .modal .btn {
            padding: 0.5rem 1rem;
            border: none;
            background: #007bff;
            color: #fff;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .modal .btn:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }

        .modal .btn-cancel {
            background-color: #6c757d;
        }

        .modal .btn-cancel:hover {
            background-color: #5a6268;
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

        .feedback-red {
            color: red;
            font-style: italic;
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
            <!--   <li><a href="index.html">Logout</a></li> -->
            <li><a href="logout.php">Logout</a></li>
          </ul>
      </nav>
  </header>

  <!-- Manage Appointments Section -->
<section class="manage-section">
    <h1>Manage Appointments</h1>

    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Patient</th>
            <th>Service</th>
            <th>Date</th>
            <th>Time</th>
            <th>Status</th>
            <th>Rejection Reason</th>
            <th>FB from User</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($appointment = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($appointment['appointment_id']); ?></td>
                <td><?php echo htmlspecialchars($appointment['patient_name']); ?></td>
                <td><?php echo htmlspecialchars($appointment['service_name']); ?></td>
                <td><?php echo htmlspecialchars($appointment['date']); ?></td>
                <td><?php echo htmlspecialchars($appointment['time']); ?></td>
                <td><?php echo htmlspecialchars($appointment['status']); ?></td>
                <td><?php echo htmlspecialchars($appointment['rejection_reason'] ?? 'N/A'); ?></td>
                <td>
                    <?php if (!empty($appointment['feedback_text'])) { ?>
                        <span class="feedback-red"><?php echo htmlspecialchars($appointment['feedback_text']); ?></span>
                    <?php } else { ?>
                        N/A
                    <?php } ?>
                </td>

                <td>
                    <a href="?action=approve&appointment_id=<?php echo $appointment['appointment_id']; ?>" class="btn btn-approve">Approve</a>
                    <button class="btn btn-reject" data-id="<?php echo $appointment['appointment_id']; ?>">Reject</button>
                    <a href="?action=delete&appointment_id=<?php echo $appointment['appointment_id']; ?>" class="btn btn-delete"
                       onclick="return confirm('Are you sure you want to delete this appointment?');">Delete</a>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</section>

<!-- Modal -->
<div id="rejectModal" class="modal">
    <h2>Reject Appointment</h2>
    <form method="POST">
        <input type="hidden" name="appointment_id" id="appointmentId">
        <div class="field">
            <label for="rejectReason">Reason for Rejection</label>
            <textarea id="rejectReason" name="reject_reason" required></textarea>
        </div>
        <div class="actions">
            <button type="submit" class="btn">Submit</button>
            <button type="button" class="btn btn-cancel" onclick="toggleModal(false)">Cancel</button>
        </div>
    </form>
</div>

<!-- Footer -->
<footer>
    <p>&copy; 2025 Your Smile Clinic. All Rights Reserved.</p>
</footer>

<script>
    const rejectButtons = document.querySelectorAll('.btn-reject');
    const modal = document.getElementById('rejectModal');
    const appointmentIdField = document.getElementById('appointmentId');

    rejectButtons.forEach(button => {
        button.addEventListener('click', () => {
            const appointmentId = button.getAttribute('data-id');
            appointmentIdField.value = appointmentId;
            toggleModal(true);
        });
    });

    function toggleModal(show) {
        modal.classList.toggle('active', show);
    }
</script>
</body>
</html>