<?php

function render_layout_start(string $title, string $basePath = ''): void
{
    $flash = get_flash();
    $isLoggedIn = is_logged_in();
    $role = current_user_role();
    $homeLink = $basePath . 'index.php';
    $gigsLink = $basePath . 'client/gigs.php';
    $loginLink = $basePath . 'login.php';
    $registerLink = $basePath . 'register.php';
    $logoutLink = $basePath . 'logout.php';
    $providerLink = $basePath . 'provider/dashboard.php';

    echo '<!doctype html>';
    echo '<html lang="en">';
    echo '<head>';
    echo '<meta charset="utf-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<title>' . e($title) . ' | LocalHub</title>';
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">';
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>';
    echo '<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">';
    echo '<link rel="stylesheet" href="' . e(resolve_asset($basePath, 'style.css')) . '">';
    echo '</head>';
    echo '<body>';
    echo '<div class="page-fade">';
    echo '<header class="site-header" id="siteHeader">';
    echo '<div class="container nav-wrap">';
    echo '<a href="' . e($homeLink) . '" class="brand">LocalHub</a>';
    echo '<nav class="site-nav">';
    echo '<a href="' . e($homeLink) . '">Home</a>';
    echo '<a href="' . e($gigsLink) . '">Explore</a>';

    if ($isLoggedIn && $role === 'provider') {
        echo '<a href="' . e($providerLink) . '">Dashboard</a>';
    }

    if ($isLoggedIn) {
        echo '<a class="btn btn-outline" href="' . e($logoutLink) . '">Logout</a>';
    } else {
        echo '<a href="' . e($loginLink) . '">Login</a>';
        echo '<a class="btn btn-primary" href="' . e($registerLink) . '">Get Started</a>';
    }

    echo '</nav>';
    echo '</div>';
    echo '</header>';

    if ($flash) {
        echo '<div class="toast toast-' . e($flash['type']) . '" data-toast="true">' . e($flash['message']) . '</div>';
    }
}

function render_layout_end(string $basePath = ''): void
{
    $homeLink = $basePath . 'index.php';
    $gigsLink = $basePath . 'client/gigs.php';
    $registerLink = $basePath . 'register.php';

    echo '<footer class="site-footer">';
    echo '<div class="container footer-grid">';
    echo '<div><h4>LocalHub</h4><p>Book trusted local experts with startup-grade speed.</p></div>';
    echo '<div><h5>Product</h5><a href="' . e($gigsLink) . '">Services</a><a href="' . e($registerLink) . '">Become a Seller</a></div>';
    echo '<div><h5>Company</h5><a href="' . e($homeLink) . '">About</a><a href="' . e($homeLink) . '#categories">Categories</a></div>';
    echo '</div>';
    echo '<p class="copyright">&copy; ' . date('Y') . ' LocalHub. All rights reserved.</p>';
    echo '</footer>';
    echo '</div>';
    echo '<script src="' . e(resolve_asset($basePath, 'ui.js')) . '"></script>';
    echo '</body>';
    echo '</html>';
}
