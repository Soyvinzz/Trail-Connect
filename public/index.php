<?php

declare(strict_types=1);

require __DIR__ . '/../app/includes/session.php';
tc_seed_data();

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
        $eventId = (int) ($_POST['event_id'] ?? 0);
        $title = trim((string) ($_POST['title'] ?? ''));
        $trail = trim((string) ($_POST['trail'] ?? ''));
        if ($title === '' || $trail === '') {
            header('Location: index.php?page=create_event&error=event_required');
            exit;
        }
        $existing = $eventId > 0 ? tc_find_event($eventId) : null;
        $savedId = tc_save_event([
            'id' => $eventId > 0 ? $eventId : null,
            'title' => $title,
            'trail' => $trail,
            'date' => (string) ($_POST['date'] ?? ''),
            'time' => (string) ($_POST['time'] ?? '08:00'),
            'difficulty' => (string) ($_POST['difficulty'] ?? 'mod'),
            'meet' => trim((string) ($_POST['meet'] ?? '')),
            'max' => (int) ($_POST['max'] ?? 12),
            'desc' => trim((string) ($_POST['desc'] ?? '')),
            'approval' => (string) ($_POST['approval'] ?? 'manual'),
            'organizer' => (string) ($existing['organizer'] ?? 'TrailConnect Organizer'),
            'status' => 'published',
        ]);
        $msg = $eventId > 0 ? 'event_updated' : 'event_created';
        header('Location: index.php?page=my_event&msg=' . $msg . '&event_id=' . $savedId);
        exit;
    }
    if ($action === 'delete_event') {
        $eventId = (int) ($_POST['event_id'] ?? 0);
        if ($eventId > 0) {
            tc_delete_event($eventId);
        }
        header('Location: index.php?page=my_event&msg=event_deleted');
        exit;
    }
    if ($action === 'join_event') {
        $eventId = (int) ($_POST['event_id'] ?? 0);
        $returnTo = (string) ($_POST['return_to'] ?? 'index.php?page=my_event');
        if ($returnTo === '' || strpos($returnTo, 'index.php') !== 0) {
            $returnTo = 'index.php?page=my_event';
        }
        if ($eventId > 0) {
            $name = tc_display_name();
            $existingRequest = null;
            foreach (tc_join_requests() as $request) {
                if ((int) $request['event_id'] === $eventId && (string) $request['hiker_name'] === $name) {
                    $existingRequest = $request;
                    break;
                }
            }
            if ($existingRequest !== null) {
                $existingRequest['status'] = 'pending';
                $existingRequest['requested_at'] = date('Y-m-d H:i:s');
                tc_save_join_request($existingRequest);
            } else {
                tc_save_join_request([
                    'event_id' => $eventId,
                    'hiker_name' => $name,
                    'status' => 'pending',
                    'requested_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }
        $separator = strpos($returnTo, '?') !== false ? '&' : '?';
        header('Location: ' . $returnTo . $separator . 'msg=join_request_submitted');
        exit;
    }
    if ($action === 'request_status') {
        $requestId = (int) ($_POST['request_id'] ?? 0);
        $status = (string) ($_POST['status'] ?? 'pending');
        $requests = tc_join_requests();
        if ($requestId > 0 && isset($requests[$requestId])) {
            $item = $requests[$requestId];
            $item['status'] = in_array($status, ['approved', 'declined', 'pending'], true) ? $status : 'pending';
            tc_save_join_request($item);
        }
        header('Location: index.php?page=my_event&msg=request_status_updated');
        exit;
    }
    if ($action === 'delete_request') {
        $requestId = (int) ($_POST['request_id'] ?? 0);
        if ($requestId > 0) {
            tc_delete_join_request($requestId);
        }
        header('Location: index.php?page=my_event&msg=join_request_deleted');
        exit;
    }
    if ($action === 'save_update') {
        $updateId = (int) ($_POST['update_id'] ?? 0);
        $message = trim((string) ($_POST['message'] ?? ''));
        $eventId = (int) ($_POST['event_id'] ?? 0);
        if ($message === '' || $eventId <= 0) {
            header('Location: index.php?page=updates&error=update_required');
            exit;
        }
        tc_save_update([
            'id' => $updateId > 0 ? $updateId : null,
            'event_id' => $eventId,
            'type' => trim((string) ($_POST['type'] ?? 'General')),
            'message' => $message,
            'posted_at' => date('Y-m-d H:i:s'),
        ]);
        $msg = $updateId > 0 ? 'update_saved' : 'update_published';
        header('Location: index.php?page=updates&msg=' . $msg);
        exit;
    }
    if ($action === 'delete_update') {
        $updateId = (int) ($_POST['update_id'] ?? 0);
        if ($updateId > 0) {
            tc_delete_update($updateId);
        }
        header('Location: index.php?page=updates&msg=update_deleted');
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
