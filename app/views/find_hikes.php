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
        <p class="page-lede">Search trails in <strong>Negros Occidental</strong> — Bacolod jump-offs, Murcia · Mambukal, Silay · Patag, DSB, Victorias &amp; the northern highlands.</p>
    </header>

    <div class="card card--stack find-toolbar glass-stack">
        <label class="sr-only" for="search-hikes">Keyword search</label>
        <input id="search-hikes" class="input input--search" type="search" placeholder="Trail, description, or organizer…" name="q" form="find-filters">

        <form id="find-filters" class="filter-panel" action="index.php" method="get">
            <input type="hidden" name="page" value="find_hikes">
            <div class="filter-panel__row">
                <label class="field-label" for="loc">Location</label>
                <select id="loc" class="input input--select" name="loc">
                    <option>All Negros Occidental</option>
                    <option>Bacolod &amp; nearby</option>
                    <option>Murcia · Mambukal</option>
                    <option>Silay · Patag</option>
                    <option>Don Salvador Benedicto</option>
                    <option>Victorias · Manapla coast</option>
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
                    <label class="chip"><input type="checkbox" name="diff[]" value="hard"> Hard</label>
                </div>
            </div>
            <div class="filter-panel__row filter-panel__row--inline">
                <label class="toggle-line">
                    <input type="checkbox" name="open_only" value="1" checked>
                    <span>Open spots only</span>
                </label>
                <span class="filter-active-badge" title="Active filters">3 filters</span>
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
                <h2 class="event-card__trail">Patag plateau loop</h2>
                <span class="badge-diff badge-diff--mod">Moderate</span>
            </div>
            <p class="event-card__org"><span class="stars">★★★★½</span> Mara Villanueva · 4.6</p>
            <p class="event-card__date">Apr 12, 2026 · 8:00 AM · Silay side</p>
            <p class="event-card__cap"><strong>5</strong> / 12 approved</p>
            <a class="btn-primary btn-primary--block" href="index.php?page=event_details">View details</a>
        </article>
        <article class="event-card event-card--panel">
            <div class="event-card__top">
                <h2 class="event-card__trail">DSB pine ridge</h2>
                <span class="badge-diff badge-diff--hard">Hard</span>
            </div>
            <p class="event-card__org"><span class="stars">★★★★★</span> Rico Magbanua · 4.9</p>
            <p class="event-card__date">Apr 18, 2026 · 6:30 AM · Cool highland air</p>
            <p class="event-card__cap"><strong>9</strong> / 10 approved</p>
            <a class="btn-primary btn-primary--block" href="index.php?page=event_details">View details</a>
        </article>
        <article class="event-card event-card--panel event-card--full">
            <div class="event-card__top">
                <h2 class="event-card__trail">Gawahon eco loop</h2>
                <span class="badge-diff badge-diff--easy">Easy</span>
                <span class="badge badge--full">Full</span>
            </div>
            <p class="event-card__org"><span class="stars">★★★★☆</span> Victorias Trail Club · 4.2</p>
            <p class="event-card__date">Apr 22, 2026 · 5:00 PM</p>
            <p class="event-card__cap"><strong>8</strong> / 8 approved</p>
            <button class="btn-primary btn-primary--block" type="button" disabled>Full</button>
            <p class="event-card__hint">Stay listed for organizer discovery — similar hikes may open soon.</p>
        </article>
    </div>
</div>
<?php include 'partials/footer.php'; ?>
