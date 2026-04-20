<?php
declare(strict_types=1);
$pageTitle = 'Event details — TrailConnect';
$bodyClass = 'app-body';
include 'partials/header.php';
include 'partials/navbar.php';
?>
<div class="container container--app container--narrow">
    <header class="page-head">
        <p class="kicker">Philippines · Major hike</p>
        <h1 class="page-title">Mt. Apo · Kapatagan–Kidapawan traverse</h1>
        <p class="page-lede">
            <span class="stars">★★★★★</span> Organizer <strong>Mindanao Ascents</strong>
            <span class="badge-diff badge-diff--hard">Hard</span>
            <span class="text-muted">· Davao / Cotabato · 2954 MASL</span>
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
            <li><strong>Date &amp; time</strong> Saturday, May 18, 2026 · staged meet · multi-day</li>
            <li><strong>Meeting point</strong> Kapatagan jump-off (pre-arranged) — group flag at staging area
                <a class="text-link" href="#">Open in maps</a>
            </li>
            <li><strong>Duration</strong> ~3 days · boulder fields, sulfur vents, forest trails</li>
        </ul>
    </section>

    <section class="card card--stack glass-stack">
        <h2 class="section-title">Approved capacity</h2>
        <p class="field-hint">Counts <strong>approved</strong> participants only — not pending join requests.</p>
        <div class="capacity-bar" role="img" aria-label="4 approved of 10 maximum">
            <div class="capacity-bar__fill" style="width: 40%"></div>
        </div>
        <p class="capacity-bar__label"><strong>4</strong> approved · <strong>10</strong> maximum</p>
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
        <a class="btn-primary" href="index.php?page=my_event">Join event</a>
        <p class="action-bar__alt">
            <span class="text-muted">Button states:</span>
            <em>Full</em> · <em>Already joined</em> · Auto-approve shows <em>Approved</em> immediately.
        </p>
    </div>
    <p class="text-center">
        <a class="text-link" href="index.php?page=find_hikes">← Back to Find hikes</a>
    </p>
</div>
<?php include 'partials/footer.php'; ?>
