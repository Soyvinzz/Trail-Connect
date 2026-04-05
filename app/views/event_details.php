<?php
declare(strict_types=1);
$pageTitle = 'Event details — TrailConnect';
$bodyClass = 'app-body';
include 'partials/header.php';
include 'partials/navbar.php';
?>
<div class="container container--app container--narrow">
    <header class="page-head">
        <p class="kicker">Negros Occidental</p>
        <h1 class="page-title">Patag plateau loop</h1>
        <p class="page-lede">
            <span class="stars">★★★★½</span> Organizer <strong>Mara Villanueva</strong>
            <span class="badge-diff badge-diff--mod">Moderate</span>
            <span class="text-muted">· Silay / Patag</span>
        </p>
    </header>

    <section class="card card--stack glass-stack safety-first">
        <h2 class="section-title safety-first__title">Safety &amp; emergency</h2>
        <p class="safety-first__note">Kept high on the page so everyone sees it before the hike.</p>
        <ul class="detail-list">
            <li><strong>Organizer emergency</strong> Mara Villanueva · +63 917 555 0142</li>
            <li><strong>Local response</strong> Murcia MDRRMO · Silay CDRRMO (coordinate for Patag access)</li>
            <li><strong>Protocol</strong> If separated: stay put, three whistle bursts, offline pin “Patag saddle clearing.”</li>
        </ul>
    </section>

    <section class="card card--stack glass-stack">
        <h2 class="section-title">When &amp; where</h2>
        <ul class="detail-list">
            <li><strong>Date &amp; time</strong> Saturday, Apr 12, 2026 · 8:00 AM – ~3:00 PM</li>
            <li><strong>Meeting point</strong> Patag jump-off parking (Silay side) — group flag near guard post
                <a class="text-link" href="#">Open in maps</a>
            </li>
            <li><strong>Duration</strong> ~7 hours including breaks &amp; photos at the saddle</li>
        </ul>
    </section>

    <section class="card card--stack glass-stack">
        <h2 class="section-title">Approved capacity</h2>
        <p class="field-hint">Counts <strong>approved</strong> participants only — not pending join requests.</p>
        <div class="capacity-bar" role="img" aria-label="5 approved of 12 maximum">
            <div class="capacity-bar__fill" style="width: 41.67%"></div>
        </div>
        <p class="capacity-bar__label"><strong>5</strong> approved · <strong>12</strong> maximum</p>
    </section>

    <section class="card card--stack glass-stack">
        <h2 class="section-title">Required gear</h2>
        <ul class="check-list">
            <li>Sturdy boots, 2L water, rain shell (Patag wind &amp; fog)</li>
            <li>Headlamp + spare batteries</li>
            <li>Whistle and compact first-aid kit</li>
        </ul>
    </section>

    <section class="card card--stack glass-stack">
        <h2 class="section-title">About this hike</h2>
        <p>Open grassland and pine-lined sections with views toward Bacolod and the strait. We regroup every 45 minutes; turn back if <em>habagat</em> clouds build or trails get slick after rain.</p>
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
