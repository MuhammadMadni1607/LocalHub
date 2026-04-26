<?php
require_once __DIR__ . '/../includes/app.php';
require_once __DIR__ . '/../includes/layout.php';

require_login_with_paths('provider', '../login.php', '../index.php');

$providerId = (int) $_SESSION['user_id'];

$stmt = $conn->prepare('SELECT id, title, price, image, category FROM gigs WHERE user_id = ? ORDER BY id DESC');
$stmt->bind_param('i', $providerId);
$stmt->execute();
$gigs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

render_layout_start('Manage Gigs', '../');
?>

<main class="section reveal">
	<div class="container">
		<div class="section-head">
			<h1>Manage Gigs</h1>
			<a class="btn btn-primary" href="add-gig.php">Add Gig</a>
		</div>

		<?php if (!empty($gigs)): ?>
			<div class="gig-grid">
				<?php foreach ($gigs as $gig): ?>
					<article class="gig-card">
						<?php if (!empty($gig['image'])): ?>
							<img src="../uploads/<?php echo e($gig['image']); ?>" alt="<?php echo e($gig['title']); ?>">
						<?php else: ?>
							<div class="gig-image-placeholder">Service Image</div>
						<?php endif; ?>
						<div class="gig-body">
							<span class="badge"><?php echo e($gig['category'] ?: 'General'); ?></span>
							<h3><?php echo e($gig['title']); ?></h3>
							<div class="gig-meta">
								<span class="rating">ID #<?php echo (int) $gig['id']; ?></span>
								<strong><?php echo e(format_price((float) $gig['price'])); ?></strong>
							</div>
							<div class="inline-actions">
								<a class="btn btn-outline" href="../client/gig-details.php?id=<?php echo (int) $gig['id']; ?>">Preview</a>
								<a class="btn btn-danger" href="delete-gig.php?id=<?php echo (int) $gig['id']; ?>" onclick="return confirm('Delete this gig?')">Delete</a>
							</div>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		<?php else: ?>
			<div class="empty-state">No gigs added yet.</div>
		<?php endif; ?>
	</div>
</main>

<?php render_layout_end('../'); ?>