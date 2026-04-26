<?php

session_start();
require_once __DIR__ . '/db.php';

const LOCALHUB_CATEGORIES = [
    'Home Repair',
    'Graphic Design',
    'Digital Marketing',
    'Web Development',
    'Video Editing',
    'Photography',
    'Writing',
    'Cleaning'
];

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function redirect_to(string $path): void
{
    header('Location: ' . $path);
    exit();
}

function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

function get_flash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $flash;
}

function is_logged_in(): bool
{
    return isset($_SESSION['user_id']);
}

function current_user_role(): ?string
{
    return $_SESSION['role'] ?? null;
}

function require_login(?string $role = null): void
{
    if (!is_logged_in()) {
        set_flash('error', 'Please sign in to continue.');
        redirect_to('login.php');
    }

    if ($role !== null && current_user_role() !== $role) {
        set_flash('error', 'You are not authorized to view that page.');
        redirect_to('index.php');
    }
}

function require_login_with_paths(?string $role, string $loginPath, string $fallbackPath): void
{
    if (!is_logged_in()) {
        set_flash('error', 'Please sign in to continue.');
        redirect_to($loginPath);
    }

    if ($role !== null && current_user_role() !== $role) {
        set_flash('error', 'You are not authorized to view that page.');
        redirect_to($fallbackPath);
    }
}

function current_user(mysqli $conn): ?array
{
    if (!is_logged_in()) {
        return null;
    }

    $stmt = $conn->prepare('SELECT id, name, email, role FROM users WHERE id = ? LIMIT 1');
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc() ?: null;
    $stmt->close();

    return $user;
}

function format_price(float $price): string
{
    return 'Rs ' . number_format($price, 0);
}

function gig_rating_from_id(int $id): float
{
    return 3.8 + (($id % 12) / 10);
}

function resolve_asset(string $basePath, string $asset): string
{
    return $basePath . ltrim($asset, '/');
}
