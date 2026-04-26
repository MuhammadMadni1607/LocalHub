<?php

require_once __DIR__ . '/../includes/app.php';

require_login_with_paths('client', '../login.php', '../index.php');

$gigId = (int) ($_GET['id'] ?? 0);
$userId = (int) $_SESSION['user_id'];

if ($gigId <= 0) {
    set_flash('error', 'Invalid wishlist request.');
    redirect_to('gigs.php');
}

$checkStmt = $conn->prepare('SELECT id FROM wishlists WHERE user_id = ? AND gig_id = ? LIMIT 1');
$checkStmt->bind_param('ii', $userId, $gigId);
$checkStmt->execute();
$existing = $checkStmt->get_result()->fetch_assoc();
$checkStmt->close();

if ($existing) {
    $deleteStmt = $conn->prepare('DELETE FROM wishlists WHERE id = ?');
    $deleteStmt->bind_param('i', $existing['id']);
    $deleteStmt->execute();
    $deleteStmt->close();
    set_flash('success', 'Removed from wishlist.');
} else {
    $insertStmt = $conn->prepare('INSERT INTO wishlists (user_id, gig_id, created_at) VALUES (?, ?, NOW())');
    $insertStmt->bind_param('ii', $userId, $gigId);
    $insertStmt->execute();
    $insertStmt->close();
    set_flash('success', 'Saved to wishlist.');
}

redirect_to('gig-details.php?id=' . $gigId);
