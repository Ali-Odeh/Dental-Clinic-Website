<?php
// Include the database connection file
require_once 'db_connection.php';

// Fetch all gallery images
$gallery_query = "SELECT * FROM gallery ORDER BY uploaded_at DESC";
$gallery_result = $conn->query($gallery_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Dental Care Gallery</title>
    <link rel="stylesheet" href="style.css">

    <style>
        :root {
            --primary-color: #3b82f6;
            --primary-dark: #2563eb;
            --background-light: #f0f9ff;
            --text-color: #1f2937;
            --text-light: #6b7280;
        }

        .header {
            background: linear-gradient(90deg, #0047ab, #89cff0);
            color: white;
            padding: 10px 20px;
            position: sticky;
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

        .header .hamburger {
            display: none;
            flex-direction: column;
            cursor: pointer;
        }

        .header .hamburger span {
            background-color: white;
            height: 3px;
            width: 25px;
            margin: 4px 0;
            transition: all 0.3s ease-in-out;
        }

        .header .nav-menu {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            gap: 1rem;
        }

        .header .nav-menu.active {
            display: flex;
            flex-direction: column;
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            background-color: #0047ab;
            z-index: 1000;
        }

        .header .nav-menu li {
            display: inline-block;
            text-align: center;
        }

        .header .nav-menu li a {
            text-decoration: none;
            color: white;
            padding: 0.5rem 1rem;
            display: block;
            border-radius: 4px;
            font-size: 1.2rem;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .header .nav-menu li a:hover {
            background-color: #ffcc00;
            color: black;
        }

        @media (max-width: 768px) {
            .header .hamburger {
                display: flex;
            }

            .header .nav-menu {
                display: none;
                flex-direction: column;
                align-items: center;
                width: 100%;
            }
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .page-title {
            background-color: var(--background-light);
            padding: 4rem 0;
            text-align: center;
        }

        .page-title h2 {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .page-title p {
            font-size: 1.2rem;
            color: var(--text-light);
            max-width: 600px;
            margin: 0 auto;
        }

        .gallery {
            padding: 4rem 0;
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }

        .gallery-item {
            overflow: hidden;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .gallery-item:hover {
            transform: scale(1.05);
        }

        .gallery-item img {
            width: 100%;
            height: auto;
            display: block;
        }

        footer {
            text-align: center;
            padding: 2rem;
            background: var(--primary-color);
            color: white;
        }
    </style>
</head>
<body>

<header class="header">
    <nav>
        <div class="logo">Your Smile Clinic</div>
        <button class="hamburger" aria-label="Toggle menu">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <ul class="nav-menu">
            <li><a href="index.html">Home</a></li>
            <li><a href="about.html">About</a></li>
            <li><a href="services.html">Services</a></li>
            <li><a href="contact_us.php">Contact</a></li>
        </ul>
    </nav>
</header>

<main>
    <section class="page-title">
        <div class="container">
            <h2>Explore Our Smile Transformations</h2>
            <p>Explore our state-of-the-art facilities and happy patient smiles.</p>
        </div>
    </section>

    <section id="gallery" class="gallery">
        <div class="container">
            <div class="gallery-grid">
                <?php while ($row = $gallery_result->fetch_assoc()) { ?>
                    <div class="gallery-item">
                        <img src="<?php echo htmlspecialchars($row['image_path']); ?>" alt="Dental Gallery Image">
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>
</main>

<footer>
    <p>&copy; 2025 DentistCare. All rights reserved.</p>
</footer>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const hamburger = document.querySelector('.hamburger');
        const navMenu = document.querySelector('.nav-menu');

        hamburger.addEventListener('click', function () {
            navMenu.classList.toggle('active');
        });
    });
</script>
</body>
</html>
