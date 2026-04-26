<?php
session_start();
include 'includes/db.php';

$id = $_GET['id'];

$query = "SELECT services.*, users.full_name 
          FROM services 
          JOIN users ON services.provider_id = users.id
          WHERE services.id='$id'";

$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $row['title']; ?> | LocalHub</title>
    <link rel="stylesheet" href="style.css">
</head>

<body class="bg-light">

<div class="container" style="max-width: 900px; padding-top: 50px;">
    
    <div style="margin-bottom: 30px;">
        <a href="services.php" class="btn" style="background: #f1f5f9; color: var(--text-dark); padding: 0.6rem 1.2rem;">
            ← Back to Marketplace
        </a>
    </div>

    <div class="card" style="padding: 3rem; display: flex; flex-direction: row; gap: 40px; align-items: flex-start; flex-wrap: wrap;">
        
        <div style="flex: 0 0 200px;">
            <?php 
                $service_title = strtolower($row['title']);
                $manual_image = "https://via.placeholder.com/200x200?text=Service"; 

                if (strpos($service_title, 'video') !== false) {
                    $manual_image = "uploads/video_editing.jpg"; 
                } elseif (strpos($service_title, 'graphic') !== false) {
                    $manual_image = "uploads/graphic_design.png";
                }
            ?>

            <div style="position: relative; width: 200px; height: 200px; border-radius: 20px; overflow: hidden; box-shadow: var(--shadow); border: 4px solid white; background: #f1f5f9;">
                <img src="<?php echo $manual_image; ?>" 
                     onerror="this.src='https://via.placeholder.com/200?text=No+Image'" 
                     style="width: 100%; height: 100%; object-fit: cover;" 
                     alt="<?php echo $row['title']; ?>">
                
                <div style="position: absolute; bottom: 10px; right: 10px; background: #6366f1; color: white; padding: 4px 10px; border-radius: 50px; font-weight: 800; font-size: 0.75rem; box-shadow: 0 4px 10px rgba(0,0,0,0.2);">
                    $<?php echo number_format($row['price'], 0); ?>
                </div>
            </div>
        </div>

        <div style="flex: 1; min-width: 300px;">
            
            <div style="margin-bottom: 15px;">
                <span style="background: #6366f115; color: #6366f1; padding: 5px 12px; border-radius: 50px; font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">
                    Verified Expert
                </span>
            </div>

            <h1 style="font-size: 2.2rem; margin: 0 0 15px 0;"><?php echo $row['title']; ?></h1>
            
            <p style="color: var(--text-muted); font-size: 1.1rem; line-height: 1.7; margin-bottom: 25px;">
                <?php echo $row['description']; ?>
            </p>

            <div style="background: #f8fafc; border-radius: 16px; padding: 1.5rem; margin-bottom: 30px; border: 1px solid #f1f5f9; display: flex; flex-direction: column; gap: 10px;">
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-muted);">Total Cost</span>
                    <strong style="font-size: 1.2rem; color: #6366f1;">$<?php echo number_format($row['price'], 2); ?></strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-muted);">Service Provider</span>
                    <strong><?php echo $row['full_name']; ?></strong>
                </div>
            </div>

            <div style="display: flex; gap: 15px;">
                <a href="book-service.php?id=<?php echo $row['id']; ?>" class="btn btn-primary" style="flex: 1; padding: 1rem; text-align: center;">
                    Book Service Now
                </a>
            </div>

            <p style="text-align: center; margin-top: 20px; font-size: 0.85rem; color: var(--text-muted);">
                Secure transaction powered by LocalHub.
            </p>
        </div>
    </div>
</div>

</body>
</html>