<?php
require_once __DIR__ . '/includes/app.php';
require_once __DIR__ . '/includes/layout.php';

$featuredResult = $conn->query('SELECT id, title, description, price, image, category FROM gigs ORDER BY id DESC LIMIT 8');
$featuredGigs = $featuredResult ? $featuredResult->fetch_all(MYSQLI_ASSOC) : [];

$trendingResult = $conn->query('SELECT id, title, price, image, category FROM gigs ORDER BY price DESC LIMIT 4');
$trendingGigs = $trendingResult ? $trendingResult->fetch_all(MYSQLI_ASSOC) : [];

render_layout_start('Home');
?>

<main>
	<section class="hero-section reveal">
		<div class="container hero-grid">
			<div>
				<span class="chip">Trusted Local Marketplace</span>
				<h1>Book top local talent in minutes, not days.</h1>
				<p>From design to home services, discover vetted experts with transparent pricing and smooth ordering.</p>
				<form class="hero-search" action="client/gigs.php" method="get">
					<input class="search-input" type="text" name="q" placeholder="Search services, skills, or categories" maxlength="120">
					<button class="btn btn-primary" type="submit">Search</button>
				</form>
			</div>
			<div class="hero-panel">
				<h3>Popular This Week</h3>
				<?php if (!empty($trendingGigs)): ?>
					<ul class="mini-list">
						<?php foreach ($trendingGigs as $gig): ?>
							<li>
								<a href="client/gig-details.php?id=<?php echo (int) $gig['id']; ?>">
									<span><?php echo e($gig['title']); ?></span>
									<strong><?php echo e(format_price((float) $gig['price'])); ?></strong>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php else: ?>
					<p>No services yet. Sellers can publish their first gig now.</p>
				<?php endif; ?>
			</div>
		</div>
	</section>

	<section class="section reveal" id="categories">
		<div class="container">
			<div class="section-head">
				<h2>Browse Categories</h2>
				<a class="text-link" href="client/gigs.php">View all services</a>
			</div>
			<div class="categories-grid">
				<?php foreach (LOCALHUB_CATEGORIES as $category): ?>
					<a class="category-card" href="client/gigs.php?category=<?php echo urlencode($category); ?>">
						<span class="category-icon">●</span>
						<h4><?php echo e($category); ?></h4>
						<p>Explore verified professionals</p>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="section section-soft reveal">
		<div class="container">
			<div class="section-head">
				<h2>Featured Gigs</h2>
			</div>
			<div class="gig-grid">
				<?php if (!empty($featuredGigs)): ?>
					<?php foreach ($featuredGigs as $gig): ?>
						<article class="gig-card">
							<?php if (!empty($gig['image'])): ?>
								<img src="uploads/<?php echo e($gig['image']); ?>" alt="<?php echo e($gig['title']); ?>">
							<?php else: ?>
								<div class="gig-image-placeholder">Service Image</div>
							<?php endif; ?>
							<div class="gig-body">
								<span class="badge"><?php echo e($gig['category'] ?: 'General'); ?></span>
								<h3><?php echo e($gig['title']); ?></h3>
								<p><?php echo e(mb_strimwidth($gig['description'], 0, 95, '...')); ?></p>
								<div class="gig-meta">
									<span class="rating">★ <?php echo number_format(gig_rating_from_id((int) $gig['id']), 1); ?></span>
									<strong><?php echo e(format_price((float) $gig['price'])); ?></strong>
								</div>
								<a class="btn btn-outline" href="client/gig-details.php?id=<?php echo (int) $gig['id']; ?>">View Details</a>
							</div>
						</article>
					<?php endforeach; ?>
				<?php else: ?>
					<div class="empty-state">No gigs yet. Create one from the provider dashboard.</div>
				<?php endif; ?>
			</div>
		</div>
	</section>

	<section class="section reveal">
		<div class="container split-banner">
			<div>
				<h2>Built for modern buyers and sellers</h2>
				<p>Launch your service business, collect orders, and grow your local reputation with a clean workflow.</p>
			</div>
			<a class="btn btn-primary" href="register.php">Start Selling</a>
		</div>
	</section>
</main>

<?php render_layout_end(); ?>