<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != "provider") {
    header("Location: ../login.php");
    exit();
}

$message = "";
$categories = mysqli_query($conn, "SELECT * FROM categories");

if (isset($_POST['add'])) {
    $provider_id = $_SESSION['user_id'];
    $category_id = $_POST['category_id'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = $_POST['price'];

    $image = $_FILES['image']['name'];
    $tmp = $_FILES['image']['tmp_name'];

    move_uploaded_file($tmp, "../uploads/" . $image);

    $query = "INSERT INTO services(provider_id, category_id, title, description, price, image)
              VALUES('$provider_id','$category_id','$title','$description','$price','$image')";

    if (mysqli_query($conn, $query)) {
        $message = "Service Added Successfully!";
    } else {
        $message = "Error: Could not add service.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Service | LocalHub</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body class="bg-light">

<div class="container" style="max-width: 850px; padding-top: 40px;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <a href="dashboard.php" class="btn" style="background: #f1f5f9; color: var(--text-dark); padding: 0.5rem 1rem; font-size: 0.9rem;">
            ← Back
        </a>
        <h3 style="margin: 0; font-weight: 800;">Create New Service</h3>
    </div>

    <div class="card" style="padding: 0; overflow: hidden; border-radius: 20px;">
        
        <div style="background: var(--brand-gradient); padding: 1.2rem; color: white; text-align: center;">
            <p style="margin: 0; font-size: 0.8rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase;">Service Details</p>
        </div>

        <div style="padding: 2.5rem;">
            <?php if ($message != ""): ?>
                <div class="alert alert-info" style="text-align: center; border-radius: 12px; margin-bottom: 1.5rem; padding: 0.8rem;">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 40px;">
                
                <div>
                    <div style="margin-bottom: 15px;">
                        <label style="font-weight: 700; font-size: 0.85rem; display: block; margin-bottom: 5px;">Service Title</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Professional Video Editing" style="padding: 0.7rem;" required>
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label style="font-weight: 700; font-size: 0.85rem; display: block; margin-bottom: 5px;">Description</label>
                        <textarea name="description" class="form-control" style="height: 100px; padding: 0.7rem; font-size: 0.9rem;" placeholder="Describe your service..." required></textarea>
                    </div>

                    <div>
                        <label style="font-weight: 700; font-size: 0.85rem; display: block; margin-bottom: 5px;">Price ($)</label>
                        <input type="number" name="price" class="form-control" placeholder="0.00" style="padding: 0.7rem;" required>
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; justify-content: space-between;">
                    <div style="margin-bottom: 15px;">
                        <label style="font-weight: 700; font-size: 0.85rem; display: block; margin-bottom: 5px;">Category</label>
                        <select name="category_id" class="form-control" style="padding: 0.7rem; height: auto;" required>
                            <option value="">Select Category</option>
                            <?php while ($row = mysqli_fetch_assoc($categories)): ?>
                                <option value="<?php echo $row['id']; ?>"><?php echo $row['category_name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div>
                        <label style="font-weight: 700; font-size: 0.85rem; display: block; margin-bottom: 5px;">Thumbnail</label>
                        <div style="border: 2px dashed #e2e8f0; border-radius: 12px; padding: 15px; text-align: center; background: #f8fafc;">
                            <span style="font-size: 1.5rem; display: block; margin-bottom: 5px;">📸</span>
                            <input type="file" name="image" style="font-size: 0.8rem; width: 100%;" required>
                        </div>
                    </div>

                    <button class="btn btn-primary" name="add" style="margin-top: 20px; padding: 0.9rem; font-weight: 700; border-radius: 12px; width: 100%;">
                        Publish Service
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>