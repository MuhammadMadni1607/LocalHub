<?php
require_once __DIR__ . '/../includes/app.php';

require_login_with_paths('provider', '../login.php', '../index.php');

$orderId = (int) ($_GET['id'] ?? 0);
$status = $_GET['s'] ?? '';
$providerId = (int) $_SESSION['user_id'];
$allowedStatuses = ['accepted', 'completed'];

if ($orderId <= 0 || !in_array($status, $allowedStatuses, true)) {
	set_flash('error', 'Invalid update request.');
	redirect_to('bookings.php');
}

$checkStmt = $conn->prepare(
	'SELECT o.id
	 FROM orders o
	 JOIN gigs g ON g.id = o.gig_id
	 WHERE o.id = ? AND g.user_id = ?
	 LIMIT 1'
);
$checkStmt->bind_param('ii', $orderId, $providerId);
$checkStmt->execute();
$order = $checkStmt->get_result()->fetch_assoc();
$checkStmt->close();

if (!$order) {
	set_flash('error', 'Order not found.');
	redirect_to('bookings.php');
}

$updateStmt = $conn->prepare('UPDATE orders SET status = ? WHERE id = ?');
$updateStmt->bind_param('si', $status, $orderId);
$updateStmt->execute();
$updateStmt->close();

set_flash('success', 'Order status updated to ' . ucfirst($status) . '.');
redirect_to('bookings.php');
?>