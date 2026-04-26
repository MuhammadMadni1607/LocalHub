<?php
session_start();
include '../includes/db.php';

// Check if user is logged in as client
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != "client") {
    header("Location: ../login.php");
    exit();
}

$client_id = $_SESSION['user_id'];

// Modified query to fetch image
$query = "SELECT bookings.*, services.title, services.price, services.image 
          FROM bookings 
          JOIN services ON bookings.service_id = services.id
          WHERE bookings.client_id='$client_id'
          ORDER BY bookings.id DESC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Bookings | LocalHub</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body class="bg-light">

<div class="container" style="padding-top: 50px; max-width: 800px;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2.5rem;">
        <a href="dashboard.php" class="btn" style="background: #f1f5f9; color: var(--text-dark); padding: 0.6rem 1.2rem;">
            ← Back to Dashboard
        </a>
        <h2 style="margin: 0; font-size: 2rem;">Your Bookings</h2>
    </div>

    <div style="display: flex; flex-direction: column; gap: 20px;">

        <?php if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) { 
                
                // Logic for status badge colors
                $status_color = "#64748b"; // Default (Gray)
                if($row['status'] == 'pending') $status_color = "#f59e0b"; // Orange
                if($row['status'] == 'confirmed') $status_color = "#10b981"; // Green
                if($row['status'] == 'cancelled') $status_color = "#ef4444"; // Red
        ?>

        <div class="card" style="padding: 1.5rem; display: flex; flex-direction: row; align-items: center; gap: 20px; border-radius: 16px;">
            
            <?php 
                $service_title = strtolower($row['title']);
                $manual_image = "https://via.placeholder.com/100?text=Service"; // Fallback

                if (strpos($service_title, 'video') !== false) {
                    $manual_image = "../uploads/video_editing.jpg"; 
                } elseif (strpos($service_title, 'graphic') !== false) {
                    $manual_image = "../uploads/graphic_design.png";
                }
            ?>
            
            <img src="<?php echo $manual_image; ?>" 
                 onerror="this.src='https://via.placeholder.com/100?text=No+Img'"
                 style="width: 80px; height: 80px; border-radius: 12px; object-fit: cover; flex-shrink: 0;" alt="Service Image">

            <div style="flex-grow: 1; display: flex; flex-direction: column;">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 5px;">
                    <h4 style="margin: 0; font-size: 1.15rem; color: var(--text-dark);"><?php echo $row['title']; ?></h4>
                    <span style="background: <?php echo $status_color; ?>15; color: <?php echo $status_color; ?>; padding: 5px 10px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; margin-left: 10px;">
                        <?php echo $row['status']; ?>
                    </span>
                </div>
                <p style="color: var(--text-muted); font-size: 0.85rem; margin: 0 0 10px 0;">Booking ID: #LH-<?php echo $row['id']; ?></p>
                <div style="background: #f8fafc; padding: 10px; border-radius: 8px; border: 1px inset #f1f5f9;">
                    <p style="margin: 0; font-size: 0.85rem; color: var(--text-dark);">
                        <strong>Message:</strong> <?php echo $row['message']; ?>
                    </p>
                </div>
            </div>

            <div style="text-align: right; border-left: 1px solid #f1f5f9; padding-left: 20px; min-width: 90px; flex-shrink: 0;">
                <p style="margin: 0; color: var(--text-muted); font-size: 0.75rem;">Cost</p>
                <p style="margin: 0; font-size: 1.25rem; font-weight: 800; color: #6366f1;">$<?php echo number_format($row['price'], 2); ?></p>
            </div>
        </div>

        <?php } // End While
        } else { ?>
            <div class="card text-center" style="padding: 4rem; border: 2px dashed #e2e8f0; background: transparent; box-shadow: none;">
                <div style="font-size: 4rem; margin-bottom: 1rem;">📅</div>
                <h3>No Bookings Yet</h3>
                <p style="color: var(--text-muted); margin-bottom: 2rem;">You haven't booked any services yet. Start exploring the marketplace!</p>
                <a href="../services.php" class="btn btn-primary">Browse Services</a>
            </div>
        <?php } ?>

    </div>
</div>

</body>
</html>