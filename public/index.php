<?php

declare(strict_types=1);

require __DIR__ . '/../app/includes/session.php';

if (isset($_GET['toggle_role']) && tc_logged_in()) {
    tc_set_role(tc_role() === 'hiker' ? 'organizer' : 'hiker');
    header('Location: index.php?page=dashboard');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'register') {
        tc_set_role($_POST['role'] ?? 'hiker');
        $name = trim((string) ($_POST['name'] ?? ''));
        $_SESSION['tc_name'] = $name !== '' ? $name : 'New member';
        header('Location: index.php?page=login&registered=1');
        exit;
    }
    if ($action === 'login') {
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        if ($email === '') {
            header('Location: index.php?page=login&error=email');
            exit;
        }
        if ($password === '') {
            header('Location: index.php?page=login&error=password');
            exit;
        }
        if (strtolower($email) === 'wrong@example.com') {
            header('Location: index.php?page=login&error=badpass');
            exit;
        }
        tc_set_logged_in(true);
        header('Location: index.php?page=dashboard');
        exit;
    }
    if ($action === 'logout') {
        tc_set_logged_in(false);
        header('Location: index.php?page=landing');
        exit;
    }
    if ($action === 'publish_event') {
        header('Location: index.php?page=my_event');
        exit;
    }
}

$page = $_GET['page'] ?? 'landing';

$views = [
    'landing' => 'landing.php',
    'login' => 'login.php',
    'register' => 'register.php',
    'forgot_password' => 'forgot_password.php',
    'dashboard' => 'dashboard.php',
    'find_hikes' => 'find_hikes.php',
    'event_details' => 'event_details.php',
    'create_event' => 'create_event.php',
    'my_event' => 'my_event.php',
    'updates' => 'updates.php',
    'profile' => 'profile.php',
    'settings' => 'settings.php',
    'reviews' => 'reviews.php',
];

$protected = ['dashboard', 'find_hikes', 'event_details', 'create_event', 'my_event', 'updates', 'profile', 'settings', 'reviews'];
if (in_array($page, $protected, true) && !tc_logged_in()) {
    header('Location: index.php?page=login&required=1');
    exit;
}

if ($page === 'create_event' && tc_role() !== 'organizer') {
    header('Location: index.php?page=dashboard');
    exit;
}

$file = $views[$page] ?? null;
if ($file && is_file(__DIR__ . '/../app/views/' . $file)) {
    require __DIR__ . '/../app/views/' . $file;
} else {
    require __DIR__ . '/../app/views/landing.php';
}
