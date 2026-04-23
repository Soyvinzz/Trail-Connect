<?php
declare(strict_types=1);
$pageTitle = 'Event details — TrailConnect';
$bodyClass = 'app-body';
$eventId = (int) ($_GET['event_id'] ?? 1);
$event = tc_find_event($eventId);
if ($event === null) {
    $event = tc_find_event(1);
    $eventId = (int) ($event['id'] ?? 1);
}
$returnUrl = (string) ($_GET['return'] ?? 'index.php?page=find_hikes');
if ($returnUrl === '' || strpos($returnUrl, 'index.php') !== 0) {
    $returnUrl = 'index.php?page=find_hikes';
}
$difficultyLabels = [
    'easy' => 'EASY',
    'mod' => 'MODERATE',
    'hard' => 'HARD',
    'vhard' => 'VERY HARD',
];
$myRequest = null;
foreach (tc_join_requests() as $request) {
    if ((int) $request['event_id'] === $eventId && (string) $request['hiker_name'] === tc_display_name()) {
        $myRequest = $request;
        break;
    }
}
include 'partials/header.php';
include 'partials/navbar.php';
?>
<div class="container container--app container--narrow">
    <header class="page-head">
        <p class="kicker">Philippines · Major hike</p>
        <h1 class="page-title"><?php echo htmlspecialchars((string) ($event['title'] ?? 'Event'), ENT_QUOTES, 'UTF-8'); ?></h1>
        <p class="page-lede">
            Organizer <strong><?php echo htmlspecialchars((string) ($event['organizer'] ?? 'TrailConnect Organizer'), ENT_QUOTES, 'UTF-8'); ?></strong>
            <?php $difficultyKey = (string) ($event['difficulty'] ?? 'mod'); ?>
            <span class="badge-diff badge-diff--<?php echo htmlspecialchars($difficultyKey, ENT_QUOTES, 'UTF-8'); ?>">
                <?php echo htmlspecialchars($difficultyLabels[$difficultyKey] ?? strtoupper($difficultyKey), ENT_QUOTES, 'UTF-8'); ?>
            </span>
            <span class="text-muted">· <?php echo htmlspecialchars((string) ($event['trail'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></span>
        </p>
    </header>

    <section class="card card--stack glass-stack safety-first">
        <h2 class="section-title safety-first__title">Safety &amp; emergency</h2>
        <p class="safety-first__note">Kept high on the page so everyone sees it before the hike.</p>
        <ul class="detail-list">
            <li><strong>Organizer emergency</strong> Mindanao Ascents · +63 917 555 0142</li>
            <li><strong>Local response</strong> Kidapawan CDRRMO · coordinate for crater sector &amp; ranger stations</li>
            <li><strong>Protocol</strong> If separated: stay put, three whistle bursts, share last known waypoint offline.</li>
        </ul>
    </section>

    <section class="card card--stack glass-stack">
        <h2 class="section-title">When &amp; where</h2>
        <ul class="detail-list">
            <li><strong>Date &amp; time</strong> <?php echo htmlspecialchars((string) ($event['date'] ?? ''), ENT_QUOTES, 'UTF-8'); ?> · <?php echo htmlspecialchars((string) ($event['time'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></li>
            <li><strong>Meeting point</strong> <?php echo htmlspecialchars((string) ($event['meet'] ?? 'See event briefing.'), ENT_QUOTES, 'UTF-8'); ?>
                <a class="text-link" href="#">Open in maps</a>
            </li>
            <li><strong>Event summary</strong> <?php echo htmlspecialchars((string) ($event['desc'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></li>
        </ul>
    </section>

    <section class="card card--stack glass-stack">
        <?php
        $approvedCount = 0;
        foreach (tc_join_requests() as $request) {
            if ((int) $request['event_id'] === $eventId && (string) $request['status'] === 'approved') {
                $approvedCount++;
            }
        }
        $maxCap = (int) ($event['max'] ?? 0);
        $fill = $maxCap > 0 ? min(100, (int) round(($approvedCount / $maxCap) * 100)) : 0;
        ?>
        <h2 class="section-title">Event capacity</h2>
        <p class="field-hint">Approved joiners: <strong><?php echo $approvedCount; ?></strong> of <strong><?php echo $maxCap; ?></strong>.</p>
        <div class="capacity-bar" role="img" aria-label="<?php echo $approvedCount; ?> approved of <?php echo $maxCap; ?>">
            <div class="capacity-bar__fill" style="width: <?php echo $fill; ?>%"></div>
        </div>
        <p class="capacity-bar__label"><strong><?php echo $approvedCount; ?></strong> approved · <strong><?php echo $maxCap; ?></strong> maximum</p>
    </section>

    <section class="card card--stack glass-stack">
        <h2 class="section-title">Required gear</h2>
        <ul class="check-list">
            <li>Sturdy boots, 3L+ water, layers for cold summit nights</li>
            <li>Headlamp + spare batteries · gas mask sector near sulfur vents if advised</li>
            <li>Whistle, first-aid, sun protection for exposed boulder fields</li>
        </ul>
    </section>

    <section class="card card--stack glass-stack">
        <h2 class="section-title">About this hike</h2>
        <p>Highest peak in the Philippines — diverse terrain from mossy forests to boulder fields and sulfur vents, often done as a multi-day traverse. High fitness and prior major-hike experience required.</p>
    </section>

    <div class="action-bar">
        <form method="post" action="index.php?page=event_details">
            <input type="hidden" name="action" value="join_event">
            <input type="hidden" name="event_id" value="<?php echo $eventId; ?>">
            <input type="hidden" name="return_to" value="<?php echo htmlspecialchars($returnUrl, ENT_QUOTES, 'UTF-8'); ?>">
            <?php if (tc_role() === 'organizer') : ?>
                <button type="button" class="btn-primary" disabled>Organizer view</button>
            <?php elseif ($myRequest !== null && (string) $myRequest['status'] === 'pending') : ?>
                <button type="button" class="btn-primary" disabled>Request pending</button>
            <?php elseif ($myRequest !== null && (string) $myRequest['status'] === 'approved') : ?>
                <button type="button" class="btn-primary" disabled>Already approved</button>
            <?php else : ?>
                <button type="submit" class="btn-primary">Join event</button>
            <?php endif; ?>
        </form>
        <p class="action-bar__alt">
            <?php if ($myRequest !== null) : ?>
                <span class="text-muted">Current request status:</span> <strong><?php echo htmlspecialchars(ucfirst((string) $myRequest['status']), ENT_QUOTES, 'UTF-8'); ?></strong>
            <?php else : ?>
                <span class="text-muted">No request yet. Click Join event to submit.</span>
            <?php endif; ?>
        </p>
    </div>
    <p class="text-center">
        <a class="text-link" href="<?php echo htmlspecialchars($returnUrl, ENT_QUOTES, 'UTF-8'); ?>">← Back</a>
    </p>
</div>
<?php include 'partials/footer.php'; ?>
