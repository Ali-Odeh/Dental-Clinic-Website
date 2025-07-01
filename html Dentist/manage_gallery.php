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

// Handle image upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['gallery_image'])) {
    $target_dir = "uploads/gallery/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true); // Create the directory with write permissions
    }
    $target_file = $target_dir . basename($_FILES["gallery_image"]["name"]);
    $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $valid_extensions = ["jpg", "jpeg", "png", "gif"];

    if (in_array($image_file_type, $valid_extensions)) {
        if (move_uploaded_file($_FILES["gallery_image"]["tmp_name"], $target_file)) {
            // Insert image path into the database
            $insert_query = "INSERT INTO gallery (image_path) VALUES (?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param("s", $target_file);
            $stmt->execute();
            echo "<script>alert('Image uploaded successfully!');</script>";
        } else {
            echo "<script>alert('Error uploading image. Please try again.');</script>";
        }
    } else {
        echo "<script>alert('Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.');</script>";
    }
}

// Handle image deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $image_id = intval($_GET['delete']);

    // Fetch the image path
    $select_query = "SELECT image_path FROM gallery WHERE id = ?";
    $stmt = $conn->prepare($select_query);
    $stmt->bind_param("i", $image_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $image = $result->fetch_assoc();

    if ($image) {
        // Delete the image file
        if (file_exists($image['image_path'])) {
            unlink($image['image_path']);
        }

        // Delete the image record from the database
        $delete_query = "DELETE FROM gallery WHERE id = ?";
        $stmt = $conn->prepare($delete_query);
        $stmt->bind_param("i", $image_id);
        $stmt->execute();

        echo "<script>alert('Image deleted successfully!');</script>";
    }
}

// Fetch all gallery images
$gallery_query = "SELECT * FROM gallery ORDER BY uploaded_at DESC";
$gallery_result = $conn->query($gallery_query);
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <title>Manage Gallery</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>

        .manage-gallery {
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .manage-gallery h1 {
            font-size: 2.5rem;
            color: #007bff;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .manage-gallery h2{
            color: #007bff;
            margin-top: 1.5rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .header {
            background: linear-gradient(90deg, #0047ab, #89cff0);
            color: #fff;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .header .logo {
            font-size: 1.8rem;
            font-weight: bold;
            color: white;
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

            padding: 0.5rem 1rem;
            border-radius: 4px;
            font-size: 1.2rem;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .header ul li a:hover {
            background-color:#ffcc00;
            color: black;
        }

        .gallery-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 10px;
        }
        .gallery-grid img {
            width: 100%;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .upload-form {
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }
        .upload-form input[type="file"] {
            padding: 10px;
        }
        .upload-form button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .upload-form button:hover {
            background-color: #0056b3;
        }
        .delete-btn {
            display: inline-block;
            padding: 5px 10px;
            margin-top: 10px;
            background-color: #dc3545;
            color: white;
            text-align: center;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }
        .delete-btn:hover {
            background-color: #c82333;
        }

    </style>
</head>


<body>
<header class="header">
    <div class="logo">Admin Dashboard</div>
    <ul>
        <li><a href="admin_dashboard.php">Dashboard</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</header>

<section class="manage-gallery">
    <h1>Manage Gallery</h1>

    <!-- Upload Form -->
    <form method="POST" enctype="multipart/form-data">
        <label for="gallery_image">Upload Image</label>
        <input type="file" id="gallery_image" name="gallery_image" required>
        <button type="submit">Upload ⬆️</button>
    </form>


    <!-- Gallery Images -->
    <h2>Uploaded Images</h2>
    <div class="gallery-grid">
        <?php while ($row = $gallery_result->fetch_assoc()) { ?>
            <div class="gallery-item">
                <img src="<?php echo htmlspecialchars($row['image_path']); ?>" alt="Gallery Image">
                <a href="?delete=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure?')">Delete</a>
            </div>
        <?php } ?>
    </div>
</section>

<footer>
    <p>&copy; 2025 Your Smile Clinic. All Rights Reserved.</p>
</footer>
</body>
</html>
