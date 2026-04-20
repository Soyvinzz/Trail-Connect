<?php
declare(strict_types=1);
$pageTitle = 'Profile — TrailConnect';
$bodyClass = 'app-body';
$name = tc_display_name();
$role = tc_role();
$roleLabel = $role === 'organizer' ? 'Organizer' : 'Hiker';
$rolePill = $role === 'organizer' ? 'Event organizer' : 'Trail hiker';
include 'partials/header.php';
include 'partials/navbar.php';
?>
<div class="container container--app container--landscape">
    <header class="page-head">
        <h1 class="page-title">Profile</h1>
        <p class="page-lede">Reputation and trail history across the <strong>Philippines</strong> — from Cordillera traverses to Mindanao majors.</p>
    </header>

    <section class="profile-card" aria-label="Your profile">
        <div class="profile-card__banner" aria-hidden="true"></div>

        <div class="profile-card__body">
            <div class="profile-card__avatar-wrap">
                <div class="profile-card__avatar" aria-hidden="true">
                    <span class="profile-card__avatar-ph">Photo</span>
                </div>
            </div>

            <div class="profile-card__grid">
                <div class="profile-card__col profile-card__col--main">
                    <h2 class="profile-card__name"><?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?></h2>
                    <p class="profile-card__title"><?php echo htmlspecialchars($roleLabel, ENT_QUOTES, 'UTF-8'); ?></p>
                    <p class="profile-card__location">Baguio City, Philippines</p>
                    <p class="profile-card__email">you@example.com</p>
                    <p class="profile-card__rep">
                        <span class="stars" aria-label="4.5 stars">★★★★½</span>
                        <strong>4.5</strong> <span class="profile-card__rep-from">from 12 reviews</span>
                    </p>
                    <div class="profile-card__actions">
                        <button type="button" class="profile-card__btn" id="profile-edit-open">Edit profile</button>
                        <a href="index.php?page=settings" class="profile-card__btn">Settings</a>
                    </div>
                </div>

                <div class="profile-card__col profile-card__col--side">
                    <div class="profile-card__field">
                        <h3 class="profile-card__field-label">
                            <span class="profile-card__field-icon" aria-hidden="true">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                            </span>
                            Current role
                        </h3>
                        <span class="profile-card__tag"><?php echo htmlspecialchars($rolePill, ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div class="profile-card__field">
                        <h3 class="profile-card__field-label">
                            <span class="profile-card__field-icon" aria-hidden="true">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                            </span>
                            Interests
                        </h3>
                        <div class="profile-card__tags">
                            <span>Major hikes</span>
                            <span>Pulag</span>
                            <span>G2</span>
                            <span>Reviews</span>
                            <span>Philippines</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="profile-card__quick-row">
                <a class="profile-quick" href="index.php?page=find_hikes">
                    <div class="profile-quick__text">
                        <strong class="profile-quick__title">Ready for trails</strong>
                        <span class="profile-quick__desc">18 hikes joined — find your next ridge or eco loop.</span>
                    </div>
                    <span class="profile-quick__arrow" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </span>
                </a>
                <a class="profile-quick" href="index.php?page=reviews">
                    <div class="profile-quick__text">
                        <strong class="profile-quick__title">Share trail stories</strong>
                        <span class="profile-quick__desc">9 reviews written — help others choose hikes.</span>
                    </div>
                    <span class="profile-quick__arrow" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </span>
                </a>
                <a class="profile-quick" href="index.php?page=my_event">
                    <div class="profile-quick__text">
                        <strong class="profile-quick__title">Your events</strong>
                        <span class="profile-quick__desc">6 organized nationwide — manage joiners &amp; updates.</span>
                    </div>
                    <span class="profile-quick__arrow" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </span>
                </a>
            </div>
        </div>
    </section>

    <details class="profile-edit" id="profile-edit">
        <summary class="sr-only">Edit profile details</summary>
        <div class="profile-edit__panel card card--stack glass-stack">
            <div class="profile-edit__head">
                <h2 class="profile-edit__title">Edit profile details</h2>
                <button type="button" class="profile-edit__close" id="profile-edit-close" aria-label="Close edit form">Close</button>
            </div>
            <form class="profile-edit__form" method="get" action="index.php">
                <input type="hidden" name="page" value="profile">
                <label class="field-label" for="pf-name">Display name</label>
                <input id="pf-name" class="input" name="display_name" value="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>">

                <label class="field-label" for="pf-bio">Bio</label>
                <textarea id="pf-bio" class="input" name="bio" rows="3" placeholder="Weekend mountaineer — Cordillera sea of clouds and technical Visayas lines."></textarea>

                <label class="field-label" for="pf-home">Home base</label>
                <input id="pf-home" class="input" name="home" value="Baguio City, Philippines">

                <button type="button" class="btn-primary">Save changes</button>
            </form>
        </div>
    </details>

    <section class="card card--stack glass-stack">
        <h2 class="section-title">Recent trails</h2>
        <ul class="detail-list">
            <li><strong>Mt. Pulag · Akiki–Ambangeg</strong> — Benguet / Ifugao · with Cordillera Guides</li>
            <li><strong>Mt. Tabayoc · Mossy forest</strong> — Benguet · Kabayan</li>
            <li><strong>Mt. Halcon · Technical ascent</strong> — Mindoro · Oriental Mindoro Peaks</li>
        </ul>
    </section>
</div>
<script>
(function () {
    var details = document.getElementById('profile-edit');
    var openBtn = document.getElementById('profile-edit-open');
    var closeBtn = document.getElementById('profile-edit-close');
    if (details && openBtn) {
    openBtn.addEventListener('click', function () {
        details.open = true;
        details.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        var nameInput = document.getElementById('pf-name');
        if (nameInput) {
        setTimeout(function () {
            nameInput.focus();
        }, 280);
        }
    });
    }
    if (details && closeBtn) {
    closeBtn.addEventListener('click', function () {
        details.open = false;
        if (openBtn) {
        openBtn.focus();
        }
    });
    }
})();
</script>
<?php include 'partials/footer.php'; ?>
