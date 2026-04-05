<?php
declare(strict_types=1);
$pageTitle = 'Updates — TrailConnect';
$bodyClass = 'app-body';
include 'partials/header.php';
include 'partials/navbar.php';
?>
<div class="container container--app container--narrow">
    <header class="page-head">
        <h1 class="page-title">Trail updates</h1>
        <p class="page-lede">Safety pins, weather checks, and meet-point tweaks for hikes around <strong>Bacolod &amp; Negros Occidental</strong>.</p>
    </header>

    <div class="banner-safety" role="status">
        <strong>Highland advisory</strong> — Patag and DSB may see fast-moving fog after midday rain. Organizers: confirm turnaround rules before Apr 12 weekend hikes.
    </div>

    <section class="card card--stack glass-stack">
        <h2 class="section-title">Feed</h2>
        <div class="feed-item">
            <div class="feed-item__head">
                <span class="feed-badge feed-badge--meet">Meet point</span>
                <time class="feed-item__time" datetime="2026-04-03T14:20">Apr 3, 2:20 PM</time>
            </div>
            <p class="feed-item__body"><strong>Mara Villanueva</strong> · Patag plateau loop — meet at the <em>Silay-side guard post</em> (not the old shed). Flag is teal + white.</p>
        </div>
        <div class="feed-item">
            <div class="feed-item__head">
                <span class="feed-badge feed-badge--weather">Weather</span>
                <time class="feed-item__time" datetime="2026-04-03T09:00">Apr 3, 9:00 AM</time>
            </div>
            <p class="feed-item__body">Murcia / Mambukal: light showers until noon. Trails stay open; bring shells — red clay gets slick near the falls.</p>
        </div>
        <div class="feed-item">
            <div class="feed-item__head">
                <span class="feed-badge feed-badge--safety">Safety</span>
                <time class="feed-item__time" datetime="2026-04-02T18:45">Apr 2, 6:45 PM</time>
            </div>
            <p class="feed-item__body"><strong>Rico Magbanua</strong> · DSB pine ridge — if wind gusts exceed comfort at the second saddle, we <strong>shorten the loop</strong> and regroup at the pine stand.</p>
        </div>
        <div class="feed-item">
            <div class="feed-item__head">
                <span class="feed-badge feed-badge--general">General</span>
                <time class="feed-item__time" datetime="2026-04-01T11:10">Apr 1, 11:10 AM</time>
            </div>
            <p class="feed-item__body">Victorias Trail Club: Gawahon eco loop still has <strong>2 spots</strong> — kid-friendly pace, sunset option on Apr 22.</p>
        </div>
    </section>

    <section class="card card--stack glass-stack compose-panel">
        <h2 class="section-title">Post update <span class="text-muted" style="font-size:0.75rem;font-weight:400">(organizer sample)</span></h2>
        <label class="field-label" for="upd-event">Event</label>
        <select id="upd-event" class="input input--select">
            <option>Patag plateau loop — Apr 12</option>
            <option>Mambukal falls trail — Apr 20</option>
            <option>DSB pine ridge — Apr 18</option>
        </select>
        <label class="field-label" for="upd-type">Type</label>
        <select id="upd-type" class="input input--select">
            <option>General</option>
            <option>Weather</option>
            <option>Safety</option>
            <option>Meet point</option>
        </select>
        <label class="field-label" for="upd-body">Message</label>
        <textarea id="upd-body" class="input" rows="3" placeholder="Short note to approved hikers — e.g. Victorias gate closes at 5:30 PM."></textarea>
        <button type="button" class="btn-primary" style="margin-top:0.75rem">Publish to feed</button>
    </section>
</div>
<?php include 'partials/footer.php'; ?>
