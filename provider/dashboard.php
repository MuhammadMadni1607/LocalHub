<?php
require_once __DIR__ . '/../includes/app.php';
require_once __DIR__ . '/../includes/layout.php';

require_login_with_paths('provider', '../login.php', '../index.php');

$providerId = (int) $_SESSION['user_id'];

$gigStmt = $conn->prepare('SELECT COUNT(*) AS total FROM gigs WHERE user_id = ?');
$gigStmt->bind_param('i', $providerId);
$gigStmt->execute();
$gigCount = (int) ($gigStmt->get_result()->fetch_assoc()['total'] ?? 0);
$gigStmt->close();

$orderStmt = $conn->prepare(
	'SELECT COUNT(*) AS total
	 FROM orders o
	 JOIN gigs g ON g.id = o.gig_id
	 WHERE g.user_id = ?'
);
$orderStmt->bind_param('i', $providerId);
$orderStmt->execute();
$orderCount = (int) ($orderStmt->get_result()->fetch_assoc()['total'] ?? 0);
$orderStmt->close();

$pendingStmt = $conn->prepare(
	'SELECT COUNT(*) AS total
	 FROM orders o
	 JOIN gigs g ON g.id = o.gig_id
	 WHERE g.user_id = ? AND o.status = "pending"'
);
$pendingStmt->bind_param('i', $providerId);
$pendingStmt->execute();
$pendingCount = (int) ($pendingStmt->get_result()->fetch_assoc()['total'] ?? 0);
$pendingStmt->close();

render_layout_start('Provider Dashboard', '../');
?>

<main class="section reveal">
	<div class="container">
		<div class="section-head">
			<h1>Provider Dashboard</h1>
		</div>

		<div class="stats-grid">
			<article class="stat-card">
				<h4>Total Gigs</h4>
				<p><?php echo $gigCount; ?></p>
			</article>
			<article class="stat-card">
				<h4>Total Orders</h4>
				<p><?php echo $orderCount; ?></p>
			</article>
			<article class="stat-card">
				<h4>Pending Orders</h4>
				<p><?php echo $pendingCount; ?></p>
			</article>
		</div>

		<div class="action-row">
			<a class="btn btn-primary" href="add-gig.php">Add New Gig</a>
			<a class="btn btn-outline" href="manage-gigs.php">Manage Gigs</a>
			<a class="btn btn-outline" href="bookings.php">View Orders</a>
		</div>
	</div>
</main>

<?php render_layout_end('../'); ?>