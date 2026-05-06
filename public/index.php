<?php

declare(strict_types=1);

<<<<<<< HEAD
require __DIR__ . '/../app/bootstrap.php';
=======
require __DIR__ . '/../app/includes/session.php';
>>>>>>> d32810119b58bc9e2967e699ffb7232a7c867b55
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
<<<<<<< HEAD
=======
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
>>>>>>> d32810119b58bc9e2967e699ffb7232a7c867b55
    }
}

$page = $_GET['page'] ?? 'landing';
$pageController->enforceAccess((string) $page);
$pageController->render((string) $page);
