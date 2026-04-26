<?php
require_once __DIR__ . '/../includes/app.php';
require_once __DIR__ . '/../includes/layout.php';

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
	set_flash('error', 'Invalid gig requested.');
	redirect_to('gigs.php');
}

$stmt = $conn->prepare(
	'SELECT g.id, g.title, g.description, g.price, g.image, g.category, u.name AS seller_name
	 FROM gigs g
	 JOIN users u ON u.id = g.user_id
	 WHERE g.id = ?
	 LIMIT 1'
);
$stmt->bind_param('i', $id);
$stmt->execute();
$gig = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$gig) {
	set_flash('error', 'Gig not found.');
	redirect_to('gigs.php');
}

$isWishlisted = false;
if (is_logged_in()) {
	$wishStmt = $conn->prepare('SELECT id FROM wishlists WHERE user_id = ? AND gig_id = ? LIMIT 1');
	$wishStmt->bind_param('ii', $_SESSION['user_id'], $id);
	$wishStmt->execute();
	$isWishlisted = (bool) $wishStmt->get_result()->fetch_assoc();
	$wishStmt->close();
}

render_layout_start('Gig Details', '../');
?>

<main class="section reveal">
	<div class="container details-grid">
		<section>
			<div class="details-gallery">
				<?php if (!empty($gig['image'])): ?>
					<img id="mainGigImage" src="../uploads/<?php echo e($gig['image']); ?>" alt="<?php echo e($gig['title']); ?>">
				<?php else: ?>
					<div class="gig-image-placeholder detail">Service Image</div>
				<?php endif; ?>
				<div class="thumb-row">
					<button type="button" class="thumb active" data-image="../uploads/<?php echo e($gig['image']); ?>">1</button>
					<button type="button" class="thumb" data-image="../uploads/<?php echo e($gig['image']); ?>">2</button>
					<button type="button" class="thumb" data-image="../uploads/<?php echo e($gig['image']); ?>">3</button>
				</div>
			</div>

			<h1><?php echo e($gig['title']); ?></h1>
			<div class="gig-meta-row">
				<span class="badge"><?php echo e($gig['category'] ?: 'General'); ?></span>
				<span class="rating">★ <?php echo number_format(gig_rating_from_id((int) $gig['id']), 1); ?></span>
			</div>
			<p class="lead"><?php echo nl2br(e($gig['description'])); ?></p>

			<article class="seller-box">
				<h4>Seller</h4>
				<p><?php echo e($gig['seller_name']); ?></p>
				<small>Top-rated local provider</small>
			</article>
		</section>

		<aside class="price-box">
			<h3>Starting at</h3>
			<p class="amount"><?php echo e(format_price((float) $gig['price'])); ?></p>
			<p>Delivery and exact timeline can be discussed after placing the order.</p>

			<?php if (is_logged_in()): ?>
				<a class="btn btn-primary btn-block" href="book.php?id=<?php echo (int) $gig['id']; ?>">Order Now</a>
				<a class="btn btn-outline btn-block" href="toggle-wishlist.php?id=<?php echo (int) $gig['id']; ?>">
					<?php echo $isWishlisted ? 'Remove from Wishlist' : 'Save to Wishlist'; ?>
				</a>
			<?php else: ?>
				<a class="btn btn-primary btn-block" href="../login.php">Login to Order</a>
			<?php endif; ?>
		</aside>
	</div>
</main>

<?php render_layout_end('../'); ?>