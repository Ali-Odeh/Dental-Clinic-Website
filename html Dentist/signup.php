<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the database connection file
require_once 'db_connection.php';

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect and sanitize form data
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $role = 'patient';

    // Validate input fields
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        header("Location: Login_and_Signup_form.html?error=All fields are required!&type=signup");
        exit;
    }

    // Explicitly check password match
    if ($password !== $confirm_password) {
        header("Location: Login_and_Signup_form.html?error=Passwords do not match!&type=signup");
        exit;
    }

    // Check if email already exists
    $check_email_query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($check_email_query);
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        header("Location: Login_and_Signup_form.html?error=Email is already registered!&type=signup");
        exit;
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user data into the database
    $insert_query = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("ssss", $name, $email, $hashed_password, $role);

    if ($stmt->execute()) {
        // Redirect to login page with success message
        header("Location: Login_and_Signup_form.html?message=Signup successful! Please log in.&type=login");
        exit;
    } else {
        header("Location: Login_and_Signup_form.html?error=Something went wrong. Please try again.&type=signup");
        echo "Error: " . $conn->error; // Debug output
    }

    $stmt->close();
    $conn->close();
}
?>