<?php
declare(strict_types=1);
$pageTitle = 'My events — TrailConnect';
$bodyClass = 'app-body';
$role = tc_role();
$requests = tc_join_requests();
$currentName = tc_display_name();
$currentUserId = tc_current_user_id();
$events = tc_events();
if ($role === 'organizer') {
    $events = array_values(array_filter($events, static function ($event): bool {
        return tc_event_manageable_by_current_organizer(is_array($event) ? $event : null);
    }));
}
$myRequests = [];
$incomingRequests = [];
foreach ($requests as $request) {
    $matchesHiker =
        ($currentUserId > 0 && isset($request['user_id']) && (int) $request['user_id'] === $currentUserId)
        || (string) $request['hiker_name'] === $currentName;
    if ($role === 'hiker' && $matchesHiker) {
        $myRequests[] = $request;
    }
    if ($role === 'organizer' && (string) ($request['status'] ?? '') === 'pending') {
        $evt = tc_find_event((int) ($request['event_id'] ?? 0));
        if (!tc_event_manageable_by_current_organizer(is_array($evt) ? $evt : null)) {
            continue;
        }
        $incomingRequests[] = $request;
    }
}
$msg = (string) ($_GET['msg'] ?? '');
$messages = [
    'event_created' => 'Event created successfully.',
    'event_updated' => 'Event updated successfully.',
    'event_deleted' => 'Event deleted successfully.',
    'join_request_submitted' => 'Join request submitted. Status is now pending.',
    'request_status_updated' => 'Join request status updated.',
    'join_request_deleted' => 'Join request deleted.',
    'join_requirement_failed' => 'Join request blocked. Your profile does not yet meet this event minimum requirement.',
];
$difficultyLabels = [
    'easy' => 'EASY',
    'mod' => 'MODERATE',
    'hard' => 'HARD',
    'vhard' => 'VERY HARD',
];
include 'partials/header.php';
include 'partials/navbar.php';
$currentReturn = (string) ($_SERVER['REQUEST_URI'] ?? 'index.php?page=my_event');
?>
<div class="container container--app">
    <header class="page-head">
        <h1 class="page-title">My events</h1>
        <p class="page-lede">
            <?php echo $role === 'organizer'
                ? 'Manage published hikes, review join requests, and keep event capacity accurate.'
                : 'Track your joined hikes and monitor request status in real time.'; ?>
        </p>
    </header>

    <?php if (isset($messages[$msg])) : ?>
        <div class="banner-safety" role="status" style="margin-bottom:1rem">
            <strong>Notification:</strong> <?php echo htmlspecialchars($messages[$msg], ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <?php if ($role === 'organizer') : ?>
        <section class="card card--stack glass-stack">
            <h2 class="section-title">Published events</h2>
            <?php if (empty($events)) : ?>
                <p class="text-muted">No published events yet.</p>
            <?php else : ?>
                <?php foreach ($events as $event) : ?>
                    <?php $rowTrailImg = tc_trail_image_url($event); ?>
                    <article class="event-row" style="margin-bottom:1rem">
                        <div class="event-row__thumb-wrap" aria-hidden="true">
                            <img class="event-row__thumb" src="<?php echo htmlspecialchars($rowTrailImg, ENT_QUOTES, 'UTF-8'); ?>" alt="" width="112" height="84" loading="lazy" decoding="async">
                        </div>
                        <div class="event-row__main">
                            <div class="event-row__top">
                                <h3 class="event-card__trail" style="margin:0"><?php echo htmlspecialchars((string) $event['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                                <?php $difficultyKey = (string) ($event['difficulty'] ?? 'mod'); ?>
                                <span class="badge-diff badge-diff--<?php echo htmlspecialchars($difficultyKey, ENT_QUOTES, 'UTF-8'); ?>">
                                    <?php echo htmlspecialchars($difficultyLabels[$difficultyKey] ?? strtoupper($difficultyKey), ENT_QUOTES, 'UTF-8'); ?>
                                </span>
                            </div>
                            <p class="request-card__when"><?php echo htmlspecialchars((string) $event['date'], ENT_QUOTES, 'UTF-8'); ?> <?php echo htmlspecialchars((string) $event['time'], ENT_QUOTES, 'UTF-8'); ?> · <?php echo htmlspecialchars((string) $event['trail'], ENT_QUOTES, 'UTF-8'); ?></p>
                        </div>
                        <div class="inline-actions event-row__actions">
                            <a class="btn-secondary btn-secondary--sm" href="index.php?page=event_details&event_id=<?php echo (int) $event['id']; ?>&return=<?php echo urlencode($currentReturn); ?>">View</a>
                            <a class="btn-secondary btn-secondary--sm" href="index.php?page=create_event&event_id=<?php echo (int) $event['id']; ?>">Update</a>
                            <form method="post" action="index.php?page=my_event">
                                <input type="hidden" name="action" value="delete_event">
                                <input type="hidden" name="event_id" value="<?php echo (int) $event['id']; ?>">
                                <button type="submit" class="btn-secondary btn-secondary--sm">Delete</button>
                            </form>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>

        <section class="card card--stack glass-stack" style="margin-top:1rem">
            <h2 class="section-title">Incoming join requests</h2>
            <?php if (empty($incomingRequests)) : ?>
                <p class="text-muted">No incoming requests right now.</p>
            <?php else : ?>
                <?php foreach ($incomingRequests as $request) : ?>
                    <?php $event = tc_find_event((int) $request['event_id']); ?>
                    <?php $status = (string) ($request['status'] ?? 'pending'); ?>
                    <?php
                    $joinerLevel = 'Not set';
                    $joinerMinor = 0;
                    $joinerMajor = 0;
                    $joinerCompleteness = 0;
                    if (isset($request['user_id']) && (int) $request['user_id'] > 0 && tc_db_entities_available()) {
                        try {
                            $joiner = tc_db_find_user_by_id((int) $request['user_id']);
                        } catch (\Throwable $e) {
                            $joiner = null;
                        }
                        if (is_array($joiner)) {
                            $joinerLevel = tc_hiking_level_label((string) ($joiner['hiking_level'] ?? ''));
                            $joinerMinor = (int) ($joiner['minor_hikes_completed'] ?? 0);
                            $joinerMajor = (int) ($joiner['major_hikes_completed'] ?? 0);
                            $joinerCompleteness = tc_profile_completeness([
                                'display_name' => (string) ($joiner['full_name'] ?? ''),
                                'phone_number' => (string) ($joiner['phone_number'] ?? ''),
                                'current_address' => (string) ($joiner['current_address'] ?? ''),
                                'hiking_level' => (string) ($joiner['hiking_level'] ?? ''),
                                'minor_hikes_completed' => (int) ($joiner['minor_hikes_completed'] ?? 0),
                                'major_hikes_completed' => (int) ($joiner['major_hikes_completed'] ?? 0),
                                'bio' => (string) ($joiner['bio'] ?? ''),
                                'emergency_contact_name' => (string) ($joiner['emergency_contact_name'] ?? ''),
                                'emergency_contact_number' => (string) ($joiner['emergency_contact_number'] ?? ''),
                            ]);
                        }
                    }
                    ?>
                    <article class="request-card card card--inset" style="margin-bottom:1rem">
                        <p class="request-card__who"><strong><?php echo htmlspecialchars((string) $request['hiker_name'], ENT_QUOTES, 'UTF-8'); ?></strong> requested <strong><?php echo htmlspecialchars((string) ($event['title'] ?? 'Unknown event'), ENT_QUOTES, 'UTF-8'); ?></strong></p>
                        <p class="request-card__when"><?php echo htmlspecialchars((string) $request['requested_at'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <p class="request-card__when">
                            <strong>Readiness:</strong> <?php echo htmlspecialchars($joinerLevel, ENT_QUOTES, 'UTF-8'); ?>
                            · Minor: <?php echo (int) $joinerMinor; ?>
                            · Major: <?php echo (int) $joinerMajor; ?>
                            · Profile: <?php echo (int) $joinerCompleteness; ?>%
                        </p>
                        <div class="inline-actions" style="margin-top:0.75rem">
                            <span class="badge badge--pending"><?php echo htmlspecialchars(ucfirst($status), ENT_QUOTES, 'UTF-8'); ?></span>
                            <form method="post" action="index.php?page=my_event">
                                <input type="hidden" name="action" value="request_status">
                                <input type="hidden" name="request_id" value="<?php echo (int) $request['id']; ?>">
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" class="btn-primary">Approve</button>
                            </form>
                            <form method="post" action="index.php?page=my_event">
                                <input type="hidden" name="action" value="request_status">
                                <input type="hidden" name="request_id" value="<?php echo (int) $request['id']; ?>">
                                <input type="hidden" name="status" value="declined">
                                <button type="submit" class="btn-secondary">Decline</button>
                            </form>
                            <form method="post" action="index.php?page=my_event">
                                <input type="hidden" name="action" value="delete_request">
                                <input type="hidden" name="request_id" value="<?php echo (int) $request['id']; ?>">
                                <button type="submit" class="btn-secondary btn-secondary--sm">Delete</button>
                            </form>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    <?php else : ?>
        <section class="card card--stack glass-stack">
            <h2 class="section-title">My join requests</h2>
            <?php if (empty($myRequests)) : ?>
                <p class="text-muted">You have not joined any event yet.</p>
            <?php else : ?>
                <?php foreach ($myRequests as $request) : ?>
                    <?php $event = tc_find_event((int) $request['event_id']); ?>
                    <?php $hikerTrailImg = tc_trail_image_url(is_array($event) ? $event : null); ?>
                    <article class="event-row" style="margin-bottom:1rem">
                        <div class="event-row__thumb-wrap" aria-hidden="true">
                            <img class="event-row__thumb" src="<?php echo htmlspecialchars($hikerTrailImg, ENT_QUOTES, 'UTF-8'); ?>" alt="" width="112" height="84" loading="lazy" decoding="async">
                        </div>
                        <div class="event-row__main">
                            <div class="event-row__top">
                                <h3 class="event-card__trail" style="margin:0"><?php echo htmlspecialchars((string) ($event['title'] ?? 'Unknown event'), ENT_QUOTES, 'UTF-8'); ?></h3>
                            </div>
                            <p class="request-card__when"><?php echo htmlspecialchars((string) ($event['trail'] ?? ''), ENT_QUOTES, 'UTF-8'); ?> · <?php echo htmlspecialchars((string) ($event['date'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>
                        </div>
                        <div class="inline-actions inline-actions--stack">
                            <?php if ($request['status'] === 'approved') : ?>
                                <span class="badge badge--approved">Approved</span>
                            <?php elseif ($request['status'] === 'declined') : ?>
                                <span class="badge badge--full">Declined</span>
                            <?php else : ?>
                                <span class="badge badge--pending">Pending</span>
                            <?php endif; ?>
                            <a class="btn-secondary btn-secondary--sm" href="index.php?page=event_details&event_id=<?php echo (int) $request['event_id']; ?>&return=<?php echo urlencode($currentReturn); ?>">Details</a>
                            <?php if ($request['status'] !== 'approved') : ?>
                                <form method="post" action="index.php?page=my_event">
                                    <input type="hidden" name="action" value="delete_request">
                                    <input type="hidden" name="request_id" value="<?php echo (int) $request['id']; ?>">
                                    <button type="submit" class="btn-secondary btn-secondary--sm">Cancel request</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    <?php endif; ?>

    <p class="text-center" style="margin-top:1.5rem">
        <a class="btn-secondary btn-secondary--sm" href="index.php?page=find_hikes">Find more hikes</a>
        <?php if ($role === 'organizer') : ?>
            · <a class="btn-secondary btn-secondary--sm" href="index.php?page=create_event">Create event</a>
        <?php endif; ?>
    </p>
</div>
<?php include 'partials/footer.php'; ?>
