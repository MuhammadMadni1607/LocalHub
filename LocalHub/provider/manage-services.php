<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != "provider") {
    header("Location: ../login.php");
    exit();
}

$provider_id = $_SESSION['user_id'];

// Fetch only services belonging to this provider
$query = "SELECT * FROM services WHERE provider_id = '$provider_id' ORDER BY id DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage My Services | LocalHub</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body class="bg-light">

<div class="container" style="padding-top: 50px; max-width: 1000px;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2.5rem;">
        <div>
            <a href="dashboard.php" class="btn" style="background: #f1f5f9; color: var(--text-dark); padding: 0.6rem 1.2rem; margin-bottom: 10px; display: inline-block;">
                ← Back to Dashboard
            </a>
            <h2 style="margin: 0; font-size: 2.2rem; font-weight: 800;">Manage Listings</h2>
        </div>
        <a href="add-service.php" class="btn btn-primary" style="padding: 0.8rem 1.5rem; border-radius: 12px;">
            + Add New Service
        </a>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 25px;">

        <?php if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) { 
                
                // Manual Image Logic for Video/Graphic thumbnails
                $service_title = strtolower($row['title']);
                $manual_image = "https://via.placeholder.com/400x250?text=Service"; 

                if (strpos($service_title, 'video') !== false) {
                    $manual_image = "../uploads/video_editing.jpg"; 
                } elseif (strpos($service_title, 'graphic') !== false || strpos($service_title, 'designing') !== false) {
                    $manual_image = "../uploads/graphic_design.png";
                }
        ?>

        <div class="card" style="padding: 0; overflow: hidden; border-radius: 18px; border: 1px solid #e2e8f0; display: flex; flex-direction: column;">
            
            <div style="height: 160px; position: relative;">
                <img src="<?php echo $manual_image; ?>" 
                     onerror="this.src='https://via.placeholder.com/400x250?text=No+Image'" 
                     style="width: 100%; height: 100%; object-fit: cover;" alt="Service Image">
                <div style="position: absolute; top: 10px; left: 10px; background: rgba(0,0,0,0.6); color: white; padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 600;">
                    ID: #<?php echo $row['id']; ?>
                </div>
            </div>

            <div style="padding: 1.5rem; flex-grow: 1;">
                <h4 style="margin: 0 0 10px 0; font-weight: 700; font-size: 1.1rem;"><?php echo $row['title']; ?></h4>
                <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 15px; height: 2.5em; overflow: hidden;">
                    <?php echo substr($row['description'], 0, 70); ?>...
                </p>
                
                <div style="background: #f8fafc; padding: 10px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <span style="font-size: 0.8rem; color: var(--text-muted);">Current Price</span>
                    <strong style="color: #6366f1;">$<?php echo number_format($row['price'], 2); ?></strong>
                </div>

                <div style="display: flex; gap: 10px;">
                    <a href="edit-service.php?id=<?php echo $row['id']; ?>" class="btn" style="flex: 1; background: #6366f1; color: white; text-align: center; font-size: 0.85rem; padding: 0.6rem;">
                        Edit
                    </a>
                    <a href="delete-service.php?id=<?php echo $row['id']; ?>" class="btn" style="flex: 1; background: #fee2e2; color: #ef4444; text-align: center; font-size: 0.85rem; padding: 0.6rem; font-weight: 600;" onclick="return confirm('Are you sure you want to delete this listing?')">
                        Delete
                    </a>
                </div>
            </div>
        </div>

        <?php } // End While
        } else { ?>
            <div class="card text-center" style="grid-column: 1 / -1; padding: 5rem; border: 2px dashed #cbd5e1; background: transparent; box-shadow: none;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">📁</div>
                <h3>No Services Listed</h3>
                <p style="color: var(--text-muted);">You haven't added any services yet. Start earning by creating your first listing!</p>
                <a href="add-service.php" class="btn btn-primary" style="margin-top: 20px;">+ Add Your First Service</a>
            </div>
        <?php } ?>

    </div>
</div>

</body>
</html>