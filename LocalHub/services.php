<?php
session_start();
include 'includes/db.php';

// Dynamic Back Link Logic
$back_link = "index.php"; 
if (isset($_SESSION['user_role'])) {
    if ($_SESSION['user_role'] == 'provider') {
        $back_link = "provider/dashboard.php";
    } elseif ($_SESSION['user_role'] == 'client') {
        $back_link = "client/dashboard.php";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marketplace | LocalHub</title>
    <link rel="stylesheet" href="style.css">
</head>

<body class="bg-light">

<div style="width: 100%; height: 300px; position: relative; background: #1e293b; overflow: hidden; margin-bottom: -50px;">
    <img src="https://images.pexels.com/photos/3184418/pexels-photo-3184418.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1" 
         style="width: 100%; height: 100%; object-fit: cover; opacity: 0.5;" alt="Banner">
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; width: 100%; color: white; padding: 0 20px;">
        <h1 style="color: white; font-size: 3rem; margin-bottom: 10px; text-shadow: 0 2px 10px rgba(0,0,0,0.3);">Explore Services</h1>
        <p style="font-size: 1.2rem; opacity: 0.9;">Find the perfect professional for your next big idea.</p>
    </div>
</div>

<div class="container" style="position: relative; z-index: 2;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3rem; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); padding: 1rem 1.5rem; border-radius: 20px; box-shadow: var(--card-shadow); border: 1px solid rgba(255,255,255,0.3);">
        
        <a href="<?php echo $back_link; ?>" class="btn" style="background: #f1f5f9; color: var(--text-dark); padding: 0.6rem 1.2rem; display: flex; align-items: center; gap: 8px;">
            <span>←</span> Back to Dashboard
        </a>

        <div style="font-weight: 800; font-size: 1.4rem; background: var(--brand-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
            LocalHub
        </div>

        <div>
            <?php if(isset($_SESSION['user_name'])): ?>
                <span style="color: var(--text-muted); font-size: 0.9rem; font-weight: 600;">
                    User: <?php echo $_SESSION['user_name']; ?>
                </span>
            <?php endif; ?>
        </div>
    </div>
<div class="row">

    <?php
    $query = "SELECT services.*, users.full_name 
              FROM services 
              JOIN users ON services.provider_id = users.id";

    $result = mysqli_query($conn, $query);

    while ($row = mysqli_fetch_assoc($result)) {
    ?>

    <div class="card" style="padding: 0; overflow: hidden; display: flex; flex-direction: column; border-radius: 16px;">
    
    <div style="position: relative; height: 180px; overflow: hidden; background: #f1f5f9;">
        <?php 
            // DIRECT IMAGE LOGIC (No database change needed)
            $service_title = strtolower($row['title']);
            $manual_image = "https://via.placeholder.com/400x250?text=Service"; // Default

            if (strpos($service_title, 'video') !== false) {
                $manual_image = "uploads/video_editing.jpg"; 
            } elseif (strpos($service_title, 'graphic') !== false) {
                $manual_image = "uploads/graphic_design.png";
            }
        ?>

        <img src="<?php echo $manual_image; ?>" 
             onerror="this.src='https://via.placeholder.com/400x250?text=Check+Uploads+Folder'" 
             style="width: 100%; height: 100%; object-fit: cover;">
        
        <div style="position: absolute; top: 12px; right: 12px; background: rgba(255, 255, 255, 0.9); padding: 4px 12px; border-radius: 50px; font-weight: 800; color: #6366f1; font-size: 0.9rem;">
            $<?php echo number_format($row['price'], 2); ?>
        </div>
    </div>
        
        <div style="padding: 1.2rem; flex-grow: 1;">
            <h4 style="margin: 0; font-weight: 700; font-size: 1.1rem; color: var(--text-dark);">
                <?php echo $row['title']; ?>
            </h4>
            <p style="color: var(--text-muted); font-size: 0.85rem; margin: 8px 0; line-height: 1.4; height: 2.8em; overflow: hidden;">
                <?php echo substr($row['description'], 0, 65); ?>...
            </p>
            
            <div style="margin-top: 15px; padding-top: 12px; border-top: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <div style="width: 24px; height: 24px; background: var(--brand-gradient); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 0.6rem; font-weight: bold;">
                        <?php echo strtoupper(substr($row['full_name'], 0, 1)); ?>
                    </div>
                    <span style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600;">
                        <?php echo $row['full_name']; ?>
                    </span>
                </div>
                <a href="service-details.php?id=<?php echo $row['id']; ?>" class="btn btn-primary" style="padding: 0.4rem 1rem; font-size: 0.8rem; border-radius: 8px;">
                    Details
                </a>
            </div>
        </div>
    </div>

    <?php } ?>

</div>


</body>
</html>