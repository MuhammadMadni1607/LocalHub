<?php
session_start();
include '../includes/db.php';

// Security Check: Ensure only the Provider can update their own orders
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != "provider") {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['id']) && isset($_GET['status'])) {
    $booking_id = $_GET['id'];
    $new_status = $_GET['status'];
    $provider_id = $_SESSION['user_id'];

    // Verify this booking belongs to a service owned by this provider
    $check_query = "SELECT bookings.id FROM bookings 
                    JOIN services ON bookings.service_id = services.id 
                    WHERE bookings.id = '$booking_id' AND services.provider_id = '$provider_id'";
    
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        // Update the status in the database
        $update_query = "UPDATE bookings SET status = '$new_status' WHERE id = '$booking_id'";
        
        if (mysqli_query($conn, $update_query)) {
            // Success! Redirect back to the bookings list
            header("Location: bookings.php?msg=success");
            exit();
        } else {
            echo "Error updating record: " . mysqli_error($conn);
        }
    } else {
        echo "Unauthorized action.";
    }
} else {
    header("Location: bookings.php");
    exit();
}
?>