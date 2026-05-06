<?php

declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/database.php';

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
    if (!$v) {
        unset($_SESSION['tc_user_id'], $_SESSION['tc_user_email']);
    }
}

function tc_current_user_id(): int
{
    return (int) ($_SESSION['tc_user_id'] ?? 0);
}

function tc_current_user_email(): string
{
    return (string) ($_SESSION['tc_user_email'] ?? '');
}

function tc_current_avatar_path(): string
{
    return (string) ($_SESSION['tc_avatar_path'] ?? '');
}

function tc_set_auth_user(array $user): void
{
    $role = ((string) ($user['role'] ?? 'hiker')) === 'organizer' ? 'organizer' : 'hiker';
    tc_set_role($role);
    tc_set_logged_in(true);
    $_SESSION['tc_user_id'] = (int) ($user['id'] ?? 0);
    $_SESSION['tc_user_email'] = (string) ($user['email'] ?? '');
    $_SESSION['tc_avatar_path'] = (string) ($user['avatar_path'] ?? '');
    tc_save_profile([
        'display_name' => (string) ($user['full_name'] ?? tc_display_name()),
        'bio' => (string) ($user['bio'] ?? ''),
        'home' => (string) ($user['home'] ?? ''),
        'phone_number' => (string) ($user['phone_number'] ?? ''),
        'current_address' => (string) ($user['current_address'] ?? ''),
        'hiking_level' => (string) ($user['hiking_level'] ?? ''),
        'minor_hikes_completed' => (int) ($user['minor_hikes_completed'] ?? 0),
        'major_hikes_completed' => (int) ($user['major_hikes_completed'] ?? 0),
        'emergency_contact_name' => (string) ($user['emergency_contact_name'] ?? ''),
        'emergency_contact_number' => (string) ($user['emergency_contact_number'] ?? ''),
        'medical_notes' => (string) ($user['medical_notes'] ?? ''),
    ]);
}

function tc_refresh_profile_from_db(): void
{
    $id = tc_current_user_id();
    if ($id <= 0) {
        return;
    }
    try {
        tc_db_migrate();
        $user = tc_db_find_user_by_id($id);
    } catch (\Throwable $e) {
        return;
    }
    if (!is_array($user)) {
        return;
    }
    $role = tc_role();
    $existing = $_SESSION['tc_profiles'][$role] ?? tc_profile_defaults($role);
    if (!is_array($existing)) {
        $existing = tc_profile_defaults($role);
    }
    $existing['display_name'] = (string) ($user['full_name'] ?? $existing['display_name']);
    $existing['bio'] = (string) ($user['bio'] ?? $existing['bio']);
    $existing['home'] = (string) ($user['home'] ?? $existing['home']);
    $existing['two_factor_enabled'] = !empty($user['two_factor_enabled']);
    $existing['two_factor_secret'] = (string) ($user['two_factor_secret'] ?? '');
    $existing['two_factor_temp_secret'] = (string) ($user['two_factor_temp_secret'] ?? '');
    $existing['phone_number'] = (string) ($user['phone_number'] ?? $existing['phone_number'] ?? '');
    $existing['current_address'] = (string) ($user['current_address'] ?? $existing['current_address'] ?? '');
    $existing['hiking_level'] = (string) ($user['hiking_level'] ?? $existing['hiking_level'] ?? '');
    $existing['minor_hikes_completed'] = (int) ($user['minor_hikes_completed'] ?? $existing['minor_hikes_completed'] ?? 0);
    $existing['major_hikes_completed'] = (int) ($user['major_hikes_completed'] ?? $existing['major_hikes_completed'] ?? 0);
    $existing['emergency_contact_name'] = (string) ($user['emergency_contact_name'] ?? $existing['emergency_contact_name'] ?? '');
    $existing['emergency_contact_number'] = (string) ($user['emergency_contact_number'] ?? $existing['emergency_contact_number'] ?? '');
    $existing['medical_notes'] = (string) ($user['medical_notes'] ?? $existing['medical_notes'] ?? '');
    $_SESSION['tc_profiles'][$role] = $existing;
    $_SESSION['tc_user_email'] = (string) ($user['email'] ?? '');
    $_SESSION['tc_avatar_path'] = (string) ($user['avatar_path'] ?? '');
}

function tc_profile_defaults(string $role): array
{
    if ($role === 'organizer') {
        return [
            'display_name' => 'Alex Rivers',
            'bio' => 'Organizer for major Philippines climbs with safety-first pacing.',
            'home' => 'Baguio City, Philippines',
            'phone_number' => '',
            'current_address' => 'Baguio City, Philippines',
            'hiking_level' => '',
            'minor_hikes_completed' => 0,
            'major_hikes_completed' => 0,
            'emergency_contact_name' => '',
            'emergency_contact_number' => '',
            'medical_notes' => '',
            'two_factor_enabled' => false,
            'two_factor_secret' => '',
            'two_factor_temp_secret' => '',
        ];
    }

    return [
        'display_name' => 'Jordan Peak',
        'bio' => 'Weekend hiker exploring Luzon, Visayas, and Mindanao trails.',
        'home' => 'Baguio City, Philippines',
        'phone_number' => '',
        'current_address' => 'Baguio City, Philippines',
        'hiking_level' => 'beginner',
        'minor_hikes_completed' => 0,
        'major_hikes_completed' => 0,
        'emergency_contact_name' => '',
        'emergency_contact_number' => '',
        'medical_notes' => '',
        'two_factor_enabled' => false,
        'two_factor_secret' => '',
        'two_factor_temp_secret' => '',
    ];
}

function tc_display_name(): string
{
    $profile = tc_profile();
    $n = (string) ($profile['display_name'] ?? '');

    return $n !== '' ? $n : (tc_role() === 'organizer' ? 'Alex Rivers' : 'Jordan Peak');
}

function tc_event_manageable_by_current_organizer(?array $event): bool
{
    if (!is_array($event)) {
        return false;
    }
    if (tc_role() !== 'organizer') {
        return false;
    }
    $oid = isset($event['organizer_user_id']) ? (int) $event['organizer_user_id'] : 0;
    if ($oid <= 0) {
        // DB-backed rows must have an explicit owner for isolation between organizers.
        // Session-only demo seeds omit organizer_user_id; allow any logged-in organizer there.
        return !tc_db_entities_available();
    }

    return tc_current_user_id() > 0 && tc_current_user_id() === $oid;
}

function tc_seed_data(): void
{
    try {
        tc_db_migrate();
    } catch (\Throwable $e) {
        // Keep app usable even when DB is unavailable.
    }
    $defaultEvents = [
        [
            'title' => 'Mt. Apo · Kapatagan–Kidapawan expedition',
            'trail' => 'Mt. Apo · Kapatagan–Kidapawan (Davao / Cotabato)',
            'date' => '2026-05-18',
            'time' => '04:30',
            'difficulty' => 'hard',
            'meet' => 'Kapatagan jump-off briefing area',
            'max' => 14,
            'desc' => 'Highest peak in the Philippines with multi-terrain traverse and staged safety regroup points.',
            'approval' => 'manual',
            'organizer' => 'Mindanao Ascents',
            'status' => 'published',
        ],
        [
            'title' => 'Mt. Dulang-Dulang summit push',
            'trail' => 'Mt. Dulang-Dulang (Bukidnon)',
            'date' => '2026-05-25',
            'time' => '05:00',
            'difficulty' => 'hard',
            'meet' => 'Kitanglad ranger station',
            'max' => 10,
            'desc' => 'Second-highest peak with long mossy ascent and weather-sensitive summit window.',
            'approval' => 'manual',
            'organizer' => 'Bukidnon Traverse Team',
            'status' => 'published',
        ],
        [
            'title' => 'Mt. Pulag · Akiki–Ambangeg batch',
            'trail' => 'Mt. Pulag · Akiki–Ambangeg (Benguet / Ifugao)',
            'date' => '2026-05-31',
            'time' => '05:00',
            'difficulty' => 'hard',
            'meet' => 'Benguet briefing point',
            'max' => 12,
            'desc' => 'Major hike batch with staged ascent and safety regroup points.',
            'approval' => 'manual',
            'organizer' => 'Cordillera Guides',
            'status' => 'published',
        ],
        [
            'title' => 'Mt. Kitanglad ridge roster',
            'trail' => 'Mt. Kitanglad (Bukidnon)',
            'date' => '2026-06-06',
            'time' => '05:30',
            'difficulty' => 'hard',
            'meet' => 'Malaybalay registration gate',
            'max' => 10,
            'desc' => 'High-altitude ridge day with strict pace control and visibility checks.',
            'approval' => 'manual',
            'organizer' => 'Bukidnon Traverse Team',
            'status' => 'published',
        ],
        [
            'title' => 'Mt. Kalatungan high camp climb',
            'trail' => 'Mt. Kalatungan (Bukidnon)',
            'date' => '2026-06-12',
            'time' => '06:00',
            'difficulty' => 'hard',
            'meet' => 'Valencia meet point',
            'max' => 9,
            'desc' => 'Remote and muddy major climb with river crossings and high camp pacing.',
            'approval' => 'manual',
            'organizer' => 'Mindanao Ascents',
            'status' => 'published',
        ],
        [
            'title' => 'Mt. Tabayoc mossy ascent window',
            'trail' => 'Mt. Tabayoc (Benguet)',
            'date' => '2026-06-20',
            'time' => '05:00',
            'difficulty' => 'hard',
            'meet' => 'Kabayan municipal hall',
            'max' => 12,
            'desc' => 'Steep mossy-forest ascent with sustained gradient and wet-trail risk management.',
            'approval' => 'manual',
            'organizer' => 'Cordillera Guides',
            'status' => 'published',
        ],
        [
            'title' => 'Mt. Ragang technical volcano climb',
            'trail' => 'Mt. Ragang (Lanao del Sur)',
            'date' => '2026-06-28',
            'time' => '04:30',
            'difficulty' => 'vhard',
            'meet' => 'Lanao basecamp',
            'max' => 8,
            'desc' => 'Remote stratovolcano climb requiring advanced logistics and steep terrain handling.',
            'approval' => 'manual',
            'organizer' => 'Mindanao Ascents',
            'status' => 'published',
        ],
        [
            'title' => 'Mt. Halcon technical ascent',
            'trail' => 'Mt. Halcon Technical Ascent (Mindoro)',
            'date' => '2026-07-05',
            'time' => '05:00',
            'difficulty' => 'vhard',
            'meet' => 'Baco orientation area',
            'max' => 8,
            'desc' => 'Punishing multi-day route with river crossings and steep mossy ridges.',
            'approval' => 'manual',
            'organizer' => 'Mindoro Alpine Circle',
            'status' => 'published',
        ],
        [
            'title' => 'Mt. Mantalingajan expedition roster',
            'trail' => 'Mt. Mantalingajan (Palawan)',
            'date' => '2026-07-14',
            'time' => '06:00',
            'difficulty' => 'vhard',
            'meet' => 'Rizal, Palawan staging point',
            'max' => 8,
            'desc' => 'Remote and committing expedition with knife-edge segments and limestone exposure.',
            'approval' => 'manual',
            'organizer' => 'Palawan Peak Initiative',
            'status' => 'published',
        ],
        [
            'title' => 'Mt. Guiting-Guiting · Knife-edge roster',
            'trail' => 'Mt. Guiting-Guiting Knife-Edge (Romblon)',
            'date' => '2026-07-22',
            'time' => '06:00',
            'difficulty' => 'vhard',
            'meet' => 'Sibuyan staging area',
            'max' => 8,
            'desc' => 'Technical climb for experienced hikers only with exposed ridges and rock sections.',
            'approval' => 'manual',
            'organizer' => 'Sibuyan Expeditions',
            'status' => 'published',
        ],
        [
            'title' => 'Mt. Mandalagan · Tinagong Dagat weekend',
            'trail' => 'Mt. Mandalagan (Negros Occidental)',
            'date' => '2026-08-02',
            'time' => '05:30',
            'difficulty' => 'mod',
            'meet' => 'Sagay jump-off / briefing',
            'max' => 14,
            'desc' => 'Minor hike focused on volcanic plateau pacing and Tinagong Dagat crater sector.',
            'approval' => 'manual',
            'organizer' => 'Negros Dayhike Collective',
            'status' => 'published',
        ],
        [
            'title' => 'Mt. Lingguhob · Leon dayhike batch',
            'trail' => 'Mt. Lingguhob (Iloilo)',
            'date' => '2026-08-09',
            'time' => '05:00',
            'difficulty' => 'mod',
            'meet' => 'Leon municipal hall meet',
            'max' => 16,
            'desc' => 'Western Visayas ridge dayhike with moderate elevation gain and weather checks.',
            'approval' => 'manual',
            'organizer' => 'Panay Ridge Guides',
            'status' => 'published',
        ],
        [
            'title' => 'Mt. Talinis · Twin Lakes circuit',
            'trail' => 'Mt. Talinis · Cuernos de Negros (Negros Oriental)',
            'date' => '2026-08-16',
            'time' => '05:00',
            'difficulty' => 'mod',
            'meet' => 'Valencia / Balinsasayao briefing point',
            'max' => 12,
            'desc' => 'Cuernos de Negros lake circuit—popular minor-style traverse with crater-lake highlights.',
            'approval' => 'manual',
            'organizer' => 'Dumaguete Trail Friends',
            'status' => 'published',
        ],
        [
            'title' => 'Mt. Igatmon · Igbaras limestone dayhike',
            'trail' => 'Mt. Igatmon (Igbaras, Iloilo)',
            'date' => '2026-08-23',
            'time' => '05:30',
            'difficulty' => 'easy',
            'meet' => 'Igbaras tourism / registration',
            'max' => 18,
            'desc' => 'Short limestone summit outing—skills-friendly minor hike with scrambles and views.',
            'approval' => 'manual',
            'organizer' => 'Panay Ridge Guides',
            'status' => 'published',
        ],
        [
            'title' => 'Mt. Daat · Monkayo upland dayhike',
            'trail' => 'Mt. Daat (Davao de Oro)',
            'date' => '2026-08-30',
            'time' => '05:00',
            'difficulty' => 'easy',
            'meet' => 'Monkayo staging / barangay hall',
            'max' => 16,
            'desc' => 'Mindanao montane forest minor hike—good for conditioning before bigger Davao climbs.',
            'approval' => 'manual',
            'organizer' => 'Mindanao Ascents',
            'status' => 'published',
        ],
    ];

    if (!isset($_SESSION['tc_profiles']) || !is_array($_SESSION['tc_profiles'])) {
        $_SESSION['tc_profiles'] = [
            'hiker' => tc_profile_defaults('hiker'),
            'organizer' => tc_profile_defaults('organizer'),
        ];
    }
    foreach (['hiker', 'organizer'] as $role) {
        $profile = $_SESSION['tc_profiles'][$role] ?? [];
        if (!is_array($profile)) {
            $profile = [];
        }
        $_SESSION['tc_profiles'][$role] = array_merge(tc_profile_defaults($role), $profile);
    }

    $defaultRequestSeeds = [
        ['title' => 'Mt. Apo · Kapatagan–Kidapawan expedition', 'hiker_name' => 'Jordan Peak', 'status' => 'approved', 'requested_at' => '2026-04-20 09:15:00'],
        ['title' => 'Mt. Dulang-Dulang summit push', 'hiker_name' => 'Jordan Peak', 'status' => 'pending', 'requested_at' => '2026-04-21 11:40:00'],
        ['title' => 'Mt. Pulag · Akiki–Ambangeg batch', 'hiker_name' => 'Jordan Peak', 'status' => 'declined', 'requested_at' => '2026-04-17 08:05:00'],
        ['title' => 'Mt. Guiting-Guiting · Knife-edge roster', 'hiker_name' => 'Alex Reyes', 'status' => 'pending', 'requested_at' => '2026-04-03 10:15:00'],
        ['title' => 'Mt. Halcon technical ascent', 'hiker_name' => 'Jam Santos', 'status' => 'pending', 'requested_at' => '2026-04-02 15:30:00'],
    ];
    $defaultUpdatesSeed = [
        [
            '_event_title' => 'Mt. Pulag · Akiki–Ambangeg batch',
            'type' => 'Meet point',
            'message' => 'Meet at the agreed DENR briefing area. Group flag is teal + white.',
            'posted_at' => '2026-04-03 14:20:00',
        ],
        [
            '_event_title' => 'Mt. Guiting-Guiting · Knife-edge roster',
            'type' => 'Safety',
            'message' => 'If wind gusts exceed comfort on knife-edge, shorten segment and regroup.',
            'posted_at' => '2026-04-02 18:45:00',
        ],
    ];
    $defaultReviewsSeed = [
        [
            '_event_title' => 'Mt. Pulag · Akiki–Ambangeg batch',
            'author_name' => 'Lian Cruz',
            'recipient' => 'Cordillera Guides (organizer)',
            'stars' => 5,
            'text' => 'Weather shifted fast; lead guide managed pacing and safety very well.',
            'posted_at' => '2026-03-20 08:30:00',
        ],
        [
            '_event_title' => 'Mt. Guiting-Guiting · Knife-edge roster',
            'author_name' => 'Rico Santos',
            'recipient' => 'Co-hiker — Nina Morales',
            'stars' => 4,
            'text' => 'Prepared and supportive on exposed sections. Great teamwork.',
            'posted_at' => '2026-03-15 12:20:00',
        ],
    ];

    $useDbEntities = false;
    try {
        $useDbEntities = tc_db_entities_available();
        if ($useDbEntities) {
            tc_db_seed_entities_if_needed($defaultEvents, $defaultRequestSeeds, $defaultUpdatesSeed, $defaultReviewsSeed);
            tc_db_ensure_demo_users();
            tc_db_ensure_primary_organizer_dashboard_demo();
        }
    } catch (\Throwable $e) {
        $useDbEntities = false;
    }

    if (!$useDbEntities) {
        if (!isset($_SESSION['tc_events']) || !is_array($_SESSION['tc_events'])) {
            $_SESSION['tc_events'] = [];
        }
        $events = $_SESSION['tc_events'];
        $eventTitleIndex = [];
        foreach ($events as $eventId => $event) {
            $titleKey = strtolower(trim((string) ($event['title'] ?? '')));
            if ($titleKey !== '') {
                $eventTitleIndex[$titleKey] = (int) $eventId;
            }
        }
        $nextEventId = empty($events) ? 1 : (max(array_keys($events)) + 1);
        foreach ($defaultEvents as $defaultEvent) {
            $titleKey = strtolower(trim((string) ($defaultEvent['title'] ?? '')));
            if ($titleKey === '' || isset($eventTitleIndex[$titleKey])) {
                continue;
            }
            $defaultEvent['id'] = $nextEventId;
            $events[$nextEventId] = $defaultEvent;
            $eventTitleIndex[$titleKey] = $nextEventId;
            $nextEventId++;
        }
        $_SESSION['tc_events'] = $events;

        if (!isset($_SESSION['tc_join_requests']) || !is_array($_SESSION['tc_join_requests'])) {
            $_SESSION['tc_join_requests'] = [];
        }
        $requests = $_SESSION['tc_join_requests'];
        $requestKeyIndex = [];
        foreach ($requests as $request) {
            $reqEventId = (int) ($request['event_id'] ?? 0);
            $reqHiker = strtolower(trim((string) ($request['hiker_name'] ?? '')));
            if ($reqEventId > 0 && $reqHiker !== '') {
                $requestKeyIndex[$reqEventId . '|' . $reqHiker] = true;
            }
        }
        $nextRequestId = empty($requests) ? 1 : (max(array_keys($requests)) + 1);
        foreach ($defaultRequestSeeds as $seed) {
            $seedTitleKey = strtolower(trim((string) ($seed['title'] ?? '')));
            $seedEventId = $eventTitleIndex[$seedTitleKey] ?? 0;
            $seedHiker = strtolower(trim((string) ($seed['hiker_name'] ?? '')));
            if ($seedEventId <= 0 || $seedHiker === '') {
                continue;
            }
            $key = $seedEventId . '|' . $seedHiker;
            if (isset($requestKeyIndex[$key])) {
                continue;
            }
            $requests[$nextRequestId] = [
                'id' => $nextRequestId,
                'event_id' => $seedEventId,
                'hiker_name' => (string) $seed['hiker_name'],
                'status' => in_array((string) $seed['status'], ['pending', 'approved', 'declined'], true) ? (string) $seed['status'] : 'pending',
                'requested_at' => (string) $seed['requested_at'],
            ];
            $requestKeyIndex[$key] = true;
            $nextRequestId++;
        }
        $_SESSION['tc_join_requests'] = $requests;

        if (!isset($_SESSION['tc_updates']) || !is_array($_SESSION['tc_updates'])) {
            $_SESSION['tc_updates'] = [];
        }
        if ($_SESSION['tc_updates'] === []) {
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

        if (!isset($_SESSION['tc_reviews']) || !is_array($_SESSION['tc_reviews'])) {
            $_SESSION['tc_reviews'] = [];
        }
        if ($_SESSION['tc_reviews'] === []) {
            $_SESSION['tc_reviews'] = [
                1 => [
                    'id' => 1,
                    'author_name' => 'Jordan Peak',
                    'recipient' => 'Cordillera Guides (organizer)',
                    'event_id' => 1,
                    'stars' => 5,
                    'text' => 'Weather shifted fast; lead guide managed pacing and safety very well.',
                    'posted_at' => '2026-03-20 08:30:00',
                ],
                2 => [
                    'id' => 2,
                    'author_name' => 'Alex Rivers',
                    'recipient' => 'Co-hiker — Alex Reyes',
                    'event_id' => 2,
                    'stars' => 4,
                    'text' => 'Prepared and supportive on exposed sections. Great teamwork.',
                    'posted_at' => '2026-03-15 12:20:00',
                ],
            ];
        }
    }

    if (!isset($_SESSION['tc_notices']) || !is_array($_SESSION['tc_notices'])) {
        $_SESSION['tc_notices'] = [
            'hiker' => [],
            'organizer' => [],
            'users' => [],
        ];
    }
    if (!isset($_SESSION['tc_notices']['users']) || !is_array($_SESSION['tc_notices']['users'])) {
        $_SESSION['tc_notices']['users'] = [];
    }
}

function tc_profile(): array
{
    tc_seed_data();
    tc_refresh_profile_from_db();
    $role = tc_role();
    $profile = $_SESSION['tc_profiles'][$role] ?? [];
    if (!is_array($profile)) {
        $profile = [];
    }

    return array_merge(tc_profile_defaults($role), $profile);
}

function tc_save_profile(array $profile): void
{
    tc_seed_data();
    $role = tc_role();
    $existing = tc_profile();
    $next = array_merge($existing, [
        'display_name' => trim((string) ($profile['display_name'] ?? $existing['display_name'])),
        'bio' => trim((string) ($profile['bio'] ?? $existing['bio'])),
        'home' => trim((string) ($profile['home'] ?? $existing['home'])),
        'phone_number' => trim((string) ($profile['phone_number'] ?? $existing['phone_number'] ?? '')),
        'current_address' => trim((string) ($profile['current_address'] ?? $existing['current_address'] ?? '')),
        'hiking_level' => in_array((string) ($profile['hiking_level'] ?? $existing['hiking_level'] ?? ''), ['beginner', 'minor', 'intermediate', 'advanced'], true)
            ? (string) ($profile['hiking_level'] ?? $existing['hiking_level'] ?? '') : '',
        'minor_hikes_completed' => max(0, (int) ($profile['minor_hikes_completed'] ?? $existing['minor_hikes_completed'] ?? 0)),
        'major_hikes_completed' => max(0, (int) ($profile['major_hikes_completed'] ?? $existing['major_hikes_completed'] ?? 0)),
        'emergency_contact_name' => trim((string) ($profile['emergency_contact_name'] ?? $existing['emergency_contact_name'] ?? '')),
        'emergency_contact_number' => trim((string) ($profile['emergency_contact_number'] ?? $existing['emergency_contact_number'] ?? '')),
        'medical_notes' => trim((string) ($profile['medical_notes'] ?? $existing['medical_notes'] ?? '')),
    ]);
    $_SESSION['tc_profiles'][$role] = $next;
    $userId = tc_current_user_id();
    if ($userId > 0) {
        try {
            tc_db_update_user_profile($userId, [
                'full_name' => (string) $next['display_name'],
                'bio' => (string) $next['bio'],
                'home' => (string) $next['home'],
                'phone_number' => $next['phone_number'] !== '' ? (string) $next['phone_number'] : null,
                'current_address' => $next['current_address'] !== '' ? (string) $next['current_address'] : null,
                'hiking_level' => $next['hiking_level'] !== '' ? (string) $next['hiking_level'] : null,
                'minor_hikes_completed' => (int) $next['minor_hikes_completed'],
                'major_hikes_completed' => (int) $next['major_hikes_completed'],
                'emergency_contact_name' => $next['emergency_contact_name'] !== '' ? (string) $next['emergency_contact_name'] : null,
                'emergency_contact_number' => $next['emergency_contact_number'] !== '' ? (string) $next['emergency_contact_number'] : null,
                'medical_notes' => $next['medical_notes'] !== '' ? (string) $next['medical_notes'] : null,
            ]);
        } catch (\Throwable $e) {
        }
    }
}

function tc_hiking_level_rank(string $level): int
{
    return match ($level) {
        'advanced' => 4,
        'intermediate' => 3,
        'minor' => 2,
        'beginner' => 1,
        default => 0,
    };
}

function tc_hiking_level_label(string $level): string
{
    return match ($level) {
        'advanced' => 'Advanced',
        'intermediate' => 'Intermediate',
        'minor' => 'Minor hikes level',
        'beginner' => 'Beginner',
        default => 'Not set',
    };
}

function tc_profile_completeness(array $profile): int
{
    $checks = [
        trim((string) ($profile['display_name'] ?? '')) !== '',
        trim((string) ($profile['phone_number'] ?? '')) !== '',
        trim((string) ($profile['current_address'] ?? '')) !== '',
        trim((string) ($profile['hiking_level'] ?? '')) !== '',
        isset($profile['minor_hikes_completed']),
        isset($profile['major_hikes_completed']),
        trim((string) ($profile['bio'] ?? '')) !== '',
        trim((string) ($profile['emergency_contact_name'] ?? '')) !== '',
        trim((string) ($profile['emergency_contact_number'] ?? '')) !== '',
    ];
    $met = 0;
    foreach ($checks as $ok) {
        if ($ok) {
            $met++;
        }
    }

    return (int) round(($met / max(1, count($checks))) * 100);
}

function tc_hiker_meets_event_requirements(array $event, array $profile): array
{
    $requiredLevel = (string) ($event['min_hiking_level'] ?? '');
    $requiredMinor = max(0, (int) ($event['min_minor_hikes'] ?? 0));
    $requiredMajor = max(0, (int) ($event['min_major_hikes'] ?? 0));
    if ($requiredLevel === '' && $requiredMinor <= 0 && $requiredMajor <= 0) {
        return ['ok' => true, 'reason' => ''];
    }
    $profileLevel = (string) ($profile['hiking_level'] ?? '');
    $profileMinor = max(0, (int) ($profile['minor_hikes_completed'] ?? 0));
    $profileMajor = max(0, (int) ($profile['major_hikes_completed'] ?? 0));
    if (tc_profile_completeness($profile) < 65) {
        return ['ok' => false, 'reason' => 'complete_profile'];
    }
    if ($requiredLevel !== '' && tc_hiking_level_rank($profileLevel) < tc_hiking_level_rank($requiredLevel)) {
        return ['ok' => false, 'reason' => 'level'];
    }
    if ($requiredMinor > $profileMinor) {
        return ['ok' => false, 'reason' => 'minor'];
    }
    if ($requiredMajor > $profileMajor) {
        return ['ok' => false, 'reason' => 'major'];
    }

    return ['ok' => true, 'reason' => ''];
}

function tc_delete_profile(): void
{
    tc_seed_data();
    $defaults = tc_profile_defaults(tc_role());
    $_SESSION['tc_profiles'][tc_role()] = $defaults;
    $userId = tc_current_user_id();
    if ($userId > 0) {
        try {
            tc_db_update_user_profile($userId, [
                'full_name' => (string) $defaults['display_name'],
                'bio' => (string) $defaults['bio'],
                'home' => (string) $defaults['home'],
                'avatar_path' => null,
            ]);
            $_SESSION['tc_avatar_path'] = '';
        } catch (\Throwable $e) {
        }
    }
}

function tc_events(): array
{
    tc_seed_data();
    if (tc_db_entities_available()) {
        try {
            return tc_db_events_fetch_all_assoc();
        } catch (\Throwable $e) {
        }
    }

    return isset($_SESSION['tc_events']) && is_array($_SESSION['tc_events']) ? $_SESSION['tc_events'] : [];
}

/** Published events only — global listing for hikers (all organizers’ published hikes). */
function tc_events_published(): array
{
    tc_seed_data();
    if (tc_db_entities_available()) {
        try {
            return tc_db_events_fetch_published_assoc();
        } catch (\Throwable $e) {
        }
    }
    $events = isset($_SESSION['tc_events']) && is_array($_SESSION['tc_events']) ? $_SESSION['tc_events'] : [];
    $out = [];
    foreach ($events as $id => $e) {
        if (!is_array($e)) {
            continue;
        }
        if (($e['status'] ?? 'published') === 'published') {
            $out[(int) $id] = $e;
        }
    }

    return $out;
}

function tc_event_count_for_current_organizer(): int
{
    if (tc_role() !== 'organizer') {
        return 0;
    }
    $n = 0;
    foreach (tc_events() as $e) {
        if (tc_event_manageable_by_current_organizer(is_array($e) ? $e : null)) {
            $n++;
        }
    }

    return $n;
}

function tc_find_event(int $eventId): ?array
{
    if (tc_db_entities_available()) {
        try {
            return tc_db_event_find($eventId);
        } catch (\Throwable $e) {
        }
    }
    $events = tc_events();

    return $events[$eventId] ?? null;
}

function tc_save_event(array $event): int
{
    tc_seed_data();
    if (tc_db_entities_available()) {
        try {
            return tc_db_event_save($event);
        } catch (\Throwable $e) {
        }
    }
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
    if (tc_db_entities_available()) {
        try {
            tc_db_event_delete($eventId);

            return;
        } catch (\Throwable $e) {
        }
    }
    unset($_SESSION['tc_events'][$eventId]);
    foreach ($_SESSION['tc_join_requests'] ?? [] as $id => $request) {
        if ((int) $request['event_id'] === $eventId) {
            unset($_SESSION['tc_join_requests'][$id]);
        }
    }
    foreach ($_SESSION['tc_updates'] ?? [] as $id => $update) {
        if ((int) $update['event_id'] === $eventId) {
            unset($_SESSION['tc_updates'][$id]);
        }
    }
}

function tc_join_requests(): array
{
    tc_seed_data();
    if (tc_db_entities_available()) {
        try {
            return tc_db_join_requests_fetch_all_assoc();
        } catch (\Throwable $e) {
        }
    }

    return isset($_SESSION['tc_join_requests']) && is_array($_SESSION['tc_join_requests']) ? $_SESSION['tc_join_requests'] : [];
}

function tc_save_join_request(array $request): int
{
    tc_seed_data();
    $uid = tc_current_user_id();
    if ($uid > 0 && !isset($request['user_id'])) {
        $request['user_id'] = $uid;
    }
    if (tc_db_entities_available()) {
        try {
            return tc_db_join_request_save($request);
        } catch (\Throwable $e) {
        }
    }
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
    if (tc_db_entities_available()) {
        try {
            tc_db_join_request_delete($requestId);

            return;
        } catch (\Throwable $e) {
        }
    }
    unset($_SESSION['tc_join_requests'][$requestId]);
}

function tc_updates(): array
{
    tc_seed_data();
    if (tc_db_entities_available()) {
        try {
            return tc_db_updates_fetch_all_assoc();
        } catch (\Throwable $e) {
        }
    }

    return isset($_SESSION['tc_updates']) && is_array($_SESSION['tc_updates']) ? $_SESSION['tc_updates'] : [];
}

function tc_save_update(array $update): int
{
    tc_seed_data();
    if (tc_db_entities_available()) {
        try {
            return tc_db_update_save($update);
        } catch (\Throwable $e) {
        }
    }
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
    if (tc_db_entities_available()) {
        try {
            tc_db_update_delete($updateId);

            return;
        } catch (\Throwable $e) {
        }
    }
    unset($_SESSION['tc_updates'][$updateId]);
}

function tc_reviews(): array
{
    tc_seed_data();
    if (tc_db_entities_available()) {
        try {
            return tc_db_reviews_fetch_all_assoc();
        } catch (\Throwable $e) {
        }
    }

    return isset($_SESSION['tc_reviews']) && is_array($_SESSION['tc_reviews']) ? $_SESSION['tc_reviews'] : [];
}

function tc_save_review(array $review): int
{
    tc_seed_data();
    if (tc_db_entities_available()) {
        try {
            return tc_db_review_save($review);
        } catch (\Throwable $e) {
        }
    }
    $id = isset($review['id']) ? (int) $review['id'] : 0;
    if ($id <= 0) {
        $id = empty($_SESSION['tc_reviews']) ? 1 : (max(array_keys($_SESSION['tc_reviews'])) + 1);
    }
    $review['id'] = $id;
    $_SESSION['tc_reviews'][$id] = $review;

    return $id;
}

function tc_delete_review(int $reviewId): void
{
    tc_seed_data();
    if (tc_db_entities_available()) {
        try {
            tc_db_review_delete($reviewId);

            return;
        } catch (\Throwable $e) {
        }
    }
    unset($_SESSION['tc_reviews'][$reviewId]);
}

/** Queue a dashboard notice for a specific account (event owner, joining hiker, etc.). */
function tc_push_notice_for_user(int $userId, string $message, string $type = 'info'): void
{
    tc_seed_data();
    if ($userId <= 0) {
        return;
    }
    $msg = trim($message);
    if ($msg === '') {
        return;
    }
    $t = in_array($type, ['success', 'warning', 'info'], true) ? $type : 'info';
    if (tc_db_entities_available()) {
        try {
            tc_db_user_notice_push($userId, $msg, $t);

            return;
        } catch (\Throwable $e) {
        }
    }
    if (!isset($_SESSION['tc_notices']['users']) || !is_array($_SESSION['tc_notices']['users'])) {
        $_SESSION['tc_notices']['users'] = [];
    }
    if (!isset($_SESSION['tc_notices']['users'][$userId])) {
        $_SESSION['tc_notices']['users'][$userId] = [];
    }
    $_SESSION['tc_notices']['users'][$userId][] = [
        'message' => $msg,
        'type' => $t,
        'created_at' => date('Y-m-d H:i:s'),
    ];
}

/**
 * Notices for the logged-in user only. Falls back to legacy role buckets when not logged in (demo switcher).
 */
function tc_pull_notices_for_current_user(): array
{
    tc_seed_data();
    $uid = tc_current_user_id();
    if ($uid > 0) {
        if (tc_db_entities_available()) {
            try {
                return tc_db_user_notices_pull_and_clear($uid);
            } catch (\Throwable $e) {
            }
        }
        if (!isset($_SESSION['tc_notices']['users']) || !is_array($_SESSION['tc_notices']['users'])) {
            return [];
        }
        $notices = $_SESSION['tc_notices']['users'][$uid] ?? [];
        $_SESSION['tc_notices']['users'][$uid] = [];

        return is_array($notices) ? $notices : [];
    }
    $roleKey = tc_role() === 'organizer' ? 'organizer' : 'hiker';
    $notices = $_SESSION['tc_notices'][$roleKey] ?? [];
    if (!is_array($notices)) {
        $notices = [];
    }
    $_SESSION['tc_notices'][$roleKey] = [];

    return $notices;
}

function tc_two_factor_library_ready(): bool
{
    if (class_exists(\PragmaRX\Google2FAQRCode\Google2FA::class)) {
        return true;
    }
    $autoload = dirname(__DIR__, 2) . '/vendor/autoload.php';
    if (is_file($autoload)) {
        require_once $autoload;
    }

    return class_exists(\PragmaRX\Google2FAQRCode\Google2FA::class);
}

function tc_two_factor_client(): ?\PragmaRX\Google2FAQRCode\Google2FA
{
    if (!tc_two_factor_library_ready()) {
        return null;
    }

    return new \PragmaRX\Google2FAQRCode\Google2FA();
}

function tc_two_factor_status(string $role): array
{
    tc_refresh_profile_from_db();
    $roleKey = $role === 'organizer' ? 'organizer' : 'hiker';
    $profile = $_SESSION['tc_profiles'][$roleKey] ?? tc_profile_defaults($roleKey);

    return [
        'enabled' => !empty($profile['two_factor_enabled']) && (string) ($profile['two_factor_secret'] ?? '') !== '',
        'secret' => (string) ($profile['two_factor_secret'] ?? ''),
        'temp_secret' => (string) ($profile['two_factor_temp_secret'] ?? ''),
    ];
}

function tc_two_factor_begin_setup(string $role): string
{
    tc_seed_data();
    tc_refresh_profile_from_db();
    $google2fa = tc_two_factor_client();
    if (!$google2fa) {
        return '';
    }
    $roleKey = $role === 'organizer' ? 'organizer' : 'hiker';
    $profile = tc_profile_defaults($roleKey);
    $existing = $_SESSION['tc_profiles'][$roleKey] ?? [];
    if (is_array($existing)) {
        $profile = array_merge($profile, $existing);
    }
    $profile['two_factor_temp_secret'] = (string) $google2fa->generateSecretKey();
    $_SESSION['tc_profiles'][$roleKey] = $profile;
    if (tc_current_user_id() > 0 && $roleKey === tc_role()) {
        try {
            tc_db_update_user_profile(tc_current_user_id(), [
                'two_factor_temp_secret' => (string) $profile['two_factor_temp_secret'],
            ]);
        } catch (\Throwable $e) {
        }
    }

    return (string) $profile['two_factor_temp_secret'];
}

function tc_two_factor_enable(string $role, string $code): bool
{
    tc_seed_data();
    $google2fa = tc_two_factor_client();
    if (!$google2fa) {
        return false;
    }
    $roleKey = $role === 'organizer' ? 'organizer' : 'hiker';
    $profile = $_SESSION['tc_profiles'][$roleKey] ?? tc_profile_defaults($roleKey);
    if (!is_array($profile)) {
        $profile = tc_profile_defaults($roleKey);
    }
    $tempSecret = (string) ($profile['two_factor_temp_secret'] ?? '');
    if ($tempSecret === '' || !$google2fa->verifyKey($tempSecret, $code, 1)) {
        return false;
    }
    $profile['two_factor_enabled'] = true;
    $profile['two_factor_secret'] = $tempSecret;
    $profile['two_factor_temp_secret'] = '';
    $_SESSION['tc_profiles'][$roleKey] = $profile;
    if (tc_current_user_id() > 0 && $roleKey === tc_role()) {
        try {
            tc_db_update_user_profile(tc_current_user_id(), [
                'two_factor_enabled' => 1,
                'two_factor_secret' => $tempSecret,
                'two_factor_temp_secret' => null,
            ]);
        } catch (\Throwable $e) {
        }
    }

    return true;
}

function tc_two_factor_disable(string $role): void
{
    tc_seed_data();
    $roleKey = $role === 'organizer' ? 'organizer' : 'hiker';
    $profile = $_SESSION['tc_profiles'][$roleKey] ?? tc_profile_defaults($roleKey);
    if (!is_array($profile)) {
        $profile = tc_profile_defaults($roleKey);
    }
    $profile['two_factor_enabled'] = false;
    $profile['two_factor_secret'] = '';
    $profile['two_factor_temp_secret'] = '';
    $_SESSION['tc_profiles'][$roleKey] = $profile;
    if (tc_current_user_id() > 0 && $roleKey === tc_role()) {
        try {
            tc_db_update_user_profile(tc_current_user_id(), [
                'two_factor_enabled' => 0,
                'two_factor_secret' => null,
                'two_factor_temp_secret' => null,
            ]);
        } catch (\Throwable $e) {
        }
    }
}

function tc_two_factor_verify_code(string $secret, string $code): bool
{
    $google2fa = tc_two_factor_client();
    if (!$google2fa || $secret === '') {
        return false;
    }
    $clean = preg_replace('/\D+/', '', $code) ?? '';
    if (strlen($clean) !== 6) {
        return false;
    }

    return $google2fa->verifyKey($secret, $clean, 4);
}

function tc_two_factor_qr_inline(string $secret, string $role): string
{
    $google2fa = tc_two_factor_client();
    if (!$google2fa || $secret === '') {
        return '';
    }
    try {
        $qr = (string) $google2fa->getQRCodeInline(
            'TrailConnect',
            ucfirst($role) . '@trailconnect.local',
            $secret
        );
    } catch (\Throwable $e) {
        return '';
    }
    if ($qr === '') {
        return '';
    }
    if (strpos($qr, '<svg') !== false) {
        return 'data:image/svg+xml;base64,' . base64_encode($qr);
    }

    return $qr;
}

function tc_two_factor_start_login(string $role, string $email): void
{
    $_SESSION['tc_2fa_pending'] = [
        'role' => $role === 'organizer' ? 'organizer' : 'hiker',
        'email' => trim($email),
        'created_at' => time(),
    ];
}

function tc_two_factor_pending_context(): ?array
{
    $pending = $_SESSION['tc_2fa_pending'] ?? null;
    if (!is_array($pending)) {
        return null;
    }
    if (!isset($pending['created_at']) || (time() - (int) $pending['created_at']) > 600) {
        unset($_SESSION['tc_2fa_pending']);

        return null;
    }

    return $pending;
}

function tc_two_factor_clear_login(): void
{
    unset($_SESSION['tc_2fa_pending']);
}

function tc_login_rate_limit_key(string $email): string
{
    return strtolower(trim($email));
}

function tc_login_rate_limit_status(string $email): array
{
    $key = tc_login_rate_limit_key($email);
    if (!isset($_SESSION['tc_login_attempts']) || !is_array($_SESSION['tc_login_attempts'])) {
        $_SESSION['tc_login_attempts'] = [];
    }
    $row = $_SESSION['tc_login_attempts'][$key] ?? ['count' => 0, 'blocked_until' => 0];
    $blockedUntil = (int) ($row['blocked_until'] ?? 0);
    $remaining = max(0, $blockedUntil - time());

    return [
        'blocked' => $remaining > 0,
        'remaining_seconds' => $remaining,
        'count' => (int) ($row['count'] ?? 0),
    ];
}

function tc_login_rate_limit_register_failure(string $email): void
{
    $key = tc_login_rate_limit_key($email);
    if (!isset($_SESSION['tc_login_attempts']) || !is_array($_SESSION['tc_login_attempts'])) {
        $_SESSION['tc_login_attempts'] = [];
    }
    $row = $_SESSION['tc_login_attempts'][$key] ?? ['count' => 0, 'blocked_until' => 0];
    $row['count'] = (int) ($row['count'] ?? 0) + 1;
    if ($row['count'] >= 5) {
        $row['blocked_until'] = time() + 300;
        $row['count'] = 0;
    }
    $_SESSION['tc_login_attempts'][$key] = $row;
}

function tc_login_rate_limit_reset(string $email): void
{
    $key = tc_login_rate_limit_key($email);
    if (isset($_SESSION['tc_login_attempts'][$key])) {
        unset($_SESSION['tc_login_attempts'][$key]);
    }
}

function tc_is_admin_user(): bool
{
    $email = strtolower(trim(tc_current_user_email()));
    $allow = $_ENV['TC_ADMIN_EMAIL'] ?? 'admin@trailconnect.local';

    return $email !== '' && $email === strtolower(trim((string) $allow));
}

require_once __DIR__ . '/trail_images.php';
