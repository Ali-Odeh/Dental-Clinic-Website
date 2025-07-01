<?php
// Include the database connection file
require_once 'db_connection.php';

// Start the session
session_start();

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

// Redirect to login form if not a POST request
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: Login_and_Signup_form.html");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        header("Location: Login_and_Signup_form.html?error=Both email and password are required!");
        exit;
    }

    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] == 'Admin') {
                header("Location: admin_dashboard.php");
            } elseif ($user['role'] == 'Patient') {
                header("Location: patient_dashboard.php");
            } else {
                header("Location: Login_and_Signup_form.html?error=Unknown role. Please contact support.");
            }
            exit;
        } else {
            header("Location: Login_and_Signup_form.html?error=Invalid password!");
        }
    } else {
        header("Location: Login_and_Signup_form.html?error=No account found with this email!");
    }

    $stmt->close();
    $conn->close();
}
?>