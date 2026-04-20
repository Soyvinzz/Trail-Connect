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
        <p class="page-lede">Safety pins, weather checks, and meet-point tweaks for hikes across the <strong>Philippines</strong>.</p>
    </header>

    <div class="banner-safety" role="status">
        <strong>Highland advisory</strong> — Cordillera summits (Pulag, Tabayoc) may see fast-moving fog after rain. Organizers: confirm turnaround rules before May traverse season.
    </div>

    <section class="card card--stack glass-stack">
        <h2 class="section-title">Feed</h2>
        <div class="feed-item">
            <div class="feed-item__head">
                <span class="feed-badge feed-badge--meet">Meet point</span>
                <time class="feed-item__time" datetime="2026-04-03T14:20">Apr 3, 2:20 PM</time>
            </div>
            <p class="feed-item__body"><strong>Cordillera Guides</strong> · Mt. Pulag · Akiki–Ambangeg — meet at the <em>agreed DENR briefing area</em> (not the old landmark). Group flag is teal + white.</p>
        </div>
        <div class="feed-item">
            <div class="feed-item__head">
                <span class="feed-badge feed-badge--weather">Weather</span>
                <time class="feed-item__time" datetime="2026-04-03T09:00">Apr 3, 9:00 AM</time>
            </div>
            <p class="feed-item__body">Bukidnon / Kitanglad range: light showers until noon. Trails stay open; bring shells — mossy sections get slick after rain.</p>
        </div>
        <div class="feed-item">
            <div class="feed-item__head">
                <span class="feed-badge feed-badge--safety">Safety</span>
                <time class="feed-item__time" datetime="2026-04-02T18:45">Apr 2, 6:45 PM</time>
            </div>
            <p class="feed-item__body"><strong>Sibuyan Expeditions</strong> · Mt. Guiting-Guiting — if wind gusts exceed comfort on the knife-edge, we <strong>shorten the segment</strong> and regroup at the last safe ledge.</p>
        </div>
        <div class="feed-item">
            <div class="feed-item__head">
                <span class="feed-badge feed-badge--general">General</span>
                <time class="feed-item__time" datetime="2026-04-01T11:10">Apr 1, 11:10 AM</time>
            </div>
            <p class="feed-item__body">Mindanao Ascents: Mt. Apo traverse still has <strong>2 spots</strong> — technical major pace, staged briefing May 17.</p>
        </div>
    </section>

    <section class="card card--stack glass-stack compose-panel">
        <h2 class="section-title">Post update <span class="text-muted" style="font-size:0.75rem;font-weight:400">(organizer sample)</span></h2>
        <label class="field-label" for="upd-event">Event</label>
        <select id="upd-event" class="input input--select">
            <option>Mt. Pulag · Akiki–Ambangeg — May 3</option>
            <option>Mt. Apo · Kapatagan–Kidapawan — May 18</option>
            <option>Mt. Guiting-Guiting · Knife-edge — Jun 1</option>
        </select>
        <label class="field-label" for="upd-type">Type</label>
        <select id="upd-type" class="input input--select">
            <option>General</option>
            <option>Weather</option>
            <option>Safety</option>
            <option>Meet point</option>
        </select>
        <label class="field-label" for="upd-body">Message</label>
        <textarea id="upd-body" class="input" rows="3" placeholder="Short note to approved hikers — e.g. ranger station closes registration at 4:00 PM."></textarea>
        <button type="button" class="btn-primary" style="margin-top:0.75rem">Publish to feed</button>
    </section>
</div>
<?php include 'partials/footer.php'; ?>
