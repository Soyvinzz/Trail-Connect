<?php
declare(strict_types=1);
$pageTitle = 'Find hikes — TrailConnect';
$bodyClass = 'app-body';
<<<<<<< HEAD
$allEvents = array_values(tc_events_published());
=======
$allEvents = array_values(tc_events());
>>>>>>> d32810119b58bc9e2967e699ffb7232a7c867b55
$allRequests = array_values(tc_join_requests());

$q = trim((string) ($_GET['q'] ?? ''));
$loc = (string) ($_GET['loc'] ?? 'All Philippines');
$from = (string) ($_GET['from'] ?? '2026-04-01');
$to = (string) ($_GET['to'] ?? '2026-06-30');
$diff = $_GET['diff'] ?? ['easy', 'mod', 'hard'];
if (!is_array($diff)) {
    $diff = ['easy', 'mod', 'hard'];
}
$sort = (string) ($_GET['sort'] ?? 'date-asc');
$openOnly = (string) ($_GET['open_only'] ?? '1') === '1';

$regionNeedles = [
    'All Philippines' => [],
    'Luzon (Cordillera · CALABARZON)' => ['benguet', 'ifugao', 'luzon', 'cordillera', 'calabarzon'],
    'Visayas (Romblon · Mindoro)' => ['romblon', 'mindoro', 'visayas'],
    'Mindanao (Davao · Bukidnon · Lanao)' => ['mindanao', 'davao', 'bukidnon', 'lanao', 'cotabato'],
    'Palawan' => ['palawan'],
];
$difficultyLabels = [
    'easy' => 'EASY',
    'mod' => 'MODERATE',
    'hard' => 'HARD',
    'vhard' => 'VERY HARD',
];

$events = [];
foreach ($allEvents as $event) {
    $eventDate = (string) ($event['date'] ?? '');
    $eventDiff = (string) ($event['difficulty'] ?? '');
    $haystack = strtolower(
        (string) ($event['title'] ?? '') . ' ' .
        (string) ($event['trail'] ?? '') . ' ' .
        (string) ($event['organizer'] ?? '')
    );

    $approvedCount = 0;
    foreach ($allRequests as $request) {
        if ((int) $request['event_id'] === (int) $event['id'] && (string) $request['status'] === 'approved') {
            $approvedCount++;
        }
    }
    $event['approved_count'] = $approvedCount;
    $event['is_full'] = $approvedCount >= (int) $event['max'];

    if ($q !== '' && strpos($haystack, strtolower($q)) === false) {
        continue;
    }
    if (!in_array($eventDiff, $diff, true)) {
        continue;
    }
    if ($from !== '' && $eventDate !== '' && $eventDate < $from) {
        continue;
    }
    if ($to !== '' && $eventDate !== '' && $eventDate > $to) {
        continue;
    }
    if ($openOnly && $event['is_full']) {
        continue;
    }
    $needles = $regionNeedles[$loc] ?? [];
    if ($needles !== []) {
        $matchedRegion = false;
        foreach ($needles as $needle) {
            if (strpos($haystack, $needle) !== false) {
                $matchedRegion = true;
                break;
            }
        }
        if (!$matchedRegion) {
            continue;
        }
    }

    $events[] = $event;
}

usort($events, static function (array $a, array $b) use ($sort): int {
    if ($sort === 'date-desc') {
        return strcmp((string) ($b['date'] ?? ''), (string) ($a['date'] ?? ''));
    }
    if ($sort === 'rating') {
        return strcmp((string) ($a['organizer'] ?? ''), (string) ($b['organizer'] ?? ''));
    }

    return strcmp((string) ($a['date'] ?? ''), (string) ($b['date'] ?? ''));
});

$activeFilterCount = 0;
if ($q !== '') {
    $activeFilterCount++;
}
if ($loc !== 'All Philippines') {
    $activeFilterCount++;
}
if ($from !== '2026-04-01' || $to !== '2026-06-30') {
    $activeFilterCount++;
}
if (count($diff) < 4) {
    $activeFilterCount++;
}
if ($openOnly) {
    $activeFilterCount++;
}
if ($sort !== 'date-asc') {
    $activeFilterCount++;
}
include 'partials/header.php';
include 'partials/navbar.php';
$currentReturn = (string) ($_SERVER['REQUEST_URI'] ?? 'index.php?page=find_hikes');
?>
<div class="container container--app">
    <header class="page-head">
        <h1 class="page-title">Find hikes</h1>
        <p class="page-lede">Discover major hikes across the <strong>Philippines</strong> — Luzon ridges, Visayas technical lines, and Mindanao expeditions. Filter by region, difficulty, and date.</p>
    </header>

    <div class="card card--stack find-toolbar glass-stack">
        <label class="sr-only" for="search-hikes">Keyword search</label>
        <input id="search-hikes" class="input input--search" type="search" placeholder="Trail, province, or organizer…" name="q" form="find-filters" value="<?php echo htmlspecialchars($q, ENT_QUOTES, 'UTF-8'); ?>">

        <form id="find-filters" class="filter-panel" action="index.php" method="get">
            <input type="hidden" name="page" value="find_hikes">
            <div class="filter-panel__row">
                <label class="field-label" for="loc">Region / area</label>
                <select id="loc" class="input input--select" name="loc">
                    <?php foreach (array_keys($regionNeedles) as $locOption) : ?>
                        <option value="<?php echo htmlspecialchars($locOption, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $loc === $locOption ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($locOption, ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-panel__row filter-panel__row--split">
                <div>
                    <span class="field-label">Date range</span>
                    <div class="date-pair">
                        <input class="input" type="date" name="from" value="<?php echo htmlspecialchars($from, ENT_QUOTES, 'UTF-8'); ?>" aria-label="From date">
                        <span class="date-pair__to">to</span>
                        <input class="input" type="date" name="to" value="<?php echo htmlspecialchars($to, ENT_QUOTES, 'UTF-8'); ?>" aria-label="To date">
                    </div>
                </div>
            </div>
            <div class="filter-panel__row">
                <span class="field-label">Difficulty</span>
                <div class="chip-group">
                    <label class="chip"><input type="checkbox" name="diff[]" value="easy" <?php echo in_array('easy', $diff, true) ? 'checked' : ''; ?>> Easy</label>
                    <label class="chip"><input type="checkbox" name="diff[]" value="mod" <?php echo in_array('mod', $diff, true) ? 'checked' : ''; ?>> Moderate</label>
                    <label class="chip"><input type="checkbox" name="diff[]" value="hard" <?php echo in_array('hard', $diff, true) ? 'checked' : ''; ?>> Hard</label>
                    <label class="chip"><input type="checkbox" name="diff[]" value="vhard" <?php echo in_array('vhard', $diff, true) ? 'checked' : ''; ?>> Very hard</label>
                </div>
            </div>
            <div class="filter-panel__row filter-panel__row--inline">
                <label class="toggle-line">
                    <input type="checkbox" name="open_only" value="1" <?php echo $openOnly ? 'checked' : ''; ?>>
                    <span>Open spots only</span>
                </label>
                <span class="filter-active-badge" title="Active filters"><?php echo $activeFilterCount; ?> filters</span>
            </div>
            <div class="sort-row">
                <span class="field-label">Sort</span>
                <label class="sort-option"><input type="radio" name="sort" value="date-asc" <?php echo $sort === 'date-asc' ? 'checked' : ''; ?>> Date ↑</label>
                <label class="sort-option"><input type="radio" name="sort" value="date-desc" <?php echo $sort === 'date-desc' ? 'checked' : ''; ?>> Date ↓</label>
                <label class="sort-option"><input type="radio" name="sort" value="rating" <?php echo $sort === 'rating' ? 'checked' : ''; ?>> Organizer rating</label>
            </div>
            <div class="inline-actions find-toolbar__actions">
                <button type="submit" class="btn-primary btn-primary--sm">Apply filters</button>
                <a class="btn-secondary btn-secondary--sm" href="index.php?page=find_hikes">Reset</a>
            </div>
        </form>
    </div>

    <div class="grid grid--events">
        <?php if (empty($events)) : ?>
            <article class="event-card event-card--panel event-card--full">
                <div class="event-card__top">
                    <h2 class="event-card__trail">No hikes found</h2>
                </div>
                <p class="event-card__hint">Adjust your filters to view available hikes.</p>
            </article>
        <?php endif; ?>
        <?php foreach ($events as $event) : ?>
<<<<<<< HEAD
            <?php $cardTrailUrls = tc_trail_image_urls($event); ?>
            <article class="event-card event-card--panel<?php echo $event['is_full'] ? ' event-card--full' : ''; ?>">
                <div class="event-card__gallery" aria-hidden="true">
                    <?php foreach ($cardTrailUrls as $ci => $cardTrailImg) : ?>
                        <div class="event-card__gallery-cell">
                            <img class="event-card__thumb" src="<?php echo htmlspecialchars($cardTrailImg, ENT_QUOTES, 'UTF-8'); ?>" alt="" width="320" height="180" loading="<?php echo $ci === 0 ? 'eager' : 'lazy'; ?>" decoding="async">
                        </div>
                    <?php endforeach; ?>
                </div>
=======
            <article class="event-card event-card--panel<?php echo $event['is_full'] ? ' event-card--full' : ''; ?>">
>>>>>>> d32810119b58bc9e2967e699ffb7232a7c867b55
                <div class="event-card__top">
                    <h2 class="event-card__trail"><?php echo htmlspecialchars((string) $event['title'], ENT_QUOTES, 'UTF-8'); ?></h2>
                    <?php $difficultyKey = (string) ($event['difficulty'] ?? 'mod'); ?>
                    <span class="badge-diff badge-diff--<?php echo htmlspecialchars($difficultyKey, ENT_QUOTES, 'UTF-8'); ?>">
                        <?php echo htmlspecialchars($difficultyLabels[$difficultyKey] ?? strtoupper($difficultyKey), ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                    <?php if ($event['is_full']) : ?><span class="badge badge--full">Full</span><?php endif; ?>
                </div>
                <p class="event-card__org"><?php echo htmlspecialchars((string) $event['organizer'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p class="event-card__date"><?php echo htmlspecialchars((string) $event['date'], ENT_QUOTES, 'UTF-8'); ?> · <?php echo htmlspecialchars((string) $event['trail'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p class="event-card__cap"><strong><?php echo (int) $event['approved_count']; ?></strong> / <?php echo (int) $event['max']; ?> approved</p>
                <a class="btn-primary btn-primary--block" href="index.php?page=event_details&event_id=<?php echo (int) $event['id']; ?>&return=<?php echo urlencode($currentReturn); ?>">View details</a>
            </article>
        <?php endforeach; ?>
    </div>

    <section class="trail-catalog" aria-label="Hike event reference">
        <h2 class="section-title">Hike events</h2>
        <p class="page-lede trail-catalog__intro">Major climbs across the Philippines — route types, commitment, and terrain at a glance.</p>

        <article class="trail-catalog__block">
            <?php $__cat = 'Mt. Guiting-Guiting Knife-Edge Traverse'; ?>
            <div class="trail-catalog__gallery" aria-hidden="true">
                <?php foreach (tc_trail_catalog_image_urls($__cat) as $__i => $__u) : ?>
                    <div class="trail-catalog__gallery-cell">
                        <img class="trail-catalog__photo" src="<?php echo htmlspecialchars($__u, ENT_QUOTES, 'UTF-8'); ?>" alt="" width="400" height="240" loading="lazy" decoding="async">
                    </div>
                <?php endforeach; ?>
            </div>
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
            <?php $__cat = 'Mt. Halcon Technical Ascent'; ?>
            <div class="trail-catalog__gallery" aria-hidden="true">
                <?php foreach (tc_trail_catalog_image_urls($__cat) as $__i => $__u) : ?>
                    <div class="trail-catalog__gallery-cell">
                        <img class="trail-catalog__photo" src="<?php echo htmlspecialchars($__u, ENT_QUOTES, 'UTF-8'); ?>" alt="" width="400" height="240" loading="lazy" decoding="async">
                    </div>
                <?php endforeach; ?>
            </div>
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
            <?php $__cat = 'Mt. Mantalingajan Knife-Edge Ridge'; ?>
            <div class="trail-catalog__gallery" aria-hidden="true">
                <?php foreach (tc_trail_catalog_image_urls($__cat) as $__i => $__u) : ?>
                    <div class="trail-catalog__gallery-cell">
                        <img class="trail-catalog__photo" src="<?php echo htmlspecialchars($__u, ENT_QUOTES, 'UTF-8'); ?>" alt="" width="400" height="240" loading="lazy" decoding="async">
                    </div>
                <?php endforeach; ?>
            </div>
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
            <?php $__cat = 'Mt. Apo Traverse via Kapatagan–Kidapawan'; ?>
            <div class="trail-catalog__gallery" aria-hidden="true">
                <?php foreach (tc_trail_catalog_image_urls($__cat) as $__i => $__u) : ?>
                    <div class="trail-catalog__gallery-cell">
                        <img class="trail-catalog__photo" src="<?php echo htmlspecialchars($__u, ENT_QUOTES, 'UTF-8'); ?>" alt="" width="400" height="240" loading="lazy" decoding="async">
                    </div>
                <?php endforeach; ?>
            </div>
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
            <?php $__cat = 'Mt. Dulang-Dulang–Mt. Kitanglad Traverse'; ?>
            <div class="trail-catalog__gallery" aria-hidden="true">
                <?php foreach (tc_trail_catalog_image_urls($__cat) as $__i => $__u) : ?>
                    <div class="trail-catalog__gallery-cell">
                        <img class="trail-catalog__photo" src="<?php echo htmlspecialchars($__u, ENT_QUOTES, 'UTF-8'); ?>" alt="" width="400" height="240" loading="lazy" decoding="async">
                    </div>
                <?php endforeach; ?>
            </div>
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
            <?php $__cat = 'Mt. Kalatungan Sweep'; ?>
            <div class="trail-catalog__gallery" aria-hidden="true">
                <?php foreach (tc_trail_catalog_image_urls($__cat) as $__i => $__u) : ?>
                    <div class="trail-catalog__gallery-cell">
                        <img class="trail-catalog__photo" src="<?php echo htmlspecialchars($__u, ENT_QUOTES, 'UTF-8'); ?>" alt="" width="400" height="240" loading="lazy" decoding="async">
                    </div>
                <?php endforeach; ?>
            </div>
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
            <?php $__cat = 'Mt. Ragang Technical Volcano Ascent'; ?>
            <div class="trail-catalog__gallery" aria-hidden="true">
                <?php foreach (tc_trail_catalog_image_urls($__cat) as $__i => $__u) : ?>
                    <div class="trail-catalog__gallery-cell">
                        <img class="trail-catalog__photo" src="<?php echo htmlspecialchars($__u, ENT_QUOTES, 'UTF-8'); ?>" alt="" width="400" height="240" loading="lazy" decoding="async">
                    </div>
                <?php endforeach; ?>
            </div>
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
            <?php $__cat = 'Mt. Piapayungan Range Climb'; ?>
            <div class="trail-catalog__gallery" aria-hidden="true">
                <?php foreach (tc_trail_catalog_image_urls($__cat) as $__i => $__u) : ?>
                    <div class="trail-catalog__gallery-cell">
                        <img class="trail-catalog__photo" src="<?php echo htmlspecialchars($__u, ENT_QUOTES, 'UTF-8'); ?>" alt="" width="400" height="240" loading="lazy" decoding="async">
                    </div>
                <?php endforeach; ?>
            </div>
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
            <?php $__cat = 'Mt. Tabayoc Mossy Forest Ascent'; ?>
            <div class="trail-catalog__gallery" aria-hidden="true">
                <?php foreach (tc_trail_catalog_image_urls($__cat) as $__i => $__u) : ?>
                    <div class="trail-catalog__gallery-cell">
                        <img class="trail-catalog__photo" src="<?php echo htmlspecialchars($__u, ENT_QUOTES, 'UTF-8'); ?>" alt="" width="400" height="240" loading="lazy" decoding="async">
                    </div>
                <?php endforeach; ?>
            </div>
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
            <?php $__cat = 'Mt. Pulag via Akiki–Ambangeg Traverse'; ?>
            <div class="trail-catalog__gallery" aria-hidden="true">
                <?php foreach (tc_trail_catalog_image_urls($__cat) as $__i => $__u) : ?>
                    <div class="trail-catalog__gallery-cell">
                        <img class="trail-catalog__photo" src="<?php echo htmlspecialchars($__u, ENT_QUOTES, 'UTF-8'); ?>" alt="" width="400" height="240" loading="lazy" decoding="async">
                    </div>
                <?php endforeach; ?>
            </div>
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

        <h2 class="section-title trail-catalog__minor-heading">Minor hikes</h2>
        <p class="page-lede trail-catalog__intro">Day hikes and approachable summits — same trail facts layout as the majors above.</p>

        <article class="trail-catalog__block">
            <?php $__cat = 'Mt. Mandalagan · Tinagong Dagat sector'; ?>
            <div class="trail-catalog__gallery" aria-hidden="true">
                <?php foreach (tc_trail_catalog_image_urls($__cat) as $__i => $__u) : ?>
                    <div class="trail-catalog__gallery-cell">
                        <img class="trail-catalog__photo" src="<?php echo htmlspecialchars($__u, ENT_QUOTES, 'UTF-8'); ?>" alt="" width="400" height="240" loading="lazy" decoding="async">
                    </div>
                <?php endforeach; ?>
            </div>
            <h3 class="trail-catalog__title">Mt. Mandalagan · Tinagong Dagat sector</h3>
            <ul class="detail-list">
                <li><strong>Trail Name:</strong> Mt. Mandalagan</li>
                <li><strong>Location:</strong> Negros Occidental, Philippines</li>
                <li><strong>Elevation:</strong> ~1885 MASL (volcanic massif)</li>
                <li><strong>Difficulty:</strong> Moderate (Minor Hike)</li>
                <li><strong>Duration:</strong> 1–2 days (typical Tinagong Dagat / crater-lake circuits)</li>
                <li><strong>Trail Class:</strong> Mossy forest, volcanic plateau, seasonal crater lake</li>
                <li><strong>Description:</strong> Popular Negros dayhike and weekend objective featuring dramatic volcanic scenery and the flooded crater basin known as Tinagong Dagat during wet months.</li>
            </ul>
        </article>
        <article class="trail-catalog__block">
            <?php $__cat = 'Mt. Lingguhob · Leon ridge'; ?>
            <div class="trail-catalog__gallery" aria-hidden="true">
                <?php foreach (tc_trail_catalog_image_urls($__cat) as $__i => $__u) : ?>
                    <div class="trail-catalog__gallery-cell">
                        <img class="trail-catalog__photo" src="<?php echo htmlspecialchars($__u, ENT_QUOTES, 'UTF-8'); ?>" alt="" width="400" height="240" loading="lazy" decoding="async">
                    </div>
                <?php endforeach; ?>
            </div>
            <h3 class="trail-catalog__title">Mt. Lingguhob · Leon ridge</h3>
            <ul class="detail-list">
                <li><strong>Trail Name:</strong> Mt. Lingguhob</li>
                <li><strong>Location:</strong> Iloilo (Western Visayas), Philippines</li>
                <li><strong>Elevation:</strong> ~1207 MASL</li>
                <li><strong>Difficulty:</strong> Moderate (Minor Hike)</li>
                <li><strong>Duration:</strong> 1 day</li>
                <li><strong>Trail Class:</strong> Ridge walks, mixed farmland and pine-upland transitions nearby</li>
                <li><strong>Description:</strong> Accessible Panay ridge hike suited to hikers building endurance; pairs well with Leon–Bucari upland scenery and cool ridge breezes.</li>
            </ul>
        </article>
        <article class="trail-catalog__block">
            <?php $__cat = 'Mt. Talinis · Cuernos de Negros'; ?>
            <div class="trail-catalog__gallery" aria-hidden="true">
                <?php foreach (tc_trail_catalog_image_urls($__cat) as $__i => $__u) : ?>
                    <div class="trail-catalog__gallery-cell">
                        <img class="trail-catalog__photo" src="<?php echo htmlspecialchars($__u, ENT_QUOTES, 'UTF-8'); ?>" alt="" width="400" height="240" loading="lazy" decoding="async">
                    </div>
                <?php endforeach; ?>
            </div>
            <h3 class="trail-catalog__title">Mt. Talinis · Cuernos de Negros</h3>
            <ul class="detail-list">
                <li><strong>Trail Name:</strong> Mt. Talinis (Cuernos de Negros)</li>
                <li><strong>Location:</strong> Negros Oriental, Philippines</li>
                <li><strong>Elevation:</strong> ~1862 MASL</li>
                <li><strong>Difficulty:</strong> Moderate–Hard (Minor Hike entry routes)</li>
                <li><strong>Duration:</strong> 1–2 days (lake circuits / shorter guided options)</li>
                <li><strong>Trail Class:</strong> Volcanic slopes, crater lakes (e.g. Balinsasayao–Danao), mossy segments</li>
                <li><strong>Description:</strong> Second-highest mountain on Negros and a flagship Dumaguete-area objective; twin-lake approaches are often used as the “minor” friendly circuit before longer volcanic traverses.</li>
            </ul>
        </article>
        <article class="trail-catalog__block">
            <?php $__cat = 'Mt. Igatmon · Igbaras limestone'; ?>
            <div class="trail-catalog__gallery" aria-hidden="true">
                <?php foreach (tc_trail_catalog_image_urls($__cat) as $__i => $__u) : ?>
                    <div class="trail-catalog__gallery-cell">
                        <img class="trail-catalog__photo" src="<?php echo htmlspecialchars($__u, ENT_QUOTES, 'UTF-8'); ?>" alt="" width="400" height="240" loading="lazy" decoding="async">
                    </div>
                <?php endforeach; ?>
            </div>
            <h3 class="trail-catalog__title">Mt. Igatmon · Igbaras limestone</h3>
            <ul class="detail-list">
                <li><strong>Trail Name:</strong> Mt. Igatmon</li>
                <li><strong>Location:</strong> Igbaras, Iloilo, Philippines</li>
                <li><strong>Elevation:</strong> ~823 MASL</li>
                <li><strong>Difficulty:</strong> Easy–Moderate (Minor Hike)</li>
                <li><strong>Duration:</strong> 1 day</li>
                <li><strong>Trail Class:</strong> Limestone outcrops, short scrambles, open summit blocks</li>
                <li><strong>Description:</strong> Compact Panay dayhike known for sharp limestone scenery and big ridgeline views—ideal for hikers graduating from flat trails to short scrambles.</li>
            </ul>
        </article>
        <article class="trail-catalog__block">
            <?php $__cat = 'Mt. Daat · Davao de Oro uplands'; ?>
            <div class="trail-catalog__gallery" aria-hidden="true">
                <?php foreach (tc_trail_catalog_image_urls($__cat) as $__i => $__u) : ?>
                    <div class="trail-catalog__gallery-cell">
                        <img class="trail-catalog__photo" src="<?php echo htmlspecialchars($__u, ENT_QUOTES, 'UTF-8'); ?>" alt="" width="400" height="240" loading="lazy" decoding="async">
                    </div>
                <?php endforeach; ?>
            </div>
            <h3 class="trail-catalog__title">Mt. Daat · Davao de Oro uplands</h3>
            <ul class="detail-list">
                <li><strong>Trail Name:</strong> Mt. Daat</li>
                <li><strong>Location:</strong> Davao de Oro, Mindanao, Philippines</li>
                <li><strong>Elevation:</strong> ~1040 MASL</li>
                <li><strong>Difficulty:</strong> Easy–Moderate (Minor Hike)</li>
                <li><strong>Duration:</strong> 1 day</li>
                <li><strong>Trail Class:</strong> Montane forest paths, moderate slopes, river crossings possible</li>
                <li><strong>Description:</strong> Short Mindanao upland trek suited to groups practicing jungle pacing and Leave No Trace—popular as a skills-building outing before bigger Davao Region climbs.</li>
            </ul>
        </article>
    </section>

</div>
<?php include 'partials/footer.php'; ?>
