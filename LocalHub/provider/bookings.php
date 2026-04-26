<?php
session_start();
include '../includes/db.php';

// Security Check: Only allow logged-in Providers
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != "provider") {
    header("Location: ../login.php");
    exit();
}

$provider_id = $_SESSION['user_id'];

// Query: Get bookings for the current provider's services
$query = "SELECT bookings.*, services.title, services.image, users.full_name AS client_name 
          FROM bookings 
          JOIN services ON bookings.service_id = services.id 
          JOIN users ON bookings.client_id = users.id 
          WHERE services.provider_id = '$provider_id' 
          ORDER BY bookings.id DESC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Incoming Bookings | LocalHub</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body class="bg-light">

<div class="container" style="max-width: 900px; padding-top: 50px;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2.5rem;">
        <a href="dashboard.php" class="btn" style="background: #ffffff; color: var(--text-dark); border: 1px solid #e2e8f0; padding: 0.6rem 1.2rem; border-radius: 12px;">
            ← Back to Dashboard
        </a>
        <h2 style="margin: 0; font-weight: 800;">Manage Orders</h2>
    </div>

    <div style="display: flex; flex-direction: column; gap: 20px;">

        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): 
                
                // Logic for Status Badges
                $status_bg = "#f1f5f9"; $status_text = "#64748b";
                if($row['status'] == 'pending') { $status_bg = "#fff7ed"; $status_text = "#ea580c"; }
                if($row['status'] == 'confirmed') { $status_bg = "#f0fdf4"; $status_text = "#16a34a"; }

                // Manual Image Logic for Video/Graphic
                $service_title = strtolower($row['title']);
                $manual_image = "https://via.placeholder.com/100?text=Service"; 
                if (strpos($service_title, 'video') !== false) { $manual_image = "../uploads/video_editing.jpg"; }
                elseif (strpos($service_title, 'graphic') !== false || strpos($service_title, 'designing') !== false) { $manual_image = "../uploads/graphic_design.png"; }
            ?>

            <div class="card" style="padding: 1.5rem; display: flex; align-items: flex-start; gap: 25px; border-radius: 20px; border: 1px solid #e2e8f0;">
                
                <div style="flex: 0 0 100px; height: 100px; border-radius: 16px; overflow: hidden; background: #f1f5f9;">
                    <img src="<?php echo $manual_image; ?>" style="width: 100%; height: 100%; object-fit: cover;" alt="Service">
                </div>

                <div style="flex: 1;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                        <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase;">New Order #LH-<?php echo $row['id']; ?></span>
                        <span style="background: <?php echo $status_bg; ?>; color: <?php echo $status_text; ?>; padding: 4px 12px; border-radius: 50px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase;">
                            <?php echo $row['status']; ?>
                        </span>
                    </div>

                    <h4 style="margin: 0 0 10px 0; font-size: 1.25rem; font-weight: 800;"><?php echo $row['title']; ?></h4>
                    
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                        <div style="width: 30px; height: 30px; background: #6366f1; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 800;">
                            <?php echo strtoupper(substr($row['client_name'], 0, 1)); ?>
                        </div>
                        <span style="font-size: 0.9rem; font-weight: 600;">Client: <span style="color: #6366f1;"><?php echo $row['client_name']; ?></span></span>
                    </div>

                    <div style="background: #f8fafc; padding: 15px; border-radius: 12px; border: 1px solid #f1f5f9; margin-bottom: 20px;">
                        <p style="margin: 0; font-size: 0.9rem; color: #475569; font-style: italic;">"<?php echo $row['message']; ?>"</p>
                    </div>

                    <div style="display: flex; gap: 10px;">
                        <a href="update-status.php?id=<?php echo $row['id']; ?>&status=confirmed" class="btn" style="background: #10b981; color: white; padding: 0.6rem 1.2rem; font-size: 0.85rem; flex: 1; text-align: center;">Confirm Order</a>
                        <a href="update-status.php?id=<?php echo $row['id']; ?>&status=cancelled" class="btn" style="background: #fee2e2; color: #ef4444; padding: 0.6rem 1.2rem; font-size: 0.85rem; flex: 1; text-align: center;">Decline</a>
                    </div>
                </div>
            </div>

            <?php endwhile; ?>
        <?php else: ?>
            <div style="text-align: center; padding: 80px 20px; background: white; border-radius: 24px;">
                <div style="font-size: 4rem; margin-bottom: 20px;">📥</div>
                <h3 style="font-weight: 800;">No Bookings Yet</h3>
                <p style="color: var(--text-muted);">When clients book your services, they will appear here.</p>
            </div>
        <?php endif; ?>

    </div>
</div>

</body>
</html>