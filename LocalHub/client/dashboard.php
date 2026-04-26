<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != "client") {
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    
    <title>Client Dashboard</title>

  <link rel="stylesheet" href="../style.css">
</head>
<body class="bg-light">

<div class="container" style="padding-top: 50px;">
    
    <div style="margin-bottom: 40px;">
        <h1 style="font-size: 2.5rem; margin-bottom: 10px;">Welcome back, <span style="color: #6366f1;"><?php echo $_SESSION['user_name']; ?></span></h1>
        <p style="color: var(--text-muted); font-size: 1.1rem;">What would you like to do today?</p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
        
        <a href="../services.php" style="text-decoration: none; color: inherit;">
    <div class="card" style="padding: 0; overflow: hidden; height: 100%; background: #ffffff;">
        <div style="width: 100%; height: 200px; background: #e2e8f0; overflow: hidden;">
            <img src="https://images.pexels.com/photos/3183150/pexels-photo-3183150.jpeg?auto=compress&cs=tinysrgb&w=600" 
                 style="width: 100%; height: 200px; object-fit: cover;" 
                 alt="Browse Services"
                 onerror="this.src='https://via.placeholder.com/600x400?text=Explore+Services'">
        </div>
        <div style="padding: 1.5rem;">
            <h3 style="margin-bottom: 10px;">Browse Services</h3>
            <p style="color: var(--text-muted); font-size: 0.95rem;">Explore our marketplace and find the perfect expert for your project.</p>
            <span class="btn btn-primary" style="margin-top: 15px; display: inline-block;">Explore Now</span>
        </div>
    </div>
</a>

        <a href="bookings.php" style="text-decoration: none; color: inherit;">
            <div class="card" style="padding: 0; overflow: hidden; height: 100%;">
                <img src="https://images.unsplash.com/photo-1506784983877-45594efa4cbe?auto=format&fit=crop&w=600&q=80" 
                     style="width: 100%; height: 200px; object-fit: cover;" alt="Bookings">
                <div style="padding: 1.5rem;">
                    <h3 style="margin-bottom: 10px;">My Bookings</h3>
                    <p style="color: var(--text-muted); font-size: 0.95rem;">Track your active requests and view your booking history.</p>
                    <span class="btn" style="margin-top: 15px; display: inline-block; background: #f1f5f9; color: var(--text-dark);">View History</span>
                </div>
            </div>
        </a>

        <div class="card" style="padding: 1.5rem; display: flex; flex-direction: column; justify-content: center; align-items: center; border: 2px dashed #e2e8f0; background: transparent; box-shadow: none;">
            <div style="font-size: 3rem; margin-bottom: 1rem;">👤</div>
            <h4 style="margin-bottom: 5px;">Profile Settings</h4>
            <p style="color: var(--text-muted); text-align: center; font-size: 0.85rem;">Update your information</p>
            <a href="../logout.php" style="margin-top: 20px; color: var(--danger); font-weight: 600; text-decoration: none;">Logout Account</a>
        </div>

    </div>
</div>

</body>

</body>
</html>