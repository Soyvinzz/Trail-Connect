<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function tc_role(): string
{
    $r = $_SESSION['tc_role'] ?? 'hiker';

    return $r === 'organizer' ? 'organizer' : 'hiker';
}

function tc_set_role(string $r): void
{
    $_SESSION['tc_role'] = $r === 'organizer' ? 'organizer' : 'hiker';
}

function tc_logged_in(): bool
{
    return !empty($_SESSION['tc_logged_in']);
}

function tc_set_logged_in(bool $v): void
{
    $_SESSION['tc_logged_in'] = $v;
}

function tc_display_name(): string
{
    $n = $_SESSION['tc_name'] ?? '';

    return $n !== '' ? $n : (tc_role() === 'organizer' ? 'Alex Rivers' : 'Jordan Peak');
}

function tc_seed_data(): void
{
    if (!isset($_SESSION['tc_events']) || !is_array($_SESSION['tc_events'])) {
        $_SESSION['tc_events'] = [
            1 => [
                'id' => 1,
                'title' => 'Mt. Pulag · Akiki–Ambangeg batch',
                'trail' => 'Mt. Pulag · Akiki–Ambangeg (Benguet / Ifugao)',
                'date' => '2026-05-03',
                'time' => '05:00',
                'difficulty' => 'hard',
                'meet' => 'Benguet briefing point',
                'max' => 12,
                'desc' => 'Major hike batch with staged ascent and safety regroup points.',
                'approval' => 'manual',
                'organizer' => 'Cordillera Guides',
                'status' => 'published',
            ],
            2 => [
                'id' => 2,
                'title' => 'Mt. Guiting-Guiting · Knife-edge roster',
                'trail' => 'Mt. Guiting-Guiting Knife-Edge (Romblon)',
                'date' => '2026-06-01',
                'time' => '06:00',
                'difficulty' => 'vhard',
                'meet' => 'Sibuyan staging area',
                'max' => 8,
                'desc' => 'Technical climb for experienced hikers only.',
                'approval' => 'manual',
                'organizer' => 'Sibuyan Expeditions',
                'status' => 'published',
            ],
        ];
    }

    if (!isset($_SESSION['tc_join_requests']) || !is_array($_SESSION['tc_join_requests'])) {
        $_SESSION['tc_join_requests'] = [
            1 => [
                'id' => 1,
                'event_id' => 1,
                'hiker_name' => 'Alex Reyes',
                'status' => 'pending',
                'requested_at' => '2026-04-03 10:15:00',
            ],
            2 => [
                'id' => 2,
                'event_id' => 2,
                'hiker_name' => 'Jam Santos',
                'status' => 'pending',
                'requested_at' => '2026-04-02 15:30:00',
            ],
        ];
    }

    if (!isset($_SESSION['tc_updates']) || !is_array($_SESSION['tc_updates'])) {
        $_SESSION['tc_updates'] = [
            1 => [
                'id' => 1,
                'event_id' => 1,
                'type' => 'Meet point',
                'message' => 'Meet at the agreed DENR briefing area. Group flag is teal + white.',
                'posted_at' => '2026-04-03 14:20:00',
            ],
            2 => [
                'id' => 2,
                'event_id' => 2,
                'type' => 'Safety',
                'message' => 'If wind gusts exceed comfort on knife-edge, shorten segment and regroup.',
                'posted_at' => '2026-04-02 18:45:00',
            ],
        ];
    }
}

function tc_events(): array
{
    tc_seed_data();

    return $_SESSION['tc_events'];
}

function tc_find_event(int $eventId): ?array
{
    $events = tc_events();

    return $events[$eventId] ?? null;
}

function tc_save_event(array $event): int
{
    tc_seed_data();
    $id = isset($event['id']) ? (int) $event['id'] : 0;
    if ($id <= 0) {
        $id = empty($_SESSION['tc_events']) ? 1 : (max(array_keys($_SESSION['tc_events'])) + 1);
    }
    $event['id'] = $id;
    $_SESSION['tc_events'][$id] = $event;

    return $id;
}

function tc_delete_event(int $eventId): void
{
    tc_seed_data();
    unset($_SESSION['tc_events'][$eventId]);
    foreach ($_SESSION['tc_join_requests'] as $id => $request) {
        if ((int) $request['event_id'] === $eventId) {
            unset($_SESSION['tc_join_requests'][$id]);
        }
    }
    foreach ($_SESSION['tc_updates'] as $id => $update) {
        if ((int) $update['event_id'] === $eventId) {
            unset($_SESSION['tc_updates'][$id]);
        }
    }
}

function tc_join_requests(): array
{
    tc_seed_data();

    return $_SESSION['tc_join_requests'];
}

function tc_save_join_request(array $request): int
{
    tc_seed_data();
    $id = isset($request['id']) ? (int) $request['id'] : 0;
    if ($id <= 0) {
        $id = empty($_SESSION['tc_join_requests']) ? 1 : (max(array_keys($_SESSION['tc_join_requests'])) + 1);
    }
    $request['id'] = $id;
    $_SESSION['tc_join_requests'][$id] = $request;

    return $id;
}

function tc_delete_join_request(int $requestId): void
{
    tc_seed_data();
    unset($_SESSION['tc_join_requests'][$requestId]);
}

function tc_updates(): array
{
    tc_seed_data();

    return $_SESSION['tc_updates'];
}

function tc_save_update(array $update): int
{
    tc_seed_data();
    $id = isset($update['id']) ? (int) $update['id'] : 0;
    if ($id <= 0) {
        $id = empty($_SESSION['tc_updates']) ? 1 : (max(array_keys($_SESSION['tc_updates'])) + 1);
    }
    $update['id'] = $id;
    $_SESSION['tc_updates'][$id] = $update;

    return $id;
}

function tc_delete_update(int $updateId): void
{
    tc_seed_data();
    unset($_SESSION['tc_updates'][$updateId]);
}
