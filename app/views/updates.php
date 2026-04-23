<?php
declare(strict_types=1);
$pageTitle = 'Updates — TrailConnect';
$bodyClass = 'app-body';
$events = array_values(tc_events());
$updates = array_values(tc_updates());
usort($updates, static function (array $a, array $b): int {
    return strcmp((string) ($b['posted_at'] ?? ''), (string) ($a['posted_at'] ?? ''));
});
$editingUpdateId = (int) ($_GET['edit_update'] ?? 0);
$editingUpdate = null;
foreach ($updates as $u) {
    if ((int) $u['id'] === $editingUpdateId) {
        $editingUpdate = $u;
        break;
    }
}
$msg = (string) ($_GET['msg'] ?? '');
$messages = [
    'update_published' => 'Update published successfully.',
    'update_saved' => 'Update changes saved successfully.',
    'update_deleted' => 'Update deleted successfully.',
];
include 'partials/header.php';
include 'partials/navbar.php';
?>
<div class="container container--app container--narrow">
    <header class="page-head">
        <h1 class="page-title">Trail updates</h1>
        <p class="page-lede">Safety pins, weather checks, and meet-point tweaks for hikes across the <strong>Philippines</strong>.</p>
    </header>

    <div class="banner-safety" role="status">
        <strong>Highland advisory</strong> — Cordillera summits (Pulag, Tabayoc) may see fast-moving fog after rain. Organizers: confirm turnaround rules before May traverse season.
    </div>
    <?php if (isset($messages[$msg])) : ?>
        <div class="banner-safety" role="status" style="margin-top:0.75rem">
            <strong>Notification:</strong> <?php echo htmlspecialchars($messages[$msg], ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <section class="card card--stack glass-stack">
        <h2 class="section-title">Feed</h2>
        <?php if (empty($updates)) : ?>
            <p class="text-muted">No updates yet.</p>
        <?php else : ?>
            <?php foreach ($updates as $update) : ?>
                <?php $event = tc_find_event((int) $update['event_id']); ?>
                <div class="feed-item">
                    <div class="feed-item__head">
                        <span class="feed-badge feed-badge--general"><?php echo htmlspecialchars((string) $update['type'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <time class="feed-item__time"><?php echo htmlspecialchars((string) $update['posted_at'], ENT_QUOTES, 'UTF-8'); ?></time>
                    </div>
                    <p class="feed-item__body"><strong><?php echo htmlspecialchars((string) ($event['title'] ?? 'Unknown event'), ENT_QUOTES, 'UTF-8'); ?></strong> — <?php echo htmlspecialchars((string) $update['message'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <div class="inline-actions">
                        <a class="text-link" href="index.php?page=updates&edit_update=<?php echo (int) $update['id']; ?>">Edit</a>
                        <form method="post" action="index.php?page=updates">
                            <input type="hidden" name="action" value="delete_update">
                            <input type="hidden" name="update_id" value="<?php echo (int) $update['id']; ?>">
                            <button type="submit" class="btn-secondary btn-secondary--sm">Delete</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        <p class="field-hint">Use this feed to publish, revise, and remove official trail announcements.</p>
    </section>

    <section class="card card--stack glass-stack compose-panel">
        <h2 class="section-title">Post update</h2>
        <form method="post" action="index.php?page=updates">
            <input type="hidden" name="action" value="save_update">
            <input type="hidden" name="update_id" value="<?php echo (int) ($editingUpdate['id'] ?? 0); ?>">
        <label class="field-label" for="upd-event">Event</label>
        <select id="upd-event" class="input input--select" name="event_id" required>
            <option value="">Select event…</option>
            <?php foreach ($events as $event) : ?>
                <option value="<?php echo (int) $event['id']; ?>" <?php echo ((int) ($editingUpdate['event_id'] ?? 0) === (int) $event['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars((string) $event['title'], ENT_QUOTES, 'UTF-8'); ?> — <?php echo htmlspecialchars((string) $event['date'], ENT_QUOTES, 'UTF-8'); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <label class="field-label" for="upd-type">Type</label>
        <select id="upd-type" class="input input--select" name="type" required>
            <?php
            $types = ['General', 'Weather', 'Safety', 'Meet point'];
            $selectedType = (string) ($editingUpdate['type'] ?? 'General');
            foreach ($types as $type) :
            ?>
                <option <?php echo $selectedType === $type ? 'selected' : ''; ?>><?php echo htmlspecialchars($type, ENT_QUOTES, 'UTF-8'); ?></option>
            <?php endforeach; ?>
        </select>
        <label class="field-label" for="upd-body">Message</label>
        <textarea id="upd-body" class="input" rows="3" name="message" placeholder="Short note to approved hikers — e.g. ranger station closes registration at 4:00 PM." required><?php echo htmlspecialchars((string) ($editingUpdate['message'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></textarea>
        <button type="submit" class="btn-primary" style="margin-top:0.75rem"><?php echo $editingUpdate ? 'Save update' : 'Publish to feed'; ?></button>
        </form>
    </section>
</div>
<?php include 'partials/footer.php'; ?>
