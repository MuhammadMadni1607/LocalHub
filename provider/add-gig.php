<?php
require_once __DIR__ . '/../includes/app.php';
require_once __DIR__ . '/../includes/layout.php';

require_login_with_paths('provider', '../login.php', '../index.php');

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$title = trim($_POST['title'] ?? '');
	$description = trim($_POST['description'] ?? '');
	$category = trim($_POST['category'] ?? '');
	$price = (float) ($_POST['price'] ?? 0);
	$userId = (int) $_SESSION['user_id'];

	if (mb_strlen($title) < 5 || mb_strlen($title) > 120) {
		$errors[] = 'Title should be between 5 and 120 characters.';
	}

	if (mb_strlen($description) < 20) {
		$errors[] = 'Description should be at least 20 characters.';
	}

	if (!in_array($category, LOCALHUB_CATEGORIES, true)) {
		$errors[] = 'Please select a valid category.';
	}

	if ($price <= 0) {
		$errors[] = 'Price must be greater than 0.';
	}

	if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
		$errors[] = 'Please upload a valid gig image.';
	}

	$imageName = '';
	if (empty($errors)) {
		$tmpName = $_FILES['image']['tmp_name'];
		$originalName = $_FILES['image']['name'];
		$ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
		$allowed = ['jpg', 'jpeg', 'png', 'webp'];

		if (!in_array($ext, $allowed, true)) {
			$errors[] = 'Image format should be jpg, jpeg, png, or webp.';
		} else {
			$imageName = uniqid('gig_', true) . '.' . $ext;
			$destination = __DIR__ . '/../uploads/' . $imageName;

			if (!move_uploaded_file($tmpName, $destination)) {
				$errors[] = 'Failed to upload image.';
			}
		}
	}

	if (empty($errors)) {
		$stmt = $conn->prepare('INSERT INTO gigs (user_id, title, description, price, image, category) VALUES (?, ?, ?, ?, ?, ?)');
		$stmt->bind_param('issdss', $userId, $title, $description, $price, $imageName, $category);
		$stmt->execute();
		$stmt->close();

		set_flash('success', 'Gig published successfully.');
		redirect_to('manage-gigs.php');
	}
}

render_layout_start('Add Gig', '../');
?>

<main class="section reveal">
	<div class="container form-shell">
		<h1>Add New Gig</h1>

		<?php if (!empty($errors)): ?>
			<div class="alert alert-error"><?php echo e(implode(' ', $errors)); ?></div>
		<?php endif; ?>

		<form class="form-grid" method="post" enctype="multipart/form-data">
			<label>Gig Title</label>
			<input type="text" name="title" required minlength="5" maxlength="120" value="<?php echo e($_POST['title'] ?? ''); ?>">

			<label>Category</label>
			<select name="category" required>
				<option value="">Select category</option>
				<?php foreach (LOCALHUB_CATEGORIES as $item): ?>
					<option value="<?php echo e($item); ?>" <?php echo (($_POST['category'] ?? '') === $item) ? 'selected' : ''; ?>><?php echo e($item); ?></option>
				<?php endforeach; ?>
			</select>

			<label>Description</label>
			<textarea name="description" required minlength="20"><?php echo e($_POST['description'] ?? ''); ?></textarea>

			<label>Starting Price (Rs)</label>
			<input type="number" name="price" min="1" step="1" required value="<?php echo e($_POST['price'] ?? ''); ?>">

			<label>Cover Image</label>
			<input type="file" name="image" accept="image/*" required>

			<button class="btn btn-primary" type="submit">Publish Gig</button>
		</form>
	</div>
</main>

<?php render_layout_end('../'); ?>