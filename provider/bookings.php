<?php
require_once __DIR__ . '/../includes/app.php';
require_once __DIR__ . '/../includes/layout.php';

require_login_with_paths('provider', '../login.php', '../index.php');

$providerId = (int) $_SESSION['user_id'];
$stmt = $conn->prepare(
	'SELECT o.id, o.status, o.created_at, g.title, g.price, u.name AS buyer_name
	 FROM orders o
	 JOIN gigs g ON g.id = o.gig_id
	 JOIN users u ON u.id = o.user_id
	 WHERE g.user_id = ?
	 ORDER BY o.created_at DESC'
);
$stmt->bind_param('i', $providerId);
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

render_layout_start('Orders', '../');
?>

<main class="section reveal">
	<div class="container">
		<div class="section-head">
			<h1>Incoming Orders</h1>
		</div>

		<?php if (!empty($orders)): ?>
			<div class="table-wrap">
				<table class="data-table">
					<thead>
					<tr>
						<th>Gig</th>
						<th>Buyer</th>
						<th>Price</th>
						<th>Status</th>
						<th>Date</th>
						<th>Actions</th>
					</tr>
					</thead>
					<tbody>
					<?php foreach ($orders as $order): ?>
						<tr>
							<td><?php echo e($order['title']); ?></td>
							<td><?php echo e($order['buyer_name']); ?></td>
							<td><?php echo e(format_price((float) $order['price'])); ?></td>
							<td><span class="status status-<?php echo e($order['status']); ?>"><?php echo e(ucfirst($order['status'])); ?></span></td>
							<td><?php echo e(date('d M Y', strtotime($order['created_at']))); ?></td>
							<td>
								<div class="inline-actions">
									<a class="btn btn-outline" href="update-status.php?id=<?php echo (int) $order['id']; ?>&s=accepted">Accept</a>
									<a class="btn btn-outline" href="update-status.php?id=<?php echo (int) $order['id']; ?>&s=completed">Complete</a>
								</div>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		<?php else: ?>
			<div class="empty-state">No orders yet.</div>
		<?php endif; ?>
	</div>
</main>

<?php render_layout_end('../'); ?>