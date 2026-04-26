<?php
require_once __DIR__ . '/../includes/app.php';

require_login_with_paths('client', '../login.php', '../index.php');

$gigId = (int) ($_GET['id'] ?? 0);
$userId = (int) $_SESSION['user_id'];

if ($gigId <= 0) {
	set_flash('error', 'Invalid order request.');
	redirect_to('gigs.php');
}

$checkStmt = $conn->prepare('SELECT id FROM gigs WHERE id = ? LIMIT 1');
$checkStmt->bind_param('i', $gigId);
$checkStmt->execute();
$gigExists = $checkStmt->get_result()->fetch_assoc();
$checkStmt->close();

if (!$gigExists) {
	set_flash('error', 'Gig not found.');
	redirect_to('gigs.php');
}

$insertStmt = $conn->prepare('INSERT INTO orders (user_id, gig_id, status, created_at) VALUES (?, ?, "pending", NOW())');
$insertStmt->bind_param('ii', $userId, $gigId);
$insertStmt->execute();
$insertStmt->close();

set_flash('success', 'Order placed successfully.');
redirect_to('gig-details.php?id=' . $gigId);
?>