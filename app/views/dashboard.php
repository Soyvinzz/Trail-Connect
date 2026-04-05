<?php
declare(strict_types=1);
$pageTitle = 'Home — TrailConnect';
$bodyClass = 'app-body dash-body';
$role = tc_role();
$name = tc_display_name();
include 'partials/header.php';
include 'partials/navbar.php';

// Card data — swap with DB queries when ready
$hikerCards = [
  [
    'label'   => 'Upcoming',
    'title'   => 'Patag plateau loop',
    'meta'    => 'Moderate · Sat, Apr 12 · 8:00 AM',
    'diff'    => 'mod',
    'org'     => '★★★★½ Mara Villanueva · Silay',
    'cta'     => ['text' => 'View details', 'href' => 'index.php?page=event_details', 'type' => 'primary'],
    'img'     => 'assets/img/mt-kalatungan.jpg',
  ],
  [
    'label'   => 'Upcoming',
    'title'   => 'Mambukal falls trail',
    'meta'    => 'Easy · Sun, Apr 20 · 9:30 AM',
    'diff'    => 'easy',
    'org'     => '★★★★☆ Ian Concepcion · Murcia',
    'cta'     => ['text' => 'View details', 'href' => 'index.php?page=event_details', 'type' => 'primary'],
    'img'     => 'assets/img/mountain1.jpg',
  ],
  [
    'label'   => 'Discover',
    'title'   => 'DSB pine ridge',
    'meta'    => 'Hard · Don Salvador Benedicto · 6 spots left',
    'diff'    => 'hard',
    'org'     => '',
    'cta'     => ['text' => 'Browse hikes', 'href' => 'index.php?page=find_hikes', 'type' => 'secondary'],
    'img'     => 'assets/img/mt-mayon.jpg',
  ],
  [
    'label'   => 'Discover',
    'title'   => 'Gawahon eco loop',
    'meta'    => 'Easy · Victorias · Open spots',
    'diff'    => 'easy',
    'org'     => '',
    'cta'     => ['text' => 'Browse hikes', 'href' => 'index.php?page=find_hikes', 'type' => 'secondary'],
    'img'     => 'assets/img/mountain-landingpage.jpg',
  ],
];

$organizerCards = [
  [
    'label'   => 'Organizer',
    'title'   => 'Pending requests',
    'meta'    => '2 new · Patag & Mambukal',
    'diff'    => '',
    'org'     => 'Review join requests before the weekend rush.',
    'cta'     => ['text' => 'Review now', 'href' => 'index.php?page=my_event', 'type' => 'primary'],
    'img'     => 'assets/img/mt-kalatungan.jpg',
  ],
  [
    'label'   => 'Organizer',
    'title'   => 'Create new event',
    'meta'    => 'Publish a Negros Occ. hike in three steps.',
    'diff'    => '',
    'org'     => '',
    'cta'     => ['text' => 'Create event', 'href' => 'index.php?page=create_event', 'type' => 'primary'],
    'img'     => 'assets/img/mountain1.jpg',
  ],
  [
    'label'   => 'Calendar',
    'title'   => 'Upcoming organized',
    'meta'    => 'Sat 12 · Patag · Sun 20 · Mambukal · May 3 · DSB draft',
    'diff'    => '',
    'org'     => '',
    'cta'     => ['text' => 'View updates', 'href' => 'index.php?page=updates', 'type' => 'secondary'],
    'img'     => 'assets/img/mt-mayon.jpg',
  ],
];

$cards   = $role === 'hiker' ? $hikerCards : $organizerCards;
$jsCards = json_encode(array_values($cards));
?>
<!-- ═══════════════════════ CINEMATIC DASHBOARD ═══════════════════════ -->
<div class="dash-cinematic" id="dashCinematic">

  <!-- Dynamic backgrounds (one per card) -->
  <div class="dash-bgs" id="dashBgs">
    <?php foreach ($cards as $i => $c): ?>
      <div class="dash-bg <?= $i === 0 ? 'is-active' : '' ?>" data-pos="<?= (int) $i ?>"
          style="background-image:url('<?= htmlspecialchars($c['img']) ?>')"></div>
    <?php endforeach; ?>
  </div>
  <div class="dash-grain"></div>

  <!-- ── Hero (no panel wrap; type colors match app pages) ── -->
  <div class="dash-hero-stack">
    <div class="dash-hero-content">
      <div class="dash-kicker" id="dashKicker">Negros Occidental</div>

      <h1 class="dash-page-title" id="dashTitle">
        <span class="dash-page-title__greet">Welcome back,</span>
        <span class="dash-page-title__name"><?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?></span>
      </h1>

      <p class="dash-page-lede" id="dashLede">
        <?php echo $role === 'organizer'
          ? 'Manage Patag, Mambukal, DSB, and Gawahon hikes — see requests at a glance and publish new weekends.'
          : 'Swipe through upcoming Negros hikes — Patag ridges, Mambukal mist, DSB pines, and Gawahon eco loops.'; ?>
      </p>

      <p class="dash-role-line">
        <span>Preview UI:</span>
        <a href="index.php?toggle_role=1">Switch to <?php echo $role === 'hiker' ? 'Organizer' : 'Hiker'; ?> dashboard</a>
      </p>
    </div>

    <?php if ($role === 'hiker'): ?>
    <div class="dash-notice">
      <strong>Pending requests</strong> — You have
      <a href="index.php?page=my_event">1 join request</a>
      awaiting organizer review (Granite Ridge sample).
    </div>
    <?php endif; ?>
  </div>

  <!-- ── Slider + controls (bottom) ── -->
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

</div><!-- /.dash-cinematic -->

<!-- Spec map — hidden in production; set display:block for dev reference -->
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

  // Fix height based on real header measurement
  const headerEl = document.querySelector('.site-header');
  if (headerEl) {
    const hh = headerEl.offsetHeight;
    cinEl.style.height = `calc(100dvh - ${hh}px)`;
  }

  totEl.textContent = String(N).padStart(2, '0');

  /* ── Build card strip ── */
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
            d.diff === 'mod' ? 'Moderate' : d.diff === 'easy' ? 'Easy' : 'Hard'
          }</span>`
        : '';

      // Strip diff word from meta to avoid duplication
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

  /* ── Swap backgrounds ── */
  function updateBg() {
    bgsEl.querySelectorAll('.dash-bg').forEach((b) => {
      const pos = parseInt(b.getAttribute('data-pos') || '0', 10);
      b.classList.toggle('is-active', pos === idx);
    });
  }

  /* ── Animate kicker / lede ── */
  function updateText(d) {
    [kickerEl, ledeEl].forEach(el => {
      el.classList.remove('tc-txt-enter');
      void el.offsetWidth;
      el.classList.add('tc-txt-exit');
    });
    setTimeout(() => {
      kickerEl.textContent = 'Negros Occidental · ' + d.label;
      ledeEl.textContent   = d.title + ' — ' + d.meta.replace(/<[^>]*>/g, '');
      [kickerEl, ledeEl].forEach(el => {
        el.classList.remove('tc-txt-exit');
        void el.offsetWidth;
        el.classList.add('tc-txt-enter');
      });
    }, 310);
  }

  /* ── NEXT ── */
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

  /* ── PREV ── */
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