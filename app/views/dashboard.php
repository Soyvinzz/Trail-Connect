<?php
declare(strict_types=1);
$pageTitle = 'Home — TrailConnect';
$bodyClass = 'app-body dash-body';
$role = tc_role();
$name = tc_display_name();
$pendingMine = 0;
$pendingAll = 0;
foreach (tc_join_requests() as $request) {
  if ((string) ($request['status'] ?? '') === 'pending') {
    $pendingAll++;
    if ((string) ($request['hiker_name'] ?? '') === $name) {
      $pendingMine++;
    }
  }
}
include 'partials/header.php';
include 'partials/navbar.php';


$hikerCards = [
  [
    'label'   => 'Upcoming',
    'title'   => 'Mt. Guiting-Guiting Knife-Edge',
    'meta'    => 'Very hard · Romblon · Jun 1 · 4 days',
    'diff'    => 'vhard',
    'org'     => '★★★★★ Sibuyan Expeditions',
    'cta'     => ['text' => 'View details', 'href' => 'index.php?page=event_details', 'type' => 'primary'],
    'img'     => 'assets/img/mt-kalatungan.jpg',
  ],
  [
    'label'   => 'Upcoming',
    'title'   => 'Mt. Pulag · Akiki–Ambangeg',
    'meta'    => 'Hard · Benguet / Ifugao · May 3 · 3 days',
    'diff'    => 'hard',
    'org'     => '★★★★½ Cordillera Guides',
    'cta'     => ['text' => 'View details', 'href' => 'index.php?page=event_details', 'type' => 'primary'],
    'img'     => 'assets/img/mountain1.jpg',
  ],
  [
    'label'   => 'Discover',
    'title'   => 'Mt. Apo Traverse',
    'meta'    => 'Hard · Davao / Cotabato · Highest peak PH · spots open',
    'diff'    => 'hard',
    'org'     => '',
    'cta'     => ['text' => 'Browse hikes', 'href' => 'index.php?page=find_hikes', 'type' => 'secondary'],
    'img'     => 'assets/img/mt-mayon.jpg',
  ],
  [
    'label'   => 'Discover',
    'title'   => 'Mt. Halcon Technical Ascent',
    'meta'    => 'Very hard · Mindoro · Major hike · waitlist',
    'diff'    => 'vhard',
    'org'     => '',
    'cta'     => ['text' => 'Browse hikes', 'href' => 'index.php?page=find_hikes', 'type' => 'secondary'],
    'img'     => 'assets/img/mountain-landingpage.jpg',
  ],
];

$organizerCards = [
  [
    'label'   => 'Organizer',
    'title'   => 'Pending requests',
    'meta'    => $pendingAll . ' pending · review join requests',
    'diff'    => '',
    'org'     => 'Review join requests for technical majors and traverse slots.',
    'cta'     => ['text' => 'Review now', 'href' => 'index.php?page=my_event', 'type' => 'primary'],
    'img'     => 'assets/img/mt-kalatungan.jpg',
  ],
  [
    'label'   => 'Organizer',
    'title'   => 'Create new event',
    'meta'    => 'Publish a Philippines hike in three steps.',
    'diff'    => '',
    'org'     => '',
    'cta'     => ['text' => 'Create event', 'href' => 'index.php?page=create_event', 'type' => 'primary'],
    'img'     => 'assets/img/mountain1.jpg',
  ],
  [
    'label'   => 'Calendar',
    'title'   => 'Upcoming organized',
    'meta'    => 'May · Pulag · Jun · G2 · Jul · Apo traverse draft',
    'diff'    => '',
    'org'     => '',
    'cta'     => ['text' => 'View updates', 'href' => 'index.php?page=updates', 'type' => 'secondary'],
    'img'     => 'assets/img/mt-mayon.jpg',
  ],
  [
    'label'   => 'Organizer',
    'title'   => 'Published events',
    'meta'    => count(tc_events()) . ' active events · monitor and update details',
    'diff'    => '',
    'org'     => '',
    'cta'     => ['text' => 'Manage events', 'href' => 'index.php?page=my_event', 'type' => 'secondary'],
    'img'     => 'assets/img/mountain-landingpage.jpg',
  ],
];

$cards   = $role === 'hiker' ? $hikerCards : $organizerCards;
$jsCards = json_encode(array_values($cards));
?>

<div class="dash-cinematic" id="dashCinematic">

  <!-- Dynamic backgrounds (one per card) -->
  <div class="dash-bgs" id="dashBgs">
    <?php foreach ($cards as $i => $c): ?>
      <div class="dash-bg <?= $i === 0 ? 'is-active' : '' ?>" data-pos="<?= (int) $i ?>"
          style="background-image:url('<?= htmlspecialchars($c['img']) ?>')"></div>
    <?php endforeach; ?>
  </div>
  <div class="dash-grain"></div>


  <div class="dash-hero-stack">
    <div class="dash-hero-content">
      <div class="dash-kicker" id="dashKicker">Philippines</div>

      <h1 class="dash-page-title" id="dashTitle">
        <span class="dash-page-title__greet">Welcome back,</span>
        <span class="dash-page-title__name"><?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?></span>
      </h1>

      <p class="dash-page-lede" id="dashLede">
        <?php echo $role === 'organizer'
          ? 'Coordinate Pulag, G2, Apo, and Halcon expeditions — see requests at a glance and publish new climbs nationwide.'
          : 'Swipe through major Philippines hikes — Cordillera sea of clouds, Sibuyan knife-edges, Mindanao traverses, and more.'; ?>
      </p>

      <p class="dash-role-line">
        <span>Preview UI:</span>
        <a href="index.php?toggle_role=1">Switch to <?php echo $role === 'hiker' ? 'Organizer' : 'Hiker'; ?> dashboard</a>
      </p>
    </div>

    <?php if ($role === 'hiker'): ?>
    <div class="dash-notice">
      <strong>Pending requests</strong> — You have
      <a href="index.php?page=my_event"><?php echo $pendingMine; ?> join request<?php echo $pendingMine === 1 ? '' : 's'; ?></a>
      awaiting organizer review.
    </div>
    <?php endif; ?>
  </div>

  <div class="dash-bottom" id="dashBottom">
    <div class="dash-slider-wrap" id="dashSliderWrap">
      <div class="dash-track" id="dashTrack"></div>
      <div class="dash-progress">
        <div class="dash-progress__fill" id="dashFill"></div>
      </div>
    </div>

    <div class="dash-controls">
      <div class="dash-counter">
        <span class="cur" id="dashCur">01</span> /
        <span id="dashTot">01</span>
      </div>
      <div class="dash-nav-btns">
        <button class="dash-nav-btn" id="dashPrev" aria-label="Previous slide">&#8592;</button>
        <button class="dash-nav-btn" id="dashNext" aria-label="Next slide">&#8594;</button>
      </div>
    </div>
  </div>

</div>


<div class="container container--app proto-map card glass-stack" aria-label="Specification screen index">
  <h2 class="section-title">All spec screens (quick links)</h2>
  <ol class="prototype-map__list">
    <li><strong>1</strong> <a href="index.php?page=login">Login</a> · <strong>2</strong> <a href="index.php?page=register">Register</a>
        <span class="text-muted">(log out to try login)</span></li>
    <li><strong>3</strong> <a href="index.php?page=dashboard">Dashboard</a> — switch role above.</li>
    <li><strong>4</strong> <a href="index.php?page=find_hikes">Find hikes</a></li>
    <li><strong>5</strong> <a href="index.php?page=event_details">Event details</a></li>
    <li><strong>6</strong> <a href="index.php?page=create_event">Create event</a> <span class="text-muted">(organizer)</span></li>
    <li><strong>7</strong> <a href="index.php?page=my_event">My events</a></li>
    <li><strong>8</strong> <a href="index.php?page=updates">Event updates</a></li>
    <li><strong>9</strong> <a href="index.php?page=profile">Profile</a> · <a href="index.php?page=settings">Settings</a></li>
    <li><strong>10</strong> <a href="index.php?page=reviews">Reviews</a></li>
  </ol>
</div>

<script>
(function () {
  'use strict';
  const CARDS = <?= $jsCards ?>;
  const N      = CARDS.length;
  const MAX    = 5;
  let idx      = 0;
  let busy     = false;

  const trackEl  = document.getElementById('dashTrack');
  const fillEl   = document.getElementById('dashFill');
  const curEl    = document.getElementById('dashCur');
  const totEl    = document.getElementById('dashTot');
  const bgsEl    = document.getElementById('dashBgs');
  const kickerEl = document.getElementById('dashKicker');
  const ledeEl   = document.getElementById('dashLede');
  const cinEl    = document.getElementById('dashCinematic');


  const headerEl = document.querySelector('.site-header');
  if (headerEl) {
    const hh = headerEl.offsetHeight;
    cinEl.style.height = `calc(100dvh - ${hh}px)`;
  }

  totEl.textContent = String(N).padStart(2, '0');

  function buildCards(exitClone) {
    trackEl.innerHTML = '';
    if (exitClone) trackEl.appendChild(exitClone);

    for (let i = 0; i < Math.min(MAX, N); i++) {
      const dIdx = (idx + i) % N;
      const d    = CARDS[dIdx];
      const el   = document.createElement('div');
      el.className  = 'dash-card';
      el.dataset.pos  = i;
      el.dataset.dIdx = dIdx;

      const chip = d.diff
        ? `<span class="dc-chip dc-chip--${d.diff}">${
            d.diff === 'mod' ? 'Moderate'
              : d.diff === 'easy' ? 'Easy'
              : d.diff === 'vhard' ? 'Very hard'
              : 'Hard'
          }</span>`
        : '';


      const cleanMeta = d.meta.replace(/^(Easy|Moderate|Hard)\s[·•]\s?/i, '');

      el.innerHTML = `
        <img class="dash-card__img" src="${d.img}" alt="${d.title}" loading="lazy"/>
        <div class="dash-card__overlay"></div>
        <div class="dash-card__badge">${d.label}</div>
        <div class="dash-card__body">
          <h2 class="dash-card__title">${d.title}</h2>
          <p class="dash-card__meta">${chip}${chip ? cleanMeta : d.meta}</p>
        </div>
      `;

      el.addEventListener('click', () => {
        const steps = (dIdx - idx + N) % N;
        if (steps > 0) goNext(steps);
      });

      trackEl.appendChild(el);
    }

    fillEl.style.width = ((idx + 1) / N * 100) + '%';
    curEl.textContent  = String(idx + 1).padStart(2, '0');
  }


  function updateBg() {
    bgsEl.querySelectorAll('.dash-bg').forEach((b) => {
      const pos = parseInt(b.getAttribute('data-pos') || '0', 10);
      b.classList.toggle('is-active', pos === idx);
    });
  }

  function updateText(d) {
    [kickerEl, ledeEl].forEach(el => {
      el.classList.remove('tc-txt-enter');
      void el.offsetWidth;
      el.classList.add('tc-txt-exit');
    });
    setTimeout(() => {
      kickerEl.textContent = 'Philippines · ' + d.label;
      ledeEl.textContent   = d.title + ' — ' + d.meta.replace(/<[^>]*>/g, '');
      [kickerEl, ledeEl].forEach(el => {
        el.classList.remove('tc-txt-exit');
        void el.offsetWidth;
        el.classList.add('tc-txt-enter');
      });
    }, 310);
  }


  function goNext(steps) {
    if (busy) return;
    busy = true;

    const heroCard = trackEl.querySelector('.dash-card[data-pos="0"]');
    let exitClone  = null;
    if (heroCard) {
      exitClone = heroCard.cloneNode(true);
      exitClone.dataset.pos   = 'exit';
      exitClone.style.cssText += `position:absolute;left:${heroCard.offsetLeft}px;bottom:0;z-index:20;`;
    }

    idx = (idx + steps) % N;
    updateText(CARDS[idx]);
    updateBg();
    buildCards(exitClone);

    setTimeout(() => {
      if (exitClone && exitClone.parentNode) exitClone.remove();
      busy = false;
    }, 720);
  }

  
  function goPrev() {
    if (busy) return;
    busy = true;
    idx  = (idx - 1 + N) % N;
    updateText(CARDS[idx]);
    updateBg();
    buildCards();
    setTimeout(() => { busy = false; }, 680);
  }

  document.getElementById('dashNext').addEventListener('click', () => goNext(1));
  document.getElementById('dashPrev').addEventListener('click', () => goPrev());
  document.addEventListener('keydown', e => {
    if (e.key === 'ArrowRight') goNext(1);
    if (e.key === 'ArrowLeft')  goPrev();
  });

  buildCards();
}());
</script>

<?php include 'partials/footer.php'; ?>