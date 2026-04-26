<?php
include 'includes/db.php';

$message = "";

if (isset($_POST['register'])) {

    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

    if (mysqli_num_rows($check) > 0) {
        $message = "Email already exists";
    } else {

        $query = "INSERT INTO users(full_name, email, password, role)
                  VALUES('$full_name', '$email', '$password', '$role')";

        if (mysqli_query($conn, $query)) {
            $message = "Registration Successful";
        } else {
            $message = "Error occurred";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
   
    <title>Register</title>
   
    <link rel="stylesheet" href="style.css">
</head>

<body class="bg-light">

<div class="container mt-5">

    <div class="col-md-5 mx-auto card p-4 shadow">

        <h3 class="text-center mb-3">Register</h3>

        <?php if ($message != "") { ?>
            <div class="alert alert-info">
                <?php echo $message; ?>
            </div>
        <?php } ?>

        <form method="POST">

            <input type="text" name="full_name" class="form-control mb-2" placeholder="Full Name" required>

            <input type="email" name="email" class="form-control mb-2" placeholder="Email" required>

            <input type="password" name="password" class="form-control mb-2" placeholder="Password" required>

            <select name="role" class="form-control mb-3" required>
                <option value="client">Client</option>
                <option value="provider">Provider</option>
            </select>

            <button class="btn btn-primary w-100" name="register">Register</button>

        </form>

        <div class="text-center mt-3">
            <a href="login.php">Already have account? Login</a>
        </div>

    </div>

</div>

</body>
</html>