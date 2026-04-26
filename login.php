<?php
require_once __DIR__ . '/includes/app.php';
require_once __DIR__ . '/includes/layout.php';

if (is_logged_in()) {
	redirect_to(current_user_role() === 'provider' ? 'provider/dashboard.php' : 'client/gigs.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$email = trim($_POST['email'] ?? '');
	$password = $_POST['password'] ?? '';

	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$errors[] = 'Please enter a valid email address.';
	}

	if ($password === '') {
		$errors[] = 'Password is required.';
	}

	if (empty($errors)) {
		$stmt = $conn->prepare('SELECT id, password, role FROM users WHERE email = ? LIMIT 1');
		$stmt->bind_param('s', $email);
		$stmt->execute();
		$result = $stmt->get_result();
		$user = $result->fetch_assoc();
		$stmt->close();

		if ($user && password_verify($password, $user['password'])) {
			session_regenerate_id(true);
			$_SESSION['user_id'] = (int) $user['id'];
			$_SESSION['role'] = $user['role'];

			set_flash('success', 'Welcome back!');
			redirect_to($user['role'] === 'provider' ? 'provider/dashboard.php' : 'client/gigs.php');
		}

		$errors[] = 'Invalid email or password.';
	}
}

render_layout_start('Login');
?>

<main class="auth-shell reveal">
	<div class="auth-card">
		<h1>Sign in to LocalHub</h1>
		<p>Access your account and continue booking or selling services.</p>

		<?php if (!empty($errors)): ?>
			<div class="alert alert-error"><?php echo e(implode(' ', $errors)); ?></div>
		<?php endif; ?>

		<form method="post" novalidate>
			<label>Email</label>
			<input type="email" name="email" placeholder="you@example.com" required value="<?php echo e($_POST['email'] ?? ''); ?>">

			<label>Password</label>
			<input type="password" name="password" placeholder="Enter your password" required minlength="6">

			<button class="btn btn-primary btn-block" type="submit">Login</button>
		</form>

		<p class="auth-hint">New here? <a href="register.php">Create an account</a>.</p>
	</div>
</main>

<?php render_layout_end(); ?>