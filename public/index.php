<?php

declare(strict_types=1);

require __DIR__ . '/../app/bootstrap.php';
tc_seed_data();

$pageController = new PageController();
$pageController->handleRoleSwitch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $controllers = [
        new AuthController(),
        new ProfileController(),
        new EventController(),
        new ReviewController(),
        new SettingsController(),
    ];
    foreach ($controllers as $controller) {
        if ($controller->handle($action)) {
            exit;
        }
    }
}

$page = $_GET['page'] ?? 'landing';
$pageController->enforceAccess((string) $page);
$pageController->render((string) $page);
