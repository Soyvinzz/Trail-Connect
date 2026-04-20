<?php
declare(strict_types=1);
$pageTitle = 'Find hikes — TrailConnect';
$bodyClass = 'app-body';
include 'partials/header.php';
include 'partials/navbar.php';
?>
<div class="container container--app">
    <header class="page-head">
        <h1 class="page-title">Find hikes</h1>
        <p class="page-lede">Discover major hikes across the <strong>Philippines</strong> — Luzon ridges, Visayas technical lines, and Mindanao expeditions. Filter by region, difficulty, and date.</p>
    </header>

    <div class="card card--stack find-toolbar glass-stack">
        <label class="sr-only" for="search-hikes">Keyword search</label>
        <input id="search-hikes" class="input input--search" type="search" placeholder="Trail, province, or organizer…" name="q" form="find-filters">

        <form id="find-filters" class="filter-panel" action="index.php" method="get">
            <input type="hidden" name="page" value="find_hikes">
            <div class="filter-panel__row">
                <label class="field-label" for="loc">Region / area</label>
                <select id="loc" class="input input--select" name="loc">
                    <option>All Philippines</option>
                    <option>Luzon (Cordillera · CALABARZON)</option>
                    <option>Visayas (Romblon · Mindoro)</option>
                    <option>Mindanao (Davao · Bukidnon · Lanao)</option>
                    <option>Palawan</option>
                </select>
            </div>
            <div class="filter-panel__row filter-panel__row--split">
                <div>
                    <span class="field-label">Date range</span>
                    <div class="date-pair">
                        <input class="input" type="date" name="from" value="2026-04-01" aria-label="From date">
                        <span class="date-pair__to">to</span>
                        <input class="input" type="date" name="to" value="2026-06-30" aria-label="To date">
                    </div>
                </div>
            </div>
            <div class="filter-panel__row">
                <span class="field-label">Difficulty</span>
                <div class="chip-group">
                    <label class="chip"><input type="checkbox" name="diff[]" value="easy" checked> Easy</label>
                    <label class="chip"><input type="checkbox" name="diff[]" value="mod" checked> Moderate</label>
                    <label class="chip"><input type="checkbox" name="diff[]" value="hard" checked> Hard</label>
                    <label class="chip"><input type="checkbox" name="diff[]" value="vhard"> Very hard</label>
                </div>
            </div>
            <div class="filter-panel__row filter-panel__row--inline">
                <label class="toggle-line">
                    <input type="checkbox" name="open_only" value="1" checked>
                    <span>Open spots only</span>
                </label>
                <span class="filter-active-badge" title="Active filters">4 filters</span>
            </div>
        </form>

        <div class="sort-row">
            <span class="field-label">Sort</span>
            <label class="sort-option"><input type="radio" name="sort" value="date-asc" checked> Date ↑</label>
            <label class="sort-option"><input type="radio" name="sort" value="date-desc"> Date ↓</label>
            <label class="sort-option"><input type="radio" name="sort" value="rating"> Organizer rating</label>
        </div>
    </div>

    <div class="grid grid--events">
        <article class="event-card event-card--panel">
            <div class="event-card__top">
                <h2 class="event-card__trail">Mt. Pulag · Akiki–Ambangeg</h2>
                <span class="badge-diff badge-diff--hard">Hard</span>
            </div>
            <p class="event-card__org"><span class="stars">★★★★½</span> Cordillera Guides · 4.7</p>
            <p class="event-card__date">May 3, 2026 · Benguet / Ifugao · Sea of clouds</p>
            <p class="event-card__cap"><strong>6</strong> / 12 approved</p>
            <a class="btn-primary btn-primary--block" href="index.php?page=event_details">View details</a>
        </article>
        <article class="event-card event-card--panel">
            <div class="event-card__top">
                <h2 class="event-card__trail">Mt. Apo · Kapatagan–Kidapawan</h2>
                <span class="badge-diff badge-diff--hard">Hard</span>
            </div>
            <p class="event-card__org"><span class="stars">★★★★★</span> Mindanao Ascents · 4.9</p>
            <p class="event-card__date">May 18, 2026 · Davao / Cotabato · Highest peak PH</p>
            <p class="event-card__cap"><strong>4</strong> / 10 approved</p>
            <a class="btn-primary btn-primary--block" href="index.php?page=event_details">View details</a>
        </article>
        <article class="event-card event-card--panel event-card--full">
            <div class="event-card__top">
                <h2 class="event-card__trail">Mt. Guiting-Guiting · Knife-edge</h2>
                <span class="badge-diff badge-diff--vhard">Very hard</span>
                <span class="badge badge--full">Full</span>
            </div>
            <p class="event-card__org"><span class="stars">★★★★★</span> Sibuyan Expeditions · 5.0</p>
            <p class="event-card__date">Jun 1, 2026 · Romblon · Technical major</p>
            <p class="event-card__cap"><strong>8</strong> / 8 approved</p>
            <button class="btn-primary btn-primary--block" type="button" disabled>Full</button>
            <p class="event-card__hint">Waitlist opens for next season — one of the toughest climbs in the country.</p>
        </article>
    </div>

    <section class="trail-catalog" aria-label="Hike event reference">
        <h2 class="section-title">Hike events</h2>
        <p class="page-lede trail-catalog__intro">Major climbs across the Philippines — route types, commitment, and terrain at a glance.</p>

        <article class="trail-catalog__block">
            <h3 class="trail-catalog__title">Mt. Guiting-Guiting Knife-Edge Traverse</h3>
            <ul class="detail-list">
                <li><strong>Trail Name:</strong> Mt. Guiting-Guiting</li>
                <li><strong>Location:</strong> Romblon, Philippines</li>
                <li><strong>Elevation:</strong> 2058 MASL</li>
                <li><strong>Difficulty:</strong> Very Hard (Major Hike)</li>
                <li><strong>Duration:</strong> 3–4 days</li>
                <li><strong>Trail Class:</strong> Technical ridges, rock scrambling, exposure</li>
                <li><strong>Description:</strong> Considered one of the toughest climbs in the country with knife-edge ridges, steep rock sections, and very technical terrain suited only for experienced climbers.</li>
            </ul>
        </article>
        <article class="trail-catalog__block">
            <h3 class="trail-catalog__title">Mt. Halcon Technical Ascent</h3>
            <ul class="detail-list">
                <li><strong>Trail Name:</strong> Mt. Halcon</li>
                <li><strong>Location:</strong> Mindoro, Philippines</li>
                <li><strong>Elevation:</strong> 2582 MASL</li>
                <li><strong>Difficulty:</strong> Very Hard (Major Hike)</li>
                <li><strong>Duration:</strong> 3–4 days</li>
                <li><strong>Trail Class:</strong> Steep, mossy forests, river crossings</li>
                <li><strong>Description:</strong> Known for its long, punishing trail, multiple river crossings, and steep mossy forest sections, Mt. Halcon demands high fitness and preparation.</li>
            </ul>
        </article>
        <article class="trail-catalog__block">
            <h3 class="trail-catalog__title">Mt. Mantalingajan Knife-Edge Ridge</h3>
            <ul class="detail-list">
                <li><strong>Trail Name:</strong> Mt. Mantalingajan</li>
                <li><strong>Location:</strong> Palawan, Philippines</li>
                <li><strong>Elevation:</strong> 2085 MASL</li>
                <li><strong>Difficulty:</strong> Very Hard (Major Hike)</li>
                <li><strong>Duration:</strong> 4–5 days</li>
                <li><strong>Trail Class:</strong> Knife-edge ridges, limestone, exposed sections</li>
                <li><strong>Description:</strong> Remote and committing expedition-style climb with sharp ridges, limestone formations, and extended exposure making it suitable only for seasoned mountaineers.</li>
            </ul>
        </article>
        <article class="trail-catalog__block">
            <h3 class="trail-catalog__title">Mt. Apo Traverse via Kapatagan–Kidapawan</h3>
            <ul class="detail-list">
                <li><strong>Trail Name:</strong> Mt. Apo</li>
                <li><strong>Location:</strong> Davao / Cotabato, Philippines</li>
                <li><strong>Elevation:</strong> 2954 MASL</li>
                <li><strong>Difficulty:</strong> Hard (Major Hike)</li>
                <li><strong>Duration:</strong> 3 days</li>
                <li><strong>Trail Class:</strong> Boulder fields, sulfur vents, forest trails</li>
                <li><strong>Description:</strong> Highest peak in the Philippines featuring diverse terrain from mossy forests to boulder fields and sulfur vents, often done as a multi-day traverse.</li>
            </ul>
        </article>
        <article class="trail-catalog__block">
            <h3 class="trail-catalog__title">Mt. Dulang-Dulang–Mt. Kitanglad Traverse</h3>
            <ul class="detail-list">
                <li><strong>Trail Name:</strong> Mt. Dulang-Dulang / Mt. Kitanglad</li>
                <li><strong>Location:</strong> Bukidnon, Philippines</li>
                <li><strong>Elevation:</strong> 2938 MASL</li>
                <li><strong>Difficulty:</strong> Hard (Major Hike)</li>
                <li><strong>Duration:</strong> 3 days</li>
                <li><strong>Trail Class:</strong> Mossy forests, ridge walk, continuous ascent</li>
                <li><strong>Description:</strong> Popular double-peak traverse with enchanting mossy forests, long ridge walks, and sustained climbs ideal for strong intermediate to advanced hikers.</li>
            </ul>
        </article>
        <article class="trail-catalog__block">
            <h3 class="trail-catalog__title">Mt. Kalatungan Sweep</h3>
            <ul class="detail-list">
                <li><strong>Trail Name:</strong> Mt. Kalatungan</li>
                <li><strong>Location:</strong> Bukidnon, Philippines</li>
                <li><strong>Elevation:</strong> 2875 MASL</li>
                <li><strong>Difficulty:</strong> Hard (Major Hike)</li>
                <li><strong>Duration:</strong> 3 days</li>
                <li><strong>Trail Class:</strong> Remote, muddy, river crossings</li>
                <li><strong>Description:</strong> Challenging and remote climb with muddy trails, river crossings, and extended approach sections, often combined with other peaks in the Kalatungan range.</li>
            </ul>
        </article>
        <article class="trail-catalog__block">
            <h3 class="trail-catalog__title">Mt. Ragang Technical Volcano Ascent</h3>
            <ul class="detail-list">
                <li><strong>Trail Name:</strong> Mt. Ragang</li>
                <li><strong>Location:</strong> Lanao del Sur, Philippines</li>
                <li><strong>Elevation:</strong> 2815 MASL</li>
                <li><strong>Difficulty:</strong> Very Hard (Major Hike)</li>
                <li><strong>Duration:</strong> 3–4 days</li>
                <li><strong>Trail Class:</strong> Remote, steep, volcanic terrain</li>
                <li><strong>Description:</strong> An active and remote stratovolcano with limited access, steep inclines, and rugged volcanic terrain that requires advanced logistics and experience.</li>
            </ul>
        </article>
        <article class="trail-catalog__block">
            <h3 class="trail-catalog__title">Mt. Piapayungan Range Climb</h3>
            <ul class="detail-list">
                <li><strong>Trail Name:</strong> Mt. Piapayungan</li>
                <li><strong>Location:</strong> Mindanao, Philippines</li>
                <li><strong>Elevation:</strong> 2806 MASL</li>
                <li><strong>Difficulty:</strong> Very Hard (Major Hike)</li>
                <li><strong>Duration:</strong> 4 days</li>
                <li><strong>Trail Class:</strong> Dense jungle, little-established trail</li>
                <li><strong>Description:</strong> Part of a rugged range with minimal established trails, dense jungle, and logistical challenges making it one of the most demanding Mindanao climbs.</li>
            </ul>
        </article>
        <article class="trail-catalog__block">
            <h3 class="trail-catalog__title">Mt. Tabayoc Mossy Forest Ascent</h3>
            <ul class="detail-list">
                <li><strong>Trail Name:</strong> Mt. Tabayoc</li>
                <li><strong>Location:</strong> Benguet, Philippines</li>
                <li><strong>Elevation:</strong> 2842 MASL</li>
                <li><strong>Difficulty:</strong> Hard (Major Hike)</li>
                <li><strong>Duration:</strong> 2–3 days</li>
                <li><strong>Trail Class:</strong> Steep, mossy, rooty trail</li>
                <li><strong>Description:</strong> Steep mossy-forest ascent with root-laden trails and cool, often wet conditions, usually paired with nearby lakes and side trips in Kabayan.</li>
            </ul>
        </article>
        <article class="trail-catalog__block">
            <h3 class="trail-catalog__title">Mt. Pulag via Akiki–Ambangeg Traverse</h3>
            <ul class="detail-list">
                <li><strong>Trail Name:</strong> Mt. Pulag</li>
                <li><strong>Location:</strong> Benguet / Ifugao, Philippines</li>
                <li><strong>Elevation:</strong> 2922 MASL</li>
                <li><strong>Difficulty:</strong> Hard (Major Hike)</li>
                <li><strong>Duration:</strong> 2–3 days</li>
                <li><strong>Trail Class:</strong> Steep Akiki trail, grassland summit</li>
                <li><strong>Description:</strong> The Akiki–Ambangeg traverse combines a steep, taxing ascent with a more relaxed descent, showcasing the famous sea of clouds and grassland summit.</li>
            </ul>
        </article>
    </section>

</div>
<?php include 'partials/footer.php'; ?>
