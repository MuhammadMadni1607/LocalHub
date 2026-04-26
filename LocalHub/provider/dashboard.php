<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != "provider") {
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Provider Dashboard | LocalHub</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body class="bg-light">

<div class="container" style="padding-top: 50px;">
    
    <div style="margin-bottom: 40px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 2.5rem; margin-bottom: 5px;">Welcome, <span style="color: #6366f1;"><?php echo $_SESSION['user_name']; ?></span></h1>
            <p style="color: var(--text-muted); font-size: 1.1rem;">Manage your services and track client requests.</p>
        </div>
        <a href="../logout.php" class="btn" style="color: var(--danger); font-weight: 700; background: #fee2e2; border-radius: 12px;">Logout</a>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px;">
        
        <a href="add-service.php" style="text-decoration: none; color: inherit;">
            <div class="card" style="padding: 0; overflow: hidden; height: 100%;">
                <div style="height: 160px; background: #6366f1; display: flex; align-items: center; justify-content: center; font-size: 4rem;">
                    ✨
                </div>
                <div style="padding: 1.5rem;">
                    <h3 style="margin-bottom: 10px;">Add New Service</h3>
                    <p style="color: var(--text-muted); font-size: 0.9rem;">Launch a new service and reach more clients in the marketplace.</p>
                    <span class="btn btn-primary" style="margin-top: 15px; width: 100%;">Create Service</span>
                </div>
            </div>
        </a>

        <a href="manage-services.php" style="text-decoration: none; color: inherit;">
            <div class="card" style="padding: 0; overflow: hidden; height: 100%;">
                <div style="height: 160px; background: #f8fafc; display: flex; align-items: center; justify-content: center;">
                    <img src="https://images.pexels.com/photos/3183150/pexels-photo-3183150.jpeg?auto=compress&cs=tinysrgb&w=600" 
                         style="width: 100%; height: 100%; object-fit: cover; opacity: 0.8;" alt="Manage">
                </div>
                <div style="padding: 1.5rem;">
                    <h3 style="margin-bottom: 10px;">My Services</h3>
                    <p style="color: var(--text-muted); font-size: 0.9rem;">Update your existing listings, change prices, or remove services.</p>
                    <span class="btn" style="margin-top: 15px; width: 100%; background: #f1f5f9; color: var(--text-dark);">Manage Listings</span>
                </div>
            </div>
        </a>

        <a href="bookings.php" style="text-decoration: none; color: inherit;">
            <div class="card" style="padding: 0; overflow: hidden; height: 100%;">
                <div style="height: 160px; background: #10b981; display: flex; align-items: center; justify-content: center; font-size: 4rem;">
                    📅
                </div>
                <div style="padding: 1.5rem;">
                    <h3 style="margin-bottom: 10px;">Client Bookings</h3>
                    <p style="color: var(--text-muted); font-size: 0.9rem;">View incoming requests and manage your active project schedule.</p>
                    <span class="btn btn-success" style="margin-top: 15px; width: 100%;">View Requests</span>
                </div>
            </div>
        </a>

    </div>

</div>

</body>
</html>