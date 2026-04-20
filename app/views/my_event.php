<?php
declare(strict_types=1);
$pageTitle = 'My events — TrailConnect';
$bodyClass = 'app-body';
$role = tc_role();
include 'partials/header.php';
include 'partials/navbar.php';
?>
<div class="container container--app">
    <header class="page-head">
        <h1 class="page-title">My events</h1>
        <p class="page-lede">
            <?php echo $role === 'organizer'
                ? 'Host major hikes anywhere in the Philippines — approve joiners, post updates, and keep capacity honest.'
                : 'Track Pulag, G2, Apo, and Mindanao expeditions — upcoming slots, pending approvals, and past badges.'; ?>
        </p>
    </header>

    <?php if ($role === 'hiker') : ?>
        <section class="card card--stack glass-stack tabs--css" aria-label="Your hikes">
            <input class="tabs__input" type="radio" name="me-hiker" id="ht-up" checked>
            <input class="tabs__input" type="radio" name="me-hiker" id="ht-pend">
            <input class="tabs__input" type="radio" name="me-hiker" id="ht-past">
            <div class="tabs__bar">
                <label class="tabs__tab" for="ht-up">Upcoming</label>
                <label class="tabs__tab" for="ht-pend">Pending</label>
                <label class="tabs__tab" for="ht-past">Past</label>
            </div>
            <div class="tabs__panels">
                <div class="tabs__panel tabs__panel--1">
                    <article class="event-row">
                        <div class="event-row__main">
                            <div class="event-row__top">
                                <h2 class="event-card__trail" style="margin:0">Mt. Pulag · Akiki–Ambangeg</h2>
                            </div>
                            <p class="request-card__who">Organizer <strong>Cordillera Guides</strong> · Benguet briefing point</p>
                            <p class="request-card__when">Sat, May 3, 2026 · staged meet</p>
                        </div>
                        <div class="inline-actions inline-actions--stack">
                            <span class="badge badge--approved">Approved</span>
                            <a class="btn-secondary" href="index.php?page=event_details">Details</a>
                            <a class="btn-secondary btn-secondary--sm" href="index.php?page=updates">Updates</a>
                        </div>
                    </article>
                    <article class="event-row" style="margin-top:1.25rem;padding-top:1.25rem;border-top:1px solid rgba(255,255,255,0.08)">
                        <div class="event-row__main">
                            <div class="event-row__top">
                                <h2 class="event-card__trail" style="margin:0">Mt. Apo · Kapatagan–Kidapawan</h2>
                            </div>
                            <p class="request-card__who"><strong>Mindanao Ascents</strong> · Davao / Cotabato</p>
                            <p class="request-card__when">Sun, May 18, 2026 · multi-day</p>
                        </div>
                        <div class="inline-actions inline-actions--stack">
                            <span class="badge badge--approved">Approved</span>
                            <a class="btn-secondary" href="index.php?page=event_details">Details</a>
                        </div>
                    </article>
                </div>
                <div class="tabs__panel tabs__panel--2">
                    <article class="event-row">
                        <div class="event-row__main">
                            <div class="event-row__top">
                                <h2 class="event-card__trail" style="margin:0">Mt. Halcon · Technical ascent</h2>
                            </div>
                            <p class="request-card__who">Host <strong>Oriental Mindoro Peaks</strong> · manual approval</p>
                            <p class="request-card__when">Requested Apr 2 · Event Jun 8</p>
                        </div>
                        <div class="event-row__rail">
                            <span class="badge badge--pending">Pending review</span>
                            <span class="text-muted" style="font-size:0.85rem">Wait for organizer</span>
                        </div>
                    </article>
                    <p class="field-hint" style="margin-top:1rem">Sample: auto-approve events flip you to <em>Approved</em> immediately.</p>
                </div>
                <div class="tabs__panel tabs__panel--3">
                    <article class="event-row">
                        <div class="event-row__main">
                            <div class="event-row__top">
                                <h2 class="event-card__trail" style="margin:0">Mt. Tabayoc · Mossy forest</h2>
                            </div>
                            <p class="request-card__who">Kabayan · Benguet · with Cordillera Guides</p>
                            <p class="request-card__when">Mar 8, 2026</p>
                        </div>
                        <div class="event-row__rail">
                            <span class="badge badge--done">Completed</span>
                            <a class="btn-secondary" href="index.php?page=reviews">Leave review</a>
                        </div>
                    </article>
                    <article class="event-row" style="margin-top:1.25rem;padding-top:1.25rem;border-top:1px solid rgba(255,255,255,0.08)">
                        <div class="event-row__main">
                            <div class="event-row__top">
                                <h2 class="event-card__trail" style="margin:0">Mt. Kalatungan sweep (sample)</h2>
                            </div>
                            <p class="request-card__when">Feb 14, 2026 · Bukidnon</p>
                        </div>
                        <div class="event-row__rail">
                            <span class="badge badge--done">Completed</span>
                            <span class="text-muted" style="font-size:0.85rem">Reviewed</span>
                        </div>
                    </article>
                </div>
            </div>
        </section>
    <?php else : ?>
        <section class="card card--stack glass-stack tabs--css" aria-label="Organizer events">
            <input class="tabs__input" type="radio" name="me-org" id="ot-mine" checked>
            <input class="tabs__input" type="radio" name="me-org" id="ot-appr">
            <div class="tabs__bar">
                <label class="tabs__tab" for="ot-mine">My published</label>
                <label class="tabs__tab" for="ot-appr">Join requests</label>
            </div>
            <div class="tabs__panels">
                <div class="tabs__panel tabs__panel--1">
                    <article class="event-row">
                        <div class="event-row__main">
                            <div class="event-row__top">
                                <h2 class="event-card__trail" style="margin:0">Mt. Pulag · Akiki–Ambangeg batch</h2>
                                <span class="badge-diff badge-diff--hard">Hard</span>
                            </div>
                            <p class="request-card__when">May 3 · 6 / 12 approved · Benguet / Ifugao</p>
                        </div>
                        <div class="inline-actions">
                            <a class="btn-secondary" href="index.php?page=event_details">View</a>
                            <a class="text-link" href="index.php?page=updates">Post update</a>
                        </div>
                    </article>
                    <article class="event-row" style="margin-top:1.25rem;padding-top:1.25rem;border-top:1px solid rgba(255,255,255,0.08)">
                        <div class="event-row__main">
                            <div class="event-row__top">
                                <h2 class="event-card__trail" style="margin:0">Mt. Guiting-Guiting · Knife-edge roster</h2>
                                <span class="badge-diff badge-diff--vhard">Very hard</span>
                            </div>
                            <p class="request-card__when">Jun 1 · 8 / 8 approved · Romblon</p>
                        </div>
                        <a class="btn-secondary" href="index.php?page=create_event">Duplicate as new</a>
                    </article>
                </div>
                <div class="tabs__panel tabs__panel--2">
                    <article class="request-card card card--inset" style="margin-bottom:1rem">
                        <p class="request-card__who"><strong>Alex Reyes</strong> wants Mt. Pulag · Akiki–Ambangeg batch</p>
                        <p class="request-card__when">Requested today · 2 mutual hikes on TrailConnect</p>
                        <div class="inline-actions" style="margin-top:0.75rem">
                            <button type="button" class="btn-primary">Approve</button>
                            <button type="button" class="btn-secondary">Decline</button>
                        </div>
                    </article>
                    <article class="request-card card card--inset">
                        <p class="request-card__who"><strong>Jam Santos</strong> · Mt. Guiting-Guiting · Knife-edge roster</p>
                        <p class="request-card__when">Gear checklist acknowledged · yesterday</p>
                        <div class="inline-actions" style="margin-top:0.75rem">
                            <button type="button" class="btn-primary">Approve</button>
                            <button type="button" class="btn-secondary">Decline</button>
                        </div>
                    </article>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <p class="text-center" style="margin-top:1.5rem">
        <a class="btn-secondary btn-secondary--sm" href="index.php?page=find_hikes">Find more hikes</a>
        <?php if ($role === 'organizer') : ?>
            · <a class="btn-secondary btn-secondary--sm" href="index.php?page=create_event">Create event</a>
        <?php endif; ?>
    </p>
</div>
<?php include 'partials/footer.php'; ?>
