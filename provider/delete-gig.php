<?php

require_once __DIR__ . '/../includes/app.php';

require_login_with_paths('provider', '../login.php', '../index.php');

$gigId = (int) ($_GET['id'] ?? 0);
$providerId = (int) $_SESSION['user_id'];

if ($gigId <= 0) {
    set_flash('error', 'Invalid gig request.');
    redirect_to('manage-gigs.php');
}

$selectStmt = $conn->prepare('SELECT image FROM gigs WHERE id = ? AND user_id = ? LIMIT 1');
$selectStmt->bind_param('ii', $gigId, $providerId);
$selectStmt->execute();
$gig = $selectStmt->get_result()->fetch_assoc();
$selectStmt->close();

if (!$gig) {
    set_flash('error', 'Gig not found.');
    redirect_to('manage-gigs.php');
}

$deleteStmt = $conn->prepare('DELETE FROM gigs WHERE id = ? AND user_id = ?');
$deleteStmt->bind_param('ii', $gigId, $providerId);
$deleteStmt->execute();
$deleteStmt->close();

if (!empty($gig['image'])) {
    $filePath = __DIR__ . '/../uploads/' . $gig['image'];
    if (is_file($filePath)) {
        unlink($filePath);
    }
}

set_flash('success', 'Gig deleted successfully.');
redirect_to('manage-gigs.php');
