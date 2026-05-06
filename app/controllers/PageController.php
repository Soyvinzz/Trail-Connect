<?php
declare(strict_types=1);

final class PageController
{
    /** @var array<string,string> */
    private array $views = [
        'landing' => 'landing.php',
        'login' => 'login.php',
        'register' => 'register.php',
        'forgot_password' => 'forgot_password.php',
        'verify_email' => 'verify_email.php',
        'reset_password' => 'reset_password.php',
        'dashboard' => 'dashboard.php',
        'hiking_101' => 'hiking_101.php',
        'find_hikes' => 'find_hikes.php',
        'event_details' => 'event_details.php',
        'create_event' => 'create_event.php',
        'my_event' => 'my_event.php',
        'updates' => 'updates.php',
        'profile' => 'profile.php',
        'settings' => 'settings.php',
        'reviews' => 'reviews.php',
        'admin' => 'admin.php',
    ];

    /** @var string[] */
    private array $protectedPages = [
        'dashboard', 'hiking_101', 'find_hikes', 'event_details',
        'create_event', 'my_event', 'updates', 'profile', 'settings', 'reviews', 'admin',
    ];

    public function handleRoleSwitch(): void
    {
        if (!isset($_GET['switch_role']) || !tc_logged_in()) {
            return;
        }
        $targetRole = (string) ($_GET['switch_role'] ?? 'hiker');
        $targetRole = $targetRole === 'organizer' ? 'organizer' : 'hiker';
        tc_set_logged_in(false);
        tc_set_role($targetRole);
        header('Location: index.php?page=login&switch_required=1&role=' . $targetRole);
        exit;
    }

    public function enforceAccess(string $page): void
    {
        if (in_array($page, $this->protectedPages, true) && !tc_logged_in()) {
            header('Location: index.php?page=login&required=1');
            exit;
        }
        if (in_array($page, ['create_event', 'updates'], true) && tc_role() !== 'organizer') {
            header('Location: index.php?page=dashboard');
            exit;
        }
        if (in_array($page, ['find_hikes', 'reviews'], true) && tc_role() !== 'hiker') {
            header('Location: index.php?page=dashboard');
            exit;
        }
        if ($page === 'admin' && !tc_is_admin_user()) {
            header('Location: index.php?page=dashboard');
            exit;
        }
    }

    public function render(string $page): void
    {
        $file = $this->views[$page] ?? null;
        if ($file && is_file(__DIR__ . '/../views/' . $file)) {
            require __DIR__ . '/../views/' . $file;
            return;
        }
        require __DIR__ . '/../views/landing.php';
    }
}
