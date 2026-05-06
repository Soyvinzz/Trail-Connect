<?php
declare(strict_types=1);

function tc_db(): \PDO
{
    static $pdo = null;
    if ($pdo instanceof \PDO) {
        return $pdo;
    }

    $host = $_ENV['TC_DB_HOST'] ?? '127.0.0.1';
    $port = (int) ($_ENV['TC_DB_PORT'] ?? 3306);
    $name = $_ENV['TC_DB_NAME'] ?? 'trailconnect';
    $user = $_ENV['TC_DB_USER'] ?? 'root';
    $pass = $_ENV['TC_DB_PASS'] ?? '';
    $dsn = 'mysql:host=' . $host . ';port=' . $port . ';dbname=' . $name . ';charset=utf8mb4';
    $pdo = new \PDO($dsn, $user, $pass, [
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
    ]);

    return $pdo;
}

function tc_db_migrate(): void
{
    $db = tc_db();
    $db->exec(
        "CREATE TABLE IF NOT EXISTS users (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            full_name VARCHAR(120) NOT NULL,
            email VARCHAR(190) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            role ENUM('hiker','organizer') NOT NULL DEFAULT 'hiker',
            is_verified TINYINT(1) NOT NULL DEFAULT 0,
            email_verified_at DATETIME NULL,
            is_disabled TINYINT(1) NOT NULL DEFAULT 0,
            phone_number VARCHAR(40) NULL,
            current_address VARCHAR(255) NULL,
            hiking_level ENUM('beginner','minor','intermediate','advanced') NULL,
            minor_hikes_completed SMALLINT UNSIGNED NULL,
            major_hikes_completed SMALLINT UNSIGNED NULL,
            emergency_contact_name VARCHAR(120) NULL,
            emergency_contact_number VARCHAR(40) NULL,
            medical_notes TEXT NULL,
            bio TEXT NULL,
            home VARCHAR(190) NULL,
            avatar_path VARCHAR(255) NULL,
            two_factor_enabled TINYINT(1) NOT NULL DEFAULT 0,
            two_factor_secret VARCHAR(64) NULL,
            two_factor_temp_secret VARCHAR(64) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );
    $db->exec(
        "CREATE TABLE IF NOT EXISTS email_verifications (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id INT UNSIGNED NOT NULL,
            token_hash VARCHAR(255) NOT NULL,
            expires_at DATETIME NOT NULL,
            used_at DATETIME NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_ev_user (user_id),
            INDEX idx_ev_token (token_hash),
            CONSTRAINT fk_ev_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );
    $db->exec(
        "CREATE TABLE IF NOT EXISTS password_resets (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id INT UNSIGNED NOT NULL,
            token_hash VARCHAR(255) NOT NULL,
            expires_at DATETIME NOT NULL,
            used_at DATETIME NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_pr_user (user_id),
            INDEX idx_pr_token (token_hash),
            CONSTRAINT fk_pr_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );
    $db->exec(
        "CREATE TABLE IF NOT EXISTS events (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            trail VARCHAR(380) NOT NULL,
            hike_date DATE NULL,
            hike_time TIME NULL,
            difficulty ENUM('easy','mod','hard','vhard') NOT NULL DEFAULT 'mod',
            min_hiking_level ENUM('beginner','minor','intermediate','advanced') NULL,
            min_minor_hikes SMALLINT UNSIGNED NOT NULL DEFAULT 0,
            min_major_hikes SMALLINT UNSIGNED NOT NULL DEFAULT 0,
            meet_place VARCHAR(255) NOT NULL DEFAULT '',
            max_slots SMALLINT UNSIGNED NOT NULL DEFAULT 12,
            description TEXT NULL,
            approval ENUM('auto','manual') NOT NULL DEFAULT 'manual',
            organizer_name VARCHAR(120) NOT NULL DEFAULT 'TrailConnect Organizer',
            publish_status ENUM('published','draft') NOT NULL DEFAULT 'published',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );
    $db->exec(
        "CREATE TABLE IF NOT EXISTS join_requests (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            event_id INT UNSIGNED NOT NULL,
            hiker_name VARCHAR(120) NOT NULL,
            user_id INT UNSIGNED NULL,
            status ENUM('pending','approved','declined') NOT NULL DEFAULT 'pending',
            requested_at DATETIME NOT NULL,
            INDEX idx_join_event (event_id),
            INDEX idx_join_user_event (user_id, event_id),
            CONSTRAINT fk_join_event FOREIGN KEY (event_id) REFERENCES events (id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );
    $db->exec(
        "CREATE TABLE IF NOT EXISTS event_updates (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            event_id INT UNSIGNED NOT NULL,
            type_label VARCHAR(80) NOT NULL DEFAULT 'General',
            message TEXT NOT NULL,
            posted_at DATETIME NOT NULL,
            INDEX idx_updates_event (event_id),
            CONSTRAINT fk_updates_event FOREIGN KEY (event_id) REFERENCES events (id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );
    $db->exec(
        "CREATE TABLE IF NOT EXISTS trail_reviews (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            author_name VARCHAR(120) NOT NULL,
            recipient VARCHAR(255) NOT NULL,
            event_id INT UNSIGNED NOT NULL DEFAULT 0,
            stars TINYINT UNSIGNED NOT NULL DEFAULT 5,
            body TEXT NOT NULL,
            posted_at DATETIME NOT NULL,
            INDEX idx_reviews_event (event_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );
    $db->exec(
        "CREATE TABLE IF NOT EXISTS user_notices (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id INT UNSIGNED NOT NULL,
            message VARCHAR(512) NOT NULL,
            notice_type VARCHAR(16) NOT NULL DEFAULT 'info',
            created_at DATETIME NOT NULL,
            INDEX idx_un_user (user_id),
            CONSTRAINT fk_un_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );
    $col = tc_db()->query("SHOW COLUMNS FROM events LIKE 'organizer_user_id'")->fetch();
    if (!$col) {
        tc_db()->exec(
            'ALTER TABLE events ADD COLUMN organizer_user_id INT UNSIGNED NULL DEFAULT NULL AFTER organizer_name'
        );
    }
    $userDisabledCol = tc_db()->query("SHOW COLUMNS FROM users LIKE 'is_disabled'")->fetch();
    $userVerifiedCol = tc_db()->query("SHOW COLUMNS FROM users LIKE 'is_verified'")->fetch();
    if (!$userVerifiedCol) {
        tc_db()->exec('ALTER TABLE users ADD COLUMN is_verified TINYINT(1) NOT NULL DEFAULT 0 AFTER role');
    }
    $userVerifiedAtCol = tc_db()->query("SHOW COLUMNS FROM users LIKE 'email_verified_at'")->fetch();
    if (!$userVerifiedAtCol) {
        tc_db()->exec('ALTER TABLE users ADD COLUMN email_verified_at DATETIME NULL AFTER is_verified');
    }
    $userDisabledCol = tc_db()->query("SHOW COLUMNS FROM users LIKE 'is_disabled'")->fetch();
    if (!$userDisabledCol) {
        tc_db()->exec('ALTER TABLE users ADD COLUMN is_disabled TINYINT(1) NOT NULL DEFAULT 0 AFTER role');
    }
    $userPhoneCol = tc_db()->query("SHOW COLUMNS FROM users LIKE 'phone_number'")->fetch();
    if (!$userPhoneCol) {
        tc_db()->exec('ALTER TABLE users ADD COLUMN phone_number VARCHAR(40) NULL AFTER role');
    }
    $userAddressCol = tc_db()->query("SHOW COLUMNS FROM users LIKE 'current_address'")->fetch();
    if (!$userAddressCol) {
        tc_db()->exec('ALTER TABLE users ADD COLUMN current_address VARCHAR(255) NULL AFTER phone_number');
    }
    $userLevelCol = tc_db()->query("SHOW COLUMNS FROM users LIKE 'hiking_level'")->fetch();
    if (!$userLevelCol) {
        tc_db()->exec(
            "ALTER TABLE users ADD COLUMN hiking_level ENUM('beginner','minor','intermediate','advanced') NULL AFTER current_address"
        );
    }
    $userMinorCountCol = tc_db()->query("SHOW COLUMNS FROM users LIKE 'minor_hikes_completed'")->fetch();
    if (!$userMinorCountCol) {
        tc_db()->exec('ALTER TABLE users ADD COLUMN minor_hikes_completed SMALLINT UNSIGNED NULL AFTER hiking_level');
    }
    $userMajorCountCol = tc_db()->query("SHOW COLUMNS FROM users LIKE 'major_hikes_completed'")->fetch();
    if (!$userMajorCountCol) {
        tc_db()->exec('ALTER TABLE users ADD COLUMN major_hikes_completed SMALLINT UNSIGNED NULL AFTER minor_hikes_completed');
    }
    $userEmergencyNameCol = tc_db()->query("SHOW COLUMNS FROM users LIKE 'emergency_contact_name'")->fetch();
    if (!$userEmergencyNameCol) {
        tc_db()->exec('ALTER TABLE users ADD COLUMN emergency_contact_name VARCHAR(120) NULL AFTER major_hikes_completed');
    }
    $userEmergencyNumberCol = tc_db()->query("SHOW COLUMNS FROM users LIKE 'emergency_contact_number'")->fetch();
    if (!$userEmergencyNumberCol) {
        tc_db()->exec('ALTER TABLE users ADD COLUMN emergency_contact_number VARCHAR(40) NULL AFTER emergency_contact_name');
    }
    $userMedicalNotesCol = tc_db()->query("SHOW COLUMNS FROM users LIKE 'medical_notes'")->fetch();
    if (!$userMedicalNotesCol) {
        tc_db()->exec('ALTER TABLE users ADD COLUMN medical_notes TEXT NULL AFTER emergency_contact_number');
    }
    $eventMinLevelCol = tc_db()->query("SHOW COLUMNS FROM events LIKE 'min_hiking_level'")->fetch();
    if (!$eventMinLevelCol) {
        tc_db()->exec(
            "ALTER TABLE events ADD COLUMN min_hiking_level ENUM('beginner','minor','intermediate','advanced') NULL AFTER difficulty"
        );
    }
    $eventMinMinorCol = tc_db()->query("SHOW COLUMNS FROM events LIKE 'min_minor_hikes'")->fetch();
    if (!$eventMinMinorCol) {
        tc_db()->exec('ALTER TABLE events ADD COLUMN min_minor_hikes SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER min_hiking_level');
    }
    $eventMinMajorCol = tc_db()->query("SHOW COLUMNS FROM events LIKE 'min_major_hikes'")->fetch();
    if (!$eventMinMajorCol) {
        tc_db()->exec('ALTER TABLE events ADD COLUMN min_major_hikes SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER min_minor_hikes');
    }
}

function tc_db_entities_available(): bool
{
    static $ok = null;
    if ($ok !== null) {
        return $ok;
    }
    try {
        tc_db_migrate();
        $ok = true;
    } catch (\Throwable $e) {
        $ok = false;
    }

    return $ok;
}

function tc_db_events_row_normalize(array $row): array
{
    return [
        'id' => (int) $row['id'],
        'title' => (string) $row['title'],
        'trail' => (string) $row['trail'],
        'date' => (string) ($row['hike_date'] ?? ''),
        'time' => substr((string) ($row['hike_time'] ?? ''), 0, 5),
        'difficulty' => (string) ($row['difficulty'] ?? 'mod'),
        'min_hiking_level' => (string) ($row['min_hiking_level'] ?? ''),
        'min_minor_hikes' => (int) ($row['min_minor_hikes'] ?? 0),
        'min_major_hikes' => (int) ($row['min_major_hikes'] ?? 0),
        'meet' => (string) ($row['meet_place'] ?? ''),
        'max' => (int) ($row['max_slots'] ?? 12),
        'desc' => (string) ($row['description'] ?? ''),
        'approval' => (string) ($row['approval'] ?? 'manual'),
        'organizer' => (string) ($row['organizer_name'] ?? ''),
        'organizer_user_id' => isset($row['organizer_user_id']) && $row['organizer_user_id'] !== null
            ? (int) $row['organizer_user_id'] : null,
        'status' => (string) ($row['publish_status'] ?? 'published'),
    ];
}

function tc_db_events_fetch_all_assoc(): array
{
    $stmt = tc_db()->query('SELECT * FROM events ORDER BY id ASC');
    $rows = $stmt->fetchAll();
    $out = [];
    foreach ($rows as $row) {
        $n = tc_db_events_row_normalize($row);
        $out[$n['id']] = $n;
    }

    return $out;
}

/** Published hikes only — used for joiner discovery (Find hikes, reviews, etc.). */
function tc_db_events_fetch_published_assoc(): array
{
    $stmt = tc_db()->query("SELECT * FROM events WHERE publish_status = 'published' ORDER BY id ASC");
    $rows = $stmt->fetchAll();
    $out = [];
    foreach ($rows as $row) {
        $n = tc_db_events_row_normalize($row);
        $out[$n['id']] = $n;
    }

    return $out;
}

function tc_db_event_find(int $id): ?array
{
    $stmt = tc_db()->prepare('SELECT * FROM events WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $id]);
    $row = $stmt->fetch();
    if (!is_array($row)) {
        return null;
    }

    return tc_db_events_row_normalize($row);
}

function tc_db_event_save(array $event): int
{
    $id = isset($event['id']) ? (int) $event['id'] : 0;
    $cols = [
        'title' => trim((string) ($event['title'] ?? '')),
        'trail' => trim((string) ($event['trail'] ?? '')),
        'hike_date' => trim((string) ($event['date'] ?? '')) ?: null,
        'hike_time' => trim((string) ($event['time'] ?? '08:00')) ?: '08:00:00',
        'difficulty' => in_array((string) ($event['difficulty'] ?? ''), ['easy', 'mod', 'hard', 'vhard'], true)
            ? (string) $event['difficulty'] : 'mod',
        'min_hiking_level' => in_array((string) ($event['min_hiking_level'] ?? ''), ['beginner', 'minor', 'intermediate', 'advanced'], true)
            ? (string) $event['min_hiking_level'] : null,
        'min_minor_hikes' => max(0, min(1000, (int) ($event['min_minor_hikes'] ?? 0))),
        'min_major_hikes' => max(0, min(1000, (int) ($event['min_major_hikes'] ?? 0))),
        'meet_place' => trim((string) ($event['meet'] ?? '')),
        'max_slots' => max(1, min(500, (int) ($event['max'] ?? 12))),
        'description' => trim((string) ($event['desc'] ?? '')),
        'approval' => ((string) ($event['approval'] ?? 'manual')) === 'auto' ? 'auto' : 'manual',
        'organizer_name' => trim((string) ($event['organizer'] ?? 'TrailConnect Organizer')),
        'organizer_user_id' => array_key_exists('organizer_user_id', $event) && $event['organizer_user_id'] !== null && $event['organizer_user_id'] !== ''
            ? max(1, (int) $event['organizer_user_id']) : null,
        'publish_status' => ((string) ($event['status'] ?? 'published')) === 'draft' ? 'draft' : 'published',
    ];
    if (strlen((string) $cols['hike_time']) <= 5) {
        $cols['hike_time'] .= ':00';
    }

    if ($id > 0) {
        $stmt = tc_db()->prepare(
            'UPDATE events SET title=:title, trail=:trail, hike_date=:hike_date, hike_time=:hike_time,
            difficulty=:difficulty, min_hiking_level=:min_hiking_level, min_minor_hikes=:min_minor_hikes, min_major_hikes=:min_major_hikes, meet_place=:meet_place, max_slots=:max_slots, description=:description,
            approval=:approval, organizer_name=:organizer_name, organizer_user_id=:organizer_user_id, publish_status=:publish_status WHERE id=:id'
        );
        $stmt->execute(array_merge($cols, ['id' => $id]));

        return $id;
    }
    $stmt = tc_db()->prepare(
        'INSERT INTO events (title, trail, hike_date, hike_time, difficulty, min_hiking_level, min_minor_hikes, min_major_hikes, meet_place, max_slots, description, approval, organizer_name, organizer_user_id, publish_status)
        VALUES (:title, :trail, :hike_date, :hike_time, :difficulty, :min_hiking_level, :min_minor_hikes, :min_major_hikes, :meet_place, :max_slots, :description, :approval, :organizer_name, :organizer_user_id, :publish_status)'
    );
    $stmt->execute($cols);

    return (int) tc_db()->lastInsertId();
}

function tc_db_event_delete(int $eventId): void
{
    $stmt = tc_db()->prepare('DELETE FROM events WHERE id = :id');
    $stmt->execute(['id' => $eventId]);
}

function tc_db_join_row_normalize(array $row): array
{
    return [
        'id' => (int) $row['id'],
        'event_id' => (int) $row['event_id'],
        'hiker_name' => (string) ($row['hiker_name'] ?? ''),
        'user_id' => isset($row['user_id']) && $row['user_id'] !== null ? (int) $row['user_id'] : null,
        'status' => (string) ($row['status'] ?? 'pending'),
        'requested_at' => (string) ($row['requested_at'] ?? ''),
    ];
}

function tc_db_join_requests_fetch_all_assoc(): array
{
    $stmt = tc_db()->query('SELECT id, event_id, hiker_name, user_id, status, requested_at FROM join_requests ORDER BY id ASC');
    $rows = $stmt->fetchAll();
    $out = [];
    foreach ($rows as $row) {
        $n = tc_db_join_row_normalize($row);
        $out[$n['id']] = $n;
    }

    return $out;
}

function tc_db_join_request_save(array $request): int
{
    $id = isset($request['id']) ? (int) $request['id'] : 0;
    $cols = [
        'event_id' => (int) ($request['event_id'] ?? 0),
        'hiker_name' => trim((string) ($request['hiker_name'] ?? '')),
        'user_id' => isset($request['user_id']) && $request['user_id'] !== '' ? (int) $request['user_id'] : null,
        'status' => in_array((string) ($request['status'] ?? ''), ['pending', 'approved', 'declined'], true)
            ? (string) $request['status'] : 'pending',
        'requested_at' => trim((string) ($request['requested_at'] ?? date('Y-m-d H:i:s'))),
    ];
    if ($id > 0) {
        $stmt = tc_db()->prepare(
            'UPDATE join_requests SET event_id=:event_id, hiker_name=:hiker_name, user_id=:user_id, status=:status, requested_at=:requested_at WHERE id=:id'
        );
        $stmt->execute(array_merge($cols, ['id' => $id]));

        return $id;
    }
    $stmt = tc_db()->prepare(
        'INSERT INTO join_requests (event_id, hiker_name, user_id, status, requested_at) VALUES (:event_id, :hiker_name, :user_id, :status, :requested_at)'
    );
    $stmt->execute($cols);

    return (int) tc_db()->lastInsertId();
}

function tc_db_join_request_delete(int $requestId): void
{
    $stmt = tc_db()->prepare('DELETE FROM join_requests WHERE id = :id');
    $stmt->execute(['id' => $requestId]);
}

function tc_db_user_notice_push(int $userId, string $message, string $type = 'info'): void
{
    if ($userId <= 0) {
        return;
    }
    $t = in_array($type, ['success', 'warning', 'info'], true) ? $type : 'info';
    $msg = substr(trim($message), 0, 512);
    if ($msg === '') {
        return;
    }
    $stmt = tc_db()->prepare(
        'INSERT INTO user_notices (user_id, message, notice_type, created_at) VALUES (:uid, :msg, :ntype, NOW())'
    );
    $stmt->execute(['uid' => $userId, 'msg' => $msg, 'ntype' => $t]);
}

/** Returns queued notices for this user and removes them (one-shot, matches session behavior). */
function tc_db_user_notices_pull_and_clear(int $userId): array
{
    if ($userId <= 0) {
        return [];
    }
    $stmt = tc_db()->prepare(
        'SELECT id, message, notice_type, created_at FROM user_notices WHERE user_id = :uid ORDER BY id ASC'
    );
    $stmt->execute(['uid' => $userId]);
    $rows = $stmt->fetchAll();
    if (!is_array($rows) || $rows === []) {
        return [];
    }
    $out = [];
    $ids = [];
    foreach ($rows as $row) {
        if (!is_array($row)) {
            continue;
        }
        $ids[] = (int) ($row['id'] ?? 0);
        $nt = (string) ($row['notice_type'] ?? 'info');
        if (!in_array($nt, ['success', 'warning', 'info'], true)) {
            $nt = 'info';
        }
        $out[] = [
            'message' => (string) ($row['message'] ?? ''),
            'type' => $nt,
            'created_at' => (string) ($row['created_at'] ?? ''),
        ];
    }
    $ids = array_values(array_filter($ids, static fn (int $id): bool => $id > 0));
    if ($ids !== []) {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $del = tc_db()->prepare("DELETE FROM user_notices WHERE id IN ($placeholders)");
        $del->execute($ids);
    }

    return $out;
}

function tc_db_update_row_normalize(array $row): array
{
    return [
        'id' => (int) $row['id'],
        'event_id' => (int) $row['event_id'],
        'type' => (string) ($row['type_label'] ?? ''),
        'message' => (string) ($row['message'] ?? ''),
        'posted_at' => (string) ($row['posted_at'] ?? ''),
    ];
}

function tc_db_updates_fetch_all_assoc(): array
{
    $stmt = tc_db()->query('SELECT id, event_id, type_label, message, posted_at FROM event_updates ORDER BY id ASC');
    $rows = $stmt->fetchAll();
    $out = [];
    foreach ($rows as $row) {
        $n = tc_db_update_row_normalize($row);
        $out[$n['id']] = $n;
    }

    return $out;
}

function tc_db_update_save(array $update): int
{
    $id = isset($update['id']) ? (int) $update['id'] : 0;
    $cols = [
        'event_id' => (int) ($update['event_id'] ?? 0),
        'type_label' => trim((string) ($update['type'] ?? 'General')),
        'message' => trim((string) ($update['message'] ?? '')),
        'posted_at' => trim((string) ($update['posted_at'] ?? date('Y-m-d H:i:s'))),
    ];
    if ($id > 0) {
        $stmt = tc_db()->prepare(
            'UPDATE event_updates SET event_id=:event_id, type_label=:type_label, message=:message, posted_at=:posted_at WHERE id=:id'
        );
        $stmt->execute(array_merge($cols, ['id' => $id]));

        return $id;
    }
    $stmt = tc_db()->prepare(
        'INSERT INTO event_updates (event_id, type_label, message, posted_at) VALUES (:event_id, :type_label, :message, :posted_at)'
    );
    $stmt->execute($cols);

    return (int) tc_db()->lastInsertId();
}

function tc_db_update_delete(int $updateId): void
{
    $stmt = tc_db()->prepare('DELETE FROM event_updates WHERE id = :id');
    $stmt->execute(['id' => $updateId]);
}

function tc_db_review_row_normalize(array $row): array
{
    return [
        'id' => (int) $row['id'],
        'author_name' => (string) ($row['author_name'] ?? ''),
        'recipient' => (string) ($row['recipient'] ?? ''),
        'event_id' => (int) ($row['event_id'] ?? 0),
        'stars' => (int) ($row['stars'] ?? 5),
        'text' => (string) ($row['body'] ?? ''),
        'posted_at' => (string) ($row['posted_at'] ?? ''),
    ];
}

function tc_db_reviews_fetch_all_assoc(): array
{
    $stmt = tc_db()->query('SELECT id, author_name, recipient, event_id, stars, body, posted_at FROM trail_reviews ORDER BY id ASC');
    $rows = $stmt->fetchAll();
    $out = [];
    foreach ($rows as $row) {
        $n = tc_db_review_row_normalize($row);
        $out[$n['id']] = $n;
    }

    return $out;
}

function tc_db_review_save(array $review): int
{
    $id = isset($review['id']) ? (int) $review['id'] : 0;
    $cols = [
        'author_name' => trim((string) ($review['author_name'] ?? '')),
        'recipient' => trim((string) ($review['recipient'] ?? '')),
        'event_id' => (int) ($review['event_id'] ?? 0),
        'stars' => max(1, min(5, (int) ($review['stars'] ?? 5))),
        'body' => trim((string) ($review['text'] ?? '')),
        'posted_at' => trim((string) ($review['posted_at'] ?? date('Y-m-d H:i:s'))),
    ];
    if ($id > 0) {
        $stmt = tc_db()->prepare(
            'UPDATE trail_reviews SET author_name=:author_name, recipient=:recipient, event_id=:event_id, stars=:stars, body=:body, posted_at=:posted_at WHERE id=:id'
        );
        $stmt->execute(array_merge($cols, ['id' => $id]));

        return $id;
    }
    $stmt = tc_db()->prepare(
        'INSERT INTO trail_reviews (author_name, recipient, event_id, stars, body, posted_at) VALUES (:author_name, :recipient, :event_id, :stars, :body, :posted_at)'
    );
    $stmt->execute($cols);

    return (int) tc_db()->lastInsertId();
}

function tc_db_review_delete(int $reviewId): void
{
    $stmt = tc_db()->prepare('DELETE FROM trail_reviews WHERE id = :id');
    $stmt->execute(['id' => $reviewId]);
}

/** Default demo passwords for presentation / local only */
function tc_demo_default_password(): string
{
    return 'TrailDemo1!';
}

/** Build fixed demo organisers + hikers when DB is enabled (safe to repeat). Keys: maya, rico, lian, kiko, nina */
function tc_db_ensure_demo_users(): array
{
    static $cache = null;
    if (is_array($cache)) {
        return $cache;
    }
    $specs = [
        'maya' => ['full_name' => 'Maya Fernandez', 'email' => 'maya@demo.trailconnect.local', 'password' => tc_demo_default_password(), 'role' => 'organizer'],
        'rico' => ['full_name' => 'Rico Santos', 'email' => 'rico@demo.trailconnect.local', 'password' => tc_demo_default_password(), 'role' => 'organizer'],
        'lian' => ['full_name' => 'Lian Cruz', 'email' => 'lian@demo.trailconnect.local', 'password' => tc_demo_default_password(), 'role' => 'hiker'],
        'kiko' => ['full_name' => 'Kiko Reyes', 'email' => 'kiko@demo.trailconnect.local', 'password' => tc_demo_default_password(), 'role' => 'hiker'],
        'nina' => ['full_name' => 'Nina Morales', 'email' => 'nina@demo.trailconnect.local', 'password' => tc_demo_default_password(), 'role' => 'hiker'],
    ];
    $out = [];
    foreach ($specs as $key => $s) {
        $row = tc_db_find_user_by_email($s['email']);
        if (!is_array($row)) {
            $row = tc_db_create_user($s['full_name'], $s['email'], $s['password'], $s['role']);
        }
        $out[$key] = $row;
    }
    $cache = $out;

    return $cache;
}

/** Full join demo set: organisers see pendings per event; hikers see pending/approved/declined */
function tc_db_demo_join_seed_rows(): array
{
    return [
        ['title' => 'Mt. Apo · Kapatagan–Kidapawan expedition', 'hiker' => 'lian', 'status' => 'pending', 'at' => '2026-04-20 09:12:00'],
        ['title' => 'Mt. Apo · Kapatagan–Kidapawan expedition', 'hiker' => 'kiko', 'status' => 'pending', 'at' => '2026-04-20 10:05:00'],
        ['title' => 'Mt. Dulang-Dulang summit push', 'hiker' => 'nina', 'status' => 'pending', 'at' => '2026-04-21 11:40:00'],
        ['title' => 'Mt. Dulang-Dulang summit push', 'hiker' => 'lian', 'status' => 'approved', 'at' => '2026-04-15 09:00:00'],
        ['title' => 'Mt. Pulag · Akiki–Ambangeg batch', 'hiker' => 'kiko', 'status' => 'declined', 'at' => '2026-04-17 08:05:00'],
        ['title' => 'Mt. Pulag · Akiki–Ambangeg batch', 'hiker' => 'nina', 'status' => 'pending', 'at' => '2026-04-22 14:18:00'],
        ['title' => 'Mt. Kitanglad ridge roster', 'hiker' => 'lian', 'status' => 'pending', 'at' => '2026-04-23 16:42:00'],
        ['title' => 'Mt. Kalatungan high camp climb', 'hiker' => 'kiko', 'status' => 'approved', 'at' => '2026-04-10 11:05:00'],
        ['title' => 'Mt. Tabayoc mossy ascent window', 'hiker' => 'nina', 'status' => 'pending', 'at' => '2026-04-24 08:58:00'],
        ['title' => 'Mt. Ragang technical volcano climb', 'hiker' => 'lian', 'status' => 'declined', 'at' => '2026-04-03 07:44:00'],
        ['title' => 'Mt. Halcon technical ascent', 'hiker' => 'kiko', 'status' => 'pending', 'at' => '2026-04-25 12:06:00'],
        ['title' => 'Mt. Mantalingajan expedition roster', 'hiker' => 'nina', 'status' => 'approved', 'at' => '2026-04-07 09:52:00'],
        ['title' => 'Mt. Guiting-Guiting · Knife-edge roster', 'hiker' => 'lian', 'status' => 'pending', 'at' => '2026-04-26 10:03:00'],
    ];
}

function tc_db_seed_join_requests_from_specs(array $byTitle, array $demoUsers): void
{
    foreach (tc_db_demo_join_seed_rows() as $seed) {
        $eid = $byTitle[strtolower(trim((string) ($seed['title'] ?? '')))] ?? 0;
        if ($eid <= 0 || !isset($seed['hiker'], $demoUsers[$seed['hiker']])) {
            continue;
        }
        $u = $demoUsers[$seed['hiker']];
        tc_db_join_request_save([
            'event_id' => $eid,
            'hiker_name' => (string) ($u['full_name'] ?? ''),
            'user_id' => (int) ($u['id'] ?? 0),
            'status' => in_array((string) ($seed['status'] ?? ''), ['pending', 'approved', 'declined'], true)
                ? (string) $seed['status'] : 'pending',
            'requested_at' => (string) ($seed['at'] ?? date('Y-m-d H:i:s')),
        ]);
    }
}

/** Seed default hikes and sample users / requests once */
function tc_db_seed_entities_if_needed(array $defaultEvents, array $defaultRequestSeeds, array $defaultUpdates, array $defaultReviews): void
{
    if (!tc_db_entities_available()) {
        return;
    }
    static $did = false;
    if ($did) {
        return;
    }
    $did = true;

    $demoUsers = tc_db_ensure_demo_users();
    $organizerCycle = [$demoUsers['maya'], $demoUsers['rico']];

    $eventCount = (int) tc_db()->query('SELECT COUNT(*) FROM events')->fetchColumn();
    $joinCount = (int) tc_db()->query('SELECT COUNT(*) FROM join_requests')->fetchColumn();

    if ($eventCount === 0) {
        $i = 0;
        foreach ($defaultEvents as $ev) {
            $owner = $organizerCycle[$i % 2];
            $ev['organizer'] = (string) ($owner['full_name'] ?? 'TrailConnect Organizer');
            $ev['organizer_user_id'] = (int) ($owner['id'] ?? 0);
            tc_db_event_save($ev + ['id' => 0]);
            $i++;
        }
        $byTitle = [];
        foreach (tc_db_events_fetch_all_assoc() as $e) {
            $byTitle[strtolower(trim($e['title']))] = $e['id'];
        }

        tc_db_seed_join_requests_from_specs($byTitle, $demoUsers);

        foreach ($defaultUpdates as $u) {
            $titleKey = strtolower(trim((string) ($u['_event_title'] ?? '')));
            $eid = $byTitle[$titleKey] ?? 0;
            if ($eid <= 0) {
                continue;
            }
            tc_db_update_save([
                'event_id' => $eid,
                'type' => (string) ($u['type'] ?? 'General'),
                'message' => (string) ($u['message'] ?? ''),
                'posted_at' => (string) ($u['posted_at'] ?? date('Y-m-d H:i:s')),
            ]);
        }
        foreach ($defaultReviews as $r) {
            $titleKey = strtolower(trim((string) ($r['_event_title'] ?? '')));
            $eid = $byTitle[$titleKey] ?? 0;
            tc_db_review_save([
                'author_name' => (string) ($r['author_name'] ?? ''),
                'recipient' => (string) ($r['recipient'] ?? ''),
                'event_id' => $eid > 0 ? $eid : 0,
                'stars' => (int) ($r['stars'] ?? 5),
                'text' => (string) ($r['text'] ?? ''),
                'posted_at' => (string) ($r['posted_at'] ?? date('Y-m-d H:i:s')),
            ]);
        }
    } elseif ($joinCount === 0 && $eventCount > 0) {
        $byTitle = [];
        foreach (tc_db_events_fetch_all_assoc() as $e) {
            $byTitle[strtolower(trim((string) $e['title']))] = $e['id'];
        }
        tc_db_seed_join_requests_from_specs($byTitle, $demoUsers);
    }
}

function tc_db_find_user_by_email(string $email): ?array
{
    $stmt = tc_db()->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
    $stmt->execute(['email' => strtolower(trim($email))]);
    $row = $stmt->fetch();

    return is_array($row) ? $row : null;
}

function tc_db_find_user_by_id(int $id): ?array
{
    $stmt = tc_db()->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $id]);
    $row = $stmt->fetch();

    return is_array($row) ? $row : null;
}

function tc_db_create_user(string $name, string $email, string $password, string $role, array $meta = []): array
{
    $cleanRole = $role === 'organizer' ? 'organizer' : 'hiker';
    $cleanEmail = strtolower(trim($email));
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $level = (string) ($meta['hiking_level'] ?? '');
    $allowedLevels = ['beginner', 'minor', 'intermediate', 'advanced'];
    if (!in_array($level, $allowedLevels, true)) {
        $level = '';
    }
    $minorHikesCompleted = null;
    if (array_key_exists('minor_hikes_completed', $meta) && $meta['minor_hikes_completed'] !== null && $meta['minor_hikes_completed'] !== '') {
        $minorHikesCompleted = max(0, (int) $meta['minor_hikes_completed']);
    }
    $majorHikesCompleted = null;
    if (array_key_exists('major_hikes_completed', $meta) && $meta['major_hikes_completed'] !== null && $meta['major_hikes_completed'] !== '') {
        $majorHikesCompleted = max(0, (int) $meta['major_hikes_completed']);
    }
    $bioNote = trim((string) ($meta['bio_note'] ?? ''));
    $phoneNumber = trim((string) ($meta['phone_number'] ?? ''));
    $currentAddress = trim((string) ($meta['current_address'] ?? ''));
    $defaultBio = $cleanRole === 'organizer'
        ? 'Organizer for major Philippines climbs with safety-first pacing.'
        : 'Weekend hiker exploring Luzon, Visayas, and Mindanao trails.';
    $hikerBio = $bioNote !== '' ? $bioNote : $defaultBio;
    if ($cleanRole === 'hiker' && $level !== '') {
        $levelLabel = match ($level) {
            'minor' => 'minor hike',
            'intermediate' => 'intermediate',
            'advanced' => 'advanced',
            default => 'beginner',
        };
        if ($bioNote === '') {
            $hikerBio = 'Hiker level: ' . $levelLabel . '.';
        }
        if ($minorHikesCompleted !== null) {
            $hikerBio .= ' Completed minor hikes: ' . $minorHikesCompleted . '.';
        }
        if ($majorHikesCompleted !== null) {
            $hikerBio .= ' Completed major hikes: ' . $majorHikesCompleted . '.';
        }
    }
    $stmt = tc_db()->prepare(
        'INSERT INTO users (
            full_name, email, password_hash, role, is_verified, email_verified_at, phone_number, current_address, hiking_level, minor_hikes_completed, major_hikes_completed, bio, home
        ) VALUES (
            :full_name, :email, :password_hash, :role, :is_verified, :email_verified_at, :phone_number, :current_address, :hiking_level, :minor_hikes_completed, :major_hikes_completed, :bio, :home
        )'
    );
    $stmt->execute([
        'full_name' => trim($name),
        'email' => $cleanEmail,
        'password_hash' => $hash,
        'role' => $cleanRole,
        'is_verified' => 0,
        'email_verified_at' => null,
        'phone_number' => $phoneNumber !== '' ? $phoneNumber : null,
        'current_address' => $currentAddress !== '' ? $currentAddress : null,
        'hiking_level' => $cleanRole === 'hiker' && $level !== '' ? $level : null,
        'minor_hikes_completed' => $cleanRole === 'hiker' ? $minorHikesCompleted : null,
        'major_hikes_completed' => $cleanRole === 'hiker' ? $majorHikesCompleted : null,
        'bio' => $cleanRole === 'hiker' ? $hikerBio : $defaultBio,
        'home' => $currentAddress !== '' ? $currentAddress : 'Baguio City, Philippines',
    ]);

    return (array) tc_db_find_user_by_id((int) tc_db()->lastInsertId());
}

function tc_db_update_user_profile(int $id, array $fields): void
{
    $allowed = [
        'full_name', 'bio', 'home', 'avatar_path', 'two_factor_enabled', 'two_factor_secret', 'two_factor_temp_secret',
        'phone_number', 'current_address', 'hiking_level', 'minor_hikes_completed', 'major_hikes_completed',
        'emergency_contact_name', 'emergency_contact_number', 'medical_notes',
    ];
    $sets = [];
    $params = ['id' => $id];
    foreach ($allowed as $key) {
        if (array_key_exists($key, $fields)) {
            $sets[] = $key . ' = :' . $key;
            $params[$key] = $fields[$key];
        }
    }
    if ($sets === []) {
        return;
    }
    $sql = 'UPDATE users SET ' . implode(', ', $sets) . ' WHERE id = :id';
    $stmt = tc_db()->prepare($sql);
    $stmt->execute($params);
}

function tc_db_password_reset_create(int $userId, string $rawToken, int $ttlSeconds = 3600): void
{
    $expiresAt = date('Y-m-d H:i:s', time() + max(300, $ttlSeconds));
    tc_db()->prepare('UPDATE password_resets SET used_at = NOW() WHERE user_id = :user_id AND used_at IS NULL')
        ->execute(['user_id' => $userId]);
    $stmt = tc_db()->prepare(
        'INSERT INTO password_resets (user_id, token_hash, expires_at, used_at) VALUES (:user_id, :token_hash, :expires_at, NULL)'
    );
    $stmt->execute([
        'user_id' => $userId,
        'token_hash' => password_hash($rawToken, PASSWORD_DEFAULT),
        'expires_at' => $expiresAt,
    ]);
}

function tc_db_password_reset_consume(string $rawToken, string $newPassword): bool
{
    $stmt = tc_db()->query(
        "SELECT id, user_id, token_hash, expires_at, used_at
         FROM password_resets
         WHERE used_at IS NULL
         ORDER BY id DESC
         LIMIT 30"
    );
    $rows = $stmt->fetchAll();
    $matched = null;
    foreach ($rows as $row) {
        if (!is_array($row)) {
            continue;
        }
        if (!password_verify($rawToken, (string) ($row['token_hash'] ?? ''))) {
            continue;
        }
        $matched = $row;
        break;
    }
    if (!is_array($matched)) {
        return false;
    }
    $expiresAt = strtotime((string) ($matched['expires_at'] ?? ''));
    if ($expiresAt === false || $expiresAt < time()) {
        return false;
    }
    $userId = (int) ($matched['user_id'] ?? 0);
    if ($userId <= 0) {
        return false;
    }
    $hash = password_hash($newPassword, PASSWORD_DEFAULT);
    tc_db()->prepare('UPDATE users SET password_hash = :password_hash WHERE id = :id')
        ->execute(['password_hash' => $hash, 'id' => $userId]);
    tc_db()->prepare('UPDATE password_resets SET used_at = NOW() WHERE id = :id')
        ->execute(['id' => (int) $matched['id']]);

    return true;
}

function tc_db_users_all(): array
{
    $stmt = tc_db()->query('SELECT id, full_name, email, role, is_disabled, created_at FROM users ORDER BY id DESC');
    $rows = $stmt->fetchAll();
    $out = [];
    foreach ($rows as $row) {
        if (is_array($row)) {
            $out[] = $row;
        }
    }

    return $out;
}

function tc_db_user_set_disabled(int $userId, bool $disabled): void
{
    $stmt = tc_db()->prepare('UPDATE users SET is_disabled = :is_disabled WHERE id = :id');
    $stmt->execute([
        'is_disabled' => $disabled ? 1 : 0,
        'id' => $userId,
    ]);
}

function tc_db_email_verification_create(int $userId, string $rawToken, int $ttlSeconds = 86400): void
{
    $expiresAt = date('Y-m-d H:i:s', time() + max(1800, $ttlSeconds));
    tc_db()->prepare('UPDATE email_verifications SET used_at = NOW() WHERE user_id = :user_id AND used_at IS NULL')
        ->execute(['user_id' => $userId]);
    $stmt = tc_db()->prepare(
        'INSERT INTO email_verifications (user_id, token_hash, expires_at, used_at) VALUES (:user_id, :token_hash, :expires_at, NULL)'
    );
    $stmt->execute([
        'user_id' => $userId,
        'token_hash' => password_hash($rawToken, PASSWORD_DEFAULT),
        'expires_at' => $expiresAt,
    ]);
}

/**
 * Organizer account that receives default published events + demo join requests (env override).
 */
function tc_primary_organizer_email(): string
{
    $e = $_ENV['TC_PRIMARY_ORGANIZER_EMAIL'] ?? 'retizajohnroy777@gmail.com';

    return strtolower(trim((string) $e));
}

/**
 * Ensures the primary organizer has published events and pending join requests from demo hikers
 * (Lian, Kiko, Nina). If they own no events, assigns the first rows in `events` to them (demo DB),
 * or inserts small defaults when the table is empty.
 */
function tc_db_ensure_primary_organizer_dashboard_demo(): void
{
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;
    if (!tc_db_entities_available()) {
        return;
    }
    $email = tc_primary_organizer_email();
    if ($email === '') {
        return;
    }
    $org = tc_db_find_user_by_email($email);
    if ($org === null || ($org['role'] ?? '') !== 'organizer') {
        return;
    }
    $orgId = (int) ($org['id'] ?? 0);
    $orgName = trim((string) ($org['full_name'] ?? 'Organizer'));
    if ($orgId <= 0) {
        return;
    }

    $demoUsers = tc_db_ensure_demo_users();
    $hikers = [];
    foreach (['lian', 'kiko', 'nina'] as $key) {
        if (!isset($demoUsers[$key])) {
            return;
        }
        $hikers[$key] = $demoUsers[$key];
    }

    $countStmt = tc_db()->prepare('SELECT COUNT(*) FROM events WHERE organizer_user_id = :oid');
    $countStmt->execute(['oid' => $orgId]);
    $owned = (int) $countStmt->fetchColumn();

    if ($owned === 0) {
        $anyStmt = tc_db()->query('SELECT COUNT(*) FROM events');
        $anyCount = (int) $anyStmt->fetchColumn();
        if ($anyCount > 0) {
            $sub = tc_db()->query('SELECT id FROM events ORDER BY id ASC LIMIT 8');
            $ids = $sub->fetchAll(\PDO::FETCH_COLUMN);
            $upd = tc_db()->prepare('UPDATE events SET organizer_user_id = :oid, organizer_name = :oname WHERE id = :id');
            foreach ($ids as $eid) {
                $upd->execute(['oid' => $orgId, 'oname' => $orgName, 'id' => (int) $eid]);
            }
        } else {
            $seedEvents = [
                [
                    'title' => 'Mt. Talinis · Twin Lakes circuit',
                    'trail' => 'Mt. Talinis · Cuernos de Negros (Negros Oriental)',
                    'date' => '2026-09-06',
                    'time' => '05:00',
                    'difficulty' => 'mod',
                    'meet' => 'Valencia / Balinsasayao briefing point',
                    'max' => 14,
                    'desc' => 'Default published hike — twin-lake circuit and guided pacing.',
                    'approval' => 'manual',
                    'organizer' => $orgName,
                    'organizer_user_id' => $orgId,
                    'status' => 'published',
                ],
                [
                    'title' => 'Mt. Mandalagan · Tinagong Dagat weekend',
                    'trail' => 'Mt. Mandalagan (Negros Occidental)',
                    'date' => '2026-09-13',
                    'time' => '05:30',
                    'difficulty' => 'mod',
                    'meet' => 'Sagay jump-off / briefing',
                    'max' => 12,
                    'desc' => 'Default published hike — volcanic plateau and crater sector.',
                    'approval' => 'manual',
                    'organizer' => $orgName,
                    'organizer_user_id' => $orgId,
                    'status' => 'published',
                ],
                [
                    'title' => 'Mt. Pulag · Akiki–Ambangeg batch',
                    'trail' => 'Mt. Pulag · Akiki–Ambangeg (Benguet / Ifugao)',
                    'date' => '2026-09-20',
                    'time' => '05:00',
                    'difficulty' => 'hard',
                    'meet' => 'Benguet briefing point',
                    'max' => 12,
                    'desc' => 'Default published hike — major batch with regroup points.',
                    'approval' => 'manual',
                    'organizer' => $orgName,
                    'organizer_user_id' => $orgId,
                    'status' => 'published',
                ],
            ];
            foreach ($seedEvents as $ev) {
                tc_db_event_save($ev + ['id' => 0]);
            }
        }
    }

    $stmt = tc_db()->prepare('SELECT id FROM events WHERE organizer_user_id = :oid ORDER BY id ASC LIMIT 10');
    $stmt->execute(['oid' => $orgId]);
    $eventIds = $stmt->fetchAll(\PDO::FETCH_COLUMN);
    if (!is_array($eventIds) || $eventIds === []) {
        return;
    }
    $nEvents = count($eventIds);

    $plan = [
        ['key' => 'lian', 'idx' => 0],
        ['key' => 'kiko', 'idx' => min(1, $nEvents - 1)],
        ['key' => 'nina', 'idx' => min(2, $nEvents - 1)],
    ];
    foreach ($plan as $row) {
        $hkey = (string) ($row['key'] ?? '');
        $idx = (int) ($row['idx'] ?? 0);
        if (!isset($hikers[$hkey])) {
            continue;
        }
        $eid = (int) ($eventIds[$idx] ?? $eventIds[0]);
        $uid = (int) ($hikers[$hkey]['id'] ?? 0);
        if ($eid <= 0 || $uid <= 0) {
            continue;
        }
        $check = tc_db()->prepare('SELECT id FROM join_requests WHERE event_id = :e AND user_id = :u LIMIT 1');
        $check->execute(['e' => $eid, 'u' => $uid]);
        if ($check->fetch()) {
            continue;
        }
        tc_db_join_request_save([
            'event_id' => $eid,
            'hiker_name' => (string) ($hikers[$hkey]['full_name'] ?? ''),
            'user_id' => $uid,
            'status' => 'pending',
            'requested_at' => date('Y-m-d H:i:s'),
        ]);
    }
}

function tc_db_email_verification_consume(string $rawToken): bool
{
    $stmt = tc_db()->query(
        "SELECT id, user_id, token_hash, expires_at, used_at
         FROM email_verifications
         WHERE used_at IS NULL
         ORDER BY id DESC
         LIMIT 30"
    );
    $rows = $stmt->fetchAll();
    $matched = null;
    foreach ($rows as $row) {
        if (!is_array($row)) {
            continue;
        }
        if (!password_verify($rawToken, (string) ($row['token_hash'] ?? ''))) {
            continue;
        }
        $matched = $row;
        break;
    }
    if (!is_array($matched)) {
        return false;
    }
    $expiresAt = strtotime((string) ($matched['expires_at'] ?? ''));
    if ($expiresAt === false || $expiresAt < time()) {
        return false;
    }
    $userId = (int) ($matched['user_id'] ?? 0);
    if ($userId <= 0) {
        return false;
    }
    tc_db()->prepare('UPDATE users SET is_verified = 1, email_verified_at = NOW() WHERE id = :id')
        ->execute(['id' => $userId]);
    tc_db()->prepare('UPDATE email_verifications SET used_at = NOW() WHERE id = :id')
        ->execute(['id' => (int) $matched['id']]);

    return true;
}
