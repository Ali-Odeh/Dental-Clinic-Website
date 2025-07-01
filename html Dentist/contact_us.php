<?php
// Include the database connection file
require_once 'db_connection.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    $message = htmlspecialchars(trim($_POST['message']));

    // Validate inputs
    if (!empty($name) && !empty($email) && !empty($phone) && !empty($message)) {
        $insert_query = "INSERT INTO messages (name, email, phone, message) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("ssss", $name, $email, $phone, $message);

        if ($stmt->execute()) {
            echo "<script>alert('Message sent successfully!');</script>";
        } else {
            echo "<script>alert('Error sending message. Please try again later.');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('All fields are required.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
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



        .container {
            max-width: 900px;
            margin: 2rem auto;
            background: #fff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0px 6px 10px rgba(0, 0, 0, 0.15);
        }
        .container h1 {
            font-size: 2rem;
            text-align: center;
            margin-bottom: 1rem;
            color: #007bff;
        }
        .field {
            margin-bottom: 1.5rem;
        }
        .field label {
            font-weight: bold;
            display: block;
            margin-bottom: 0.5rem;
        }
        .field input,
        .field textarea {
            width: 100%;
            padding: 0.9rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
            box-sizing: border-box;
        }
        .field textarea {
            height: 120px;
        }

        .contact-info {
            margin-top: 2rem;
        }
        .contact-info h3 {
            color: #007bff;
            margin-bottom: 0.5rem;
        }
        .contact-info p {
            margin: 0.3rem 0;
            font-size: 1rem;
            color: #555;
        }
        .map {
            margin-top: 2rem;
        }
        iframe {
            width: 100%;
            height: 300px;
            border: 0;
            border-radius: 10px;
        }

    </style>
</head>


<body>

<header class="header">
    <nav>
        <div class="logo">Contact Us</div>
        <ul>
            <li><a href="index.html">Home Page</a></li>
        </ul>
    </nav>
</header>

<div class="container">
    <h1>We'd Love to Hear From You!</h1>
    <form method="POST">
        <div class="field">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" placeholder="Your Name" required>
        </div>
        <div class="field">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Your Email" required>
        </div>
        <div class="field">
            <label for="phone">Phone</label>
            <input type="tel" id="phone" name="phone" placeholder="Your Phone Number" required>
        </div>
        <div class="field">
            <label for="message">Message</label>
            <textarea id="message" name="message" placeholder="Type your message here..." required></textarea>
        </div>
        <button type="submit" class="btn">Send Message</button>
    </form>

    <div class="contact-info">
        <h3>Clinic Address 📌</h3>
        <p>123 Smile Street, Ramallah City, Palestinian Ramallah</p>
        <h3>Email 📧</h3>
        <p>info@yourclinic.com</p>
        <h3>Phone 📞</h3>
        <p>+972 59 289 1676</p>
    </div>

    <div class="map">
        <h3>Our Location</h3>
        <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3381.737010828171!2d35.18096941457845!3d31.899639481236328!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x151caad93f3d1631%3A0x31772af4b4306f06!2sRamallah!5e0!3m2!1sen!2s!4v1615234011877!5m2!1sen!2s"
                allowfullscreen=""
                loading="lazy">
        </iframe>
    </div>
</div>
<footer>
    <p id="plp"> <i> plpss (. ❛ ᴗ ❛.) </i> </p>
    <p>&copy; 2025 Your Smile Clinic. All Rights Reserved.</p>
</footer>
</body>
</html>
