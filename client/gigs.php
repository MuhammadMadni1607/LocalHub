<?php
require_once __DIR__ . '/../includes/app.php';
require_once __DIR__ . '/../includes/layout.php';

$q = trim($_GET['q'] ?? '');
$category = trim($_GET['category'] ?? '');
$minPrice = isset($_GET['min_price']) ? (float) $_GET['min_price'] : 0;
$maxPrice = isset($_GET['max_price']) ? (float) $_GET['max_price'] : 0;
$page = max(1, (int) ($_GET['page'] ?? 1));
$limit = 9;
$offset = ($page - 1) * $limit;

$where = [];
$params = [];
$types = '';

if ($q !== '') {
	$where[] = '(title LIKE ? OR description LIKE ? OR category LIKE ?)';
	$like = '%' . $q . '%';
	$params[] = $like;
	$params[] = $like;
	$params[] = $like;
	$types .= 'sss';
}

if ($category !== '') {
	$where[] = 'category = ?';
	$params[] = $category;
	$types .= 's';
}

if ($minPrice > 0) {
	$where[] = 'price >= ?';
	$params[] = $minPrice;
	$types .= 'd';
}

if ($maxPrice > 0) {
	$where[] = 'price <= ?';
	$params[] = $maxPrice;
	$types .= 'd';
}

$whereSql = empty($where) ? '' : ' WHERE ' . implode(' AND ', $where);

$countSql = 'SELECT COUNT(*) AS total FROM gigs' . $whereSql;
$countStmt = $conn->prepare($countSql);
if (!empty($params)) {
	$countStmt->bind_param($types, ...$params);
}
$countStmt->execute();
$total = (int) ($countStmt->get_result()->fetch_assoc()['total'] ?? 0);
$countStmt->close();
$totalPages = max(1, (int) ceil($total / $limit));

$sql = 'SELECT id, title, description, price, image, category FROM gigs' . $whereSql . ' ORDER BY id DESC LIMIT ? OFFSET ?';
$stmt = $conn->prepare($sql);
$listParams = $params;
$listTypes = $types . 'ii';
$listParams[] = $limit;
$listParams[] = $offset;
$stmt->bind_param($listTypes, ...$listParams);
$stmt->execute();
$gigs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

render_layout_start('Browse Gigs', '../');
?>

<main class="section reveal">
	<div class="container">
		<div class="section-head">
			<h1>Explore Services</h1>
			<span class="results-count"><?php echo $total; ?> result(s)</span>
		</div>

		<form class="filters" method="get">
			<input type="text" name="q" placeholder="Search services..." value="<?php echo e($q); ?>">

			<select name="category">
				<option value="">All categories</option>
				<?php foreach (LOCALHUB_CATEGORIES as $item): ?>
					<option value="<?php echo e($item); ?>" <?php echo $category === $item ? 'selected' : ''; ?>><?php echo e($item); ?></option>
				<?php endforeach; ?>
			</select>

			<input type="number" name="min_price" min="0" placeholder="Min price" value="<?php echo $minPrice > 0 ? e((string) $minPrice) : ''; ?>">
			<input type="number" name="max_price" min="0" placeholder="Max price" value="<?php echo $maxPrice > 0 ? e((string) $maxPrice) : ''; ?>">

			<button class="btn btn-primary" type="submit">Apply</button>
		</form>

		<?php if (!empty($gigs)): ?>
			<div class="gig-grid">
				<?php foreach ($gigs as $gig): ?>
					<article class="gig-card reveal">
						<?php if (!empty($gig['image'])): ?>
							<img src="../uploads/<?php echo e($gig['image']); ?>" alt="<?php echo e($gig['title']); ?>">
						<?php else: ?>
							<div class="gig-image-placeholder">Service Image</div>
						<?php endif; ?>
						<div class="gig-body">
							<span class="badge"><?php echo e($gig['category'] ?: 'General'); ?></span>
							<h3><?php echo e($gig['title']); ?></h3>
							<p><?php echo e(mb_strimwidth($gig['description'], 0, 100, '...')); ?></p>
							<div class="gig-meta">
								<span class="rating">★ <?php echo number_format(gig_rating_from_id((int) $gig['id']), 1); ?></span>
								<strong><?php echo e(format_price((float) $gig['price'])); ?></strong>
							</div>
							<a class="btn btn-outline" href="gig-details.php?id=<?php echo (int) $gig['id']; ?>">View Gig</a>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		<?php else: ?>
			<div class="empty-state">No gigs found for these filters.</div>
		<?php endif; ?>

		<?php if ($totalPages > 1): ?>
			<nav class="pagination">
				<?php for ($i = 1; $i <= $totalPages; $i++): ?>
					<?php
					$queryParams = $_GET;
					$queryParams['page'] = $i;
					?>
					<a class="<?php echo $i === $page ? 'active' : ''; ?>" href="?<?php echo http_build_query($queryParams); ?>"><?php echo $i; ?></a>
				<?php endfor; ?>
			</nav>
		<?php endif; ?>
	</div>
</main>

<?php render_layout_end('../'); ?>