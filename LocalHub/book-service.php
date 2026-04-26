<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != "client") {
    header("Location: login.php");
    exit();
}

$service_id = $_GET['id'];
$message = "";

// Fetch service details for the summary
$service_query = mysqli_query($conn, "SELECT * FROM services WHERE id='$service_id'");
$service = mysqli_fetch_assoc($service_query);

if (isset($_POST['book'])) {
    $client_id = $_SESSION['user_id'];
    $msg = mysqli_real_escape_string($conn, $_POST['message']);

    $query = "INSERT INTO bookings(service_id, client_id, message, status)
              VALUES('$service_id','$client_id','$msg','pending')";

    if (mysqli_query($conn, $query)) {
        $message = "Booking Sent Successfully!";
    } else {
        $message = "Failed to Book Service.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Service | LocalHub</title>
    <link rel="stylesheet" href="style.css">
</head>

<body class="bg-light">

<div class="container" style="max-width: 600px; padding-top: 60px;">
    
    <div class="card" style="padding: 0; overflow: hidden;">
        
        <div style="background: var(--brand-gradient); padding: 2.5rem; text-align: center; color: white;">
            <h2 style="color: white; margin-bottom: 10px;">Confirm Booking</h2>
            <p style="opacity: 0.9; font-size: 0.9rem;">You are booking a service with LocalHub</p>
        </div>

        <div style="padding: 2.5rem;">

            <?php if ($message != "") { ?>
                <div class="alert <?php echo strpos($message, 'Successfully') !== false ? 'alert-info' : 'alert-danger'; ?>" style="text-align: center; margin-bottom: 2rem;">
                    <?php echo $message; ?>
                </div>
            <?php } ?>

            <div style="display: flex; align-items: center; gap: 15px; background: #f8fafc; padding: 1rem; border-radius: 12px; margin-bottom: 2rem; border: 1px solid #f1f5f9;">
                <div style="font-size: 1.5rem;">📦</div>
                <div>
                    <p style="margin: 0; font-size: 0.8rem; color: var(--text-muted);">Selected Service</p>
                    <strong style="font-size: 1.1rem; color: var(--text-dark);"><?php echo $service['title']; ?></strong>
                </div>
            </div>

            <form method="POST">
                <label style="display: block; margin-bottom: 10px; font-weight: 700; color: var(--text-dark);">
                    Add a message for the provider
                </label>
                <textarea name="message" class="form-control" style="min-height: 120px; padding: 1rem; margin-bottom: 1.5rem;" 
                          placeholder="Tell the provider about your requirements..." required></textarea>

                <button class="btn btn-primary w-100" name="book" style="padding: 1rem; font-size: 1.1rem;">
                    Confirm & Send Request
                </button>
            </form>

            <div style="margin-top: 1.5rem; text-align: center;">
                <a href="service-details.php?id=<?php echo $service_id; ?>" style="color: var(--text-muted); text-decoration: none; font-size: 0.9rem; font-weight: 600;">
                    ← Back to Service Details
                </a>
            </div>

        </div>
    </div>

    <p style="text-align: center; margin-top: 2rem; font-size: 0.8rem; color: var(--text-muted);">
        Your request will be sent to the provider for approval.
    </p>

</div>

</body>
</html>