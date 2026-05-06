<?php
declare(strict_types=1);

$pageTitle = 'Hiking 101 — TrailConnect';
$bodyClass = 'app-body';
include 'partials/header.php';
include 'partials/navbar.php';
?>
<main class="page-wrap">
    <section class="container container--app">
        <header class="page-head">
            <p class="kicker">Hiking Basics</p>
            <h1 class="page-title">Hiking 101</h1>
            <p class="page-lede">Learn the essentials before your next trail day: preparation, pacing, safety, and trail respect.</p>
        </header>

        <section class="grid hiking101-grid">
            <article class="card glass-stack hiking101-quick">
                <p class="hiking101-quick__icon" aria-hidden="true">🥾</p>
                <h2 class="hiking101-quick__title">Trail Ready Gear</h2>
                <p class="hiking101-quick__text">Shoes, water, rain layer, light, first aid, and energy snacks before every climb.</p>
            </article>
            <article class="card glass-stack hiking101-quick">
                <p class="hiking101-quick__icon" aria-hidden="true">🧭</p>
                <h2 class="hiking101-quick__title">Plan Your Route</h2>
                <p class="hiking101-quick__text">Know elevation, expected pace, turnaround time, and emergency contact points.</p>
            </article>
            <article class="card glass-stack hiking101-quick">
                <p class="hiking101-quick__icon" aria-hidden="true">🌿</p>
                <h2 class="hiking101-quick__title">Leave No Trace</h2>
                <p class="hiking101-quick__text">Respect trails and wildlife. Pack out everything you bring in.</p>
            </article>
        </section>

        <article class="card glass-stack card--stack">
            <h2 class="section-title">Preparation Checklist</h2>
            <ul class="detail-list">
                <li>Choose a trail based on your current fitness, weather window, and daylight time.</li>
                <li>Share your route and expected return time with a trusted contact.</li>
                <li>Pack water, trail food, rain layer, basic first aid, and emergency light.</li>
                <li>Wear broken-in footwear and carry layers for sudden temperature shifts.</li>
            </ul>
        </article>

        <article class="card glass-stack card--stack">
            <h2 class="section-title">How Difficulty Works</h2>
            <p class="card-lede">Trail difficulty is a mix of elevation, terrain, distance, and your own conditioning.</p>
            <ul class="detail-list">
                <li><strong>Beginner:</strong> short routes, moderate gradient, predictable trail surface.</li>
                <li><strong>Minor:</strong> longer duration with mixed terrain and basic navigation awareness.</li>
                <li><strong>Intermediate:</strong> sustained climbs, technical sections, tighter pacing requirements.</li>
                <li><strong>Advanced:</strong> steep or exposed terrain requiring experience, endurance, and discipline.</li>
            </ul>
        </article>

        <article class="card glass-stack card--stack safety-first">
            <h2 class="section-title safety-first__title">Safety First</h2>
            <ul class="detail-list">
                <li>Hike at your own sustainable pace and communicate early if you need a regroup.</li>
                <li>Stay on established trails and follow local park or mountain regulations.</li>
                <li>Practice Leave No Trace: bring out all trash and avoid disturbing wildlife.</li>
                <li>Turn back when conditions become unsafe; summit is optional, safe return is mandatory.</li>
            </ul>
        </article>

        <article class="card glass-stack card--stack">
            <h2 class="section-title">Starter Progress Plan</h2>
            <ul class="detail-list">
                <li>Begin with guided day hikes and focus on consistency over speed.</li>
                <li>Build base cardio and leg strength weekly before trying harder climbs.</li>
                <li>Track completed minor and major hikes to see readiness progression.</li>
                <li>Review post-hike notes: hydration, pacing, gear, and recovery.</li>
            </ul>
            <p class="card-lede">Use this page as your quick reference before joining an event from <a class="text-link" href="index.php?page=find_hikes">Find Hikes</a>.</p>
        </article>

        <section class="hiking101-split card--stack">
            <article class="card glass-stack">
                <h2 class="section-title">Quick Safety Rules</h2>
                <div class="grid hiking101-rules">
                    <div class="hiking101-rule">
                        <strong>Hydrate early</strong>
                        <p>Do not wait until you are thirsty. Drink small amounts regularly.</p>
                    </div>
                    <div class="hiking101-rule">
                        <strong>Watch weather</strong>
                        <p>If clouds, wind, or rain rapidly worsen, adjust your plan immediately.</p>
                    </div>
                    <div class="hiking101-rule">
                        <strong>Respect your pace</strong>
                        <p>Consistent pacing beats sprint-rest cycles and reduces injury risk.</p>
                    </div>
                    <div class="hiking101-rule">
                        <strong>Group discipline</strong>
                        <p>Keep visual contact and communicate breaks, symptoms, or route concerns.</p>
                    </div>
                </div>
            </article>

            <aside class="card glass-stack hiking101-checklist" aria-label="Before you hike today checklist">
                <h2 class="section-title">Before You Hike Today</h2>
                <p class="card-lede">Tick each item before leaving your jump-off point.</p>
                <label class="hiking101-check">
                    <input type="checkbox" data-h101-check value="water_food">
                    <span>Water and trail food packed</span>
                </label>
                <label class="hiking101-check">
                    <input type="checkbox" data-h101-check value="weather_route">
                    <span>Weather and route checked</span>
                </label>
                <label class="hiking101-check">
                    <input type="checkbox" data-h101-check value="contact_informed">
                    <span>Emergency contact informed</span>
                </label>
                <label class="hiking101-check">
                    <input type="checkbox" data-h101-check value="first_aid_light">
                    <span>First-aid and light ready</span>
                </label>
                <label class="hiking101-check">
                    <input type="checkbox" data-h101-check value="turnaround_time">
                    <span>Turnaround time decided</span>
                </label>
            </aside>
        </section>
    </section>
</main>
<script>
(function () {
    var checks = document.querySelectorAll('[data-h101-check]');
    if (!checks.length) return;
    var key = 'tc_hiking101_checklist';
    var saved = {};
    try {
        saved = JSON.parse(localStorage.getItem(key) || '{}') || {};
    } catch (e) {
        saved = {};
    }

    checks.forEach(function (item) {
        var name = item.value || '';
        if (name !== '' && saved[name] === true) {
            item.checked = true;
        }
        item.addEventListener('change', function () {
            var next = {};
            checks.forEach(function (input) {
                var inputName = input.value || '';
                if (inputName !== '') {
                    next[inputName] = !!input.checked;
                }
            });
            localStorage.setItem(key, JSON.stringify(next));
        });
    });
})();
</script>
<?php include 'partials/footer.php'; ?>
