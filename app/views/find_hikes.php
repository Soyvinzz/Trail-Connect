<?php
declare(strict_types=1);
$pageTitle = 'Find hikes — TrailConnect';
$bodyClass = 'app-body';
$allEvents = array_values(tc_events());
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
            <article class="event-card event-card--panel<?php echo $event['is_full'] ? ' event-card--full' : ''; ?>">
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
