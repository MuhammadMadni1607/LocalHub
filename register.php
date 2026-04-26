<?php
require_once __DIR__ . '/includes/app.php';
require_once __DIR__ . '/includes/layout.php';

if (is_logged_in()) {
	redirect_to(current_user_role() === 'provider' ? 'provider/dashboard.php' : 'client/gigs.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$name = trim($_POST['name'] ?? '');
	$email = trim($_POST['email'] ?? '');
	$password = $_POST['password'] ?? '';
	$confirmPassword = $_POST['confirm_password'] ?? '';
	$role = ($_POST['role'] ?? 'client') === 'provider' ? 'provider' : 'client';

	if (mb_strlen($name) < 2 || mb_strlen($name) > 80) {
		$errors[] = 'Name should be between 2 and 80 characters.';
	}

	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$errors[] = 'Please enter a valid email address.';
	}

	if (strlen($password) < 6) {
		$errors[] = 'Password must be at least 6 characters.';
	}

	if ($password !== $confirmPassword) {
		$errors[] = 'Password confirmation does not match.';
	}

	if (empty($errors)) {
		$checkStmt = $conn->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
		$checkStmt->bind_param('s', $email);
		$checkStmt->execute();
		$emailExists = $checkStmt->get_result()->fetch_assoc();
		$checkStmt->close();

		if ($emailExists) {
			$errors[] = 'That email is already registered.';
		} else {
			$hash = password_hash($password, PASSWORD_DEFAULT);
			$insertStmt = $conn->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)');
			$insertStmt->bind_param('ssss', $name, $email, $hash, $role);
			$insertStmt->execute();
			$insertStmt->close();

			set_flash('success', 'Registration successful. Please login.');
			redirect_to('login.php');
		}
	}
}

render_layout_start('Register');
?>

<main class="auth-shell reveal">
	<div class="auth-card">
		<h1>Create your account</h1>
		<p>Join as a buyer or a provider and start using LocalHub.</p>

		<?php if (!empty($errors)): ?>
			<div class="alert alert-error"><?php echo e(implode(' ', $errors)); ?></div>
		<?php endif; ?>

		<form method="post" novalidate>
			<label>Full Name</label>
			<input type="text" name="name" required minlength="2" maxlength="80" value="<?php echo e($_POST['name'] ?? ''); ?>">

			<label>Email</label>
			<input type="email" name="email" required value="<?php echo e($_POST['email'] ?? ''); ?>">

			<label>Password</label>
			<input type="password" name="password" required minlength="6">

			<label>Confirm Password</label>
			<input type="password" name="confirm_password" required minlength="6">

			<label>Account Type</label>
			<select name="role">
				<option value="client" <?php echo (($_POST['role'] ?? '') !== 'provider') ? 'selected' : ''; ?>>Client</option>
				<option value="provider" <?php echo (($_POST['role'] ?? '') === 'provider') ? 'selected' : ''; ?>>Provider</option>
			</select>

			<button class="btn btn-primary btn-block" type="submit">Create Account</button>
		</form>

		<p class="auth-hint">Already have an account? <a href="login.php">Sign in</a>.</p>
	</div>
</main>

<?php render_layout_end(); ?>