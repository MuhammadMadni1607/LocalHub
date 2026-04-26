<?php
session_start();
include '../includes/db.php';

// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != "provider") {
    header("Location: ../login.php");
    exit();
}

$message = "";
$service_id = $_GET['id'];
$provider_id = $_SESSION['user_id'];

// Fetch existing service details
$fetch_query = "SELECT * FROM services WHERE id = '$service_id' AND provider_id = '$provider_id'";
$result = mysqli_query($conn, $fetch_query);
$service = mysqli_fetch_assoc($result);

if (!$service) {
    die("Service not found or you don't have permission to edit it.");
}

// Handle Update Logic
if (isset($_POST['update'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = $_POST['price'];
    
    // Check if a new image was uploaded
    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        $tmp = $_FILES['image']['tmp_name'];
        move_uploaded_file($tmp, "../uploads/" . $image);
        $img_sql = ", image='$image'";
    } else {
        $img_sql = ""; // Keep old image
    }

    $update_query = "UPDATE services SET title='$title', description='$description', price='$price' $img_sql 
                     WHERE id='$service_id' AND provider_id='$provider_id'";

    if (mysqli_query($conn, $update_query)) {
        header("Location: manage-services.php?msg=updated");
        exit();
    } else {
        $message = "Error updating service.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Service | LocalHub</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body class="bg-light">

<div class="container" style="max-width: 850px; padding-top: 40px;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <a href="manage-services.php" class="btn" style="background: #f1f5f9; color: var(--text-dark); padding: 0.5rem 1rem; font-size: 0.9rem;">
            ← Cancel
        </a>
        <h3 style="margin: 0; font-weight: 800;">Edit Service Listing</h3>
    </div>

    <div class="card" style="padding: 0; overflow: hidden; border-radius: 20px;">
        
        <div style="background: #6366f1; padding: 1.2rem; color: white; text-align: center;">
            <p style="margin: 0; font-size: 0.8rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase;">Update ID: #<?php echo $service_id; ?></p>
        </div>

        <div style="padding: 2.5rem;">
            <?php if ($message != ""): ?>
                <div class="alert alert-danger" style="text-align: center; border-radius: 12px; margin-bottom: 1.5rem;">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 40px;">
                
                <div>
                    <div style="margin-bottom: 15px;">
                        <label style="font-weight: 700; font-size: 0.85rem; display: block; margin-bottom: 5px;">Service Title</label>
                        <input type="text" name="title" class="form-control" value="<?php echo $service['title']; ?>" style="padding: 0.7rem;" required>
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label style="font-weight: 700; font-size: 0.85rem; display: block; margin-bottom: 5px;">Description</label>
                        <textarea name="description" class="form-control" style="height: 120px; padding: 0.7rem;" required><?php echo $service['description']; ?></textarea>
                    </div>

                    <div>
                        <label style="font-weight: 700; font-size: 0.85rem; display: block; margin-bottom: 5px;">Price ($)</label>
                        <input type="number" name="price" class="form-control" value="<?php echo $service['price']; ?>" style="padding: 0.7rem;" required>
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <label style="font-weight: 700; font-size: 0.85rem; display: block; margin-bottom: 5px;">Current Thumbnail</label>
                        <div style="width: 100%; height: 150px; border-radius: 12px; overflow: hidden; margin-bottom: 10px; border: 1px solid #e2e8f0;">
                            <img src="../uploads/<?php echo $service['image']; ?>" onerror="this.src='https://via.placeholder.com/300x150?text=No+Image'" style="width:100%; height:100%; object-fit:cover;">
                        </div>
                        <label style="font-weight: 700; font-size: 0.85rem; display: block; margin-bottom: 5px;">Change Image (Optional)</label>
                        <input type="file" name="image" style="font-size: 0.8rem;">
                    </div>

                    <button class="btn btn-primary" name="update" style="margin-top: 20px; padding: 1rem; font-weight: 700; border-radius: 12px;">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>