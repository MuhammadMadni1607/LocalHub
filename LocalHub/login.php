<?php
session_start();
include 'includes/db.php';

$message = "";

if (isset($_POST['login'])) {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_role'] = $user['role'];

        if ($user['role'] == "provider") {
            header("Location: provider/dashboard.php");
        } else {
            header("Location: client/dashboard.php");
        }
        exit();

    } else {
        $message = "Invalid Email or Password";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>


<body class="bg-light">

<div class="container mt-5">

    <div class="col-md-5 mx-auto card p-4 shadow">

        <h3 class="text-center mb-3">Login</h3>

        <?php if ($message != "") { ?>
            <div class="alert alert-danger">
                <?php echo $message; ?>
            </div>
        <?php } ?>

        <form method="POST">

            <input type="email" name="email" class="form-control mb-2" placeholder="Email" required>

            <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>

            <button class="btn btn-primary w-100" name="login">Login</button>

        </form>

        <div class="text-center mt-3">
            <a href="register.php">Create Account</a>
        </div>

    </div>

</div>

</body>
</html>