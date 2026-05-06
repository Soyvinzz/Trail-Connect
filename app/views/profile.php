<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/paths.php';
$pageTitle = 'Profile — TrailConnect';
$bodyClass = 'app-body';
$profile = tc_profile();
$name = (string) ($profile['display_name'] ?? tc_display_name());
$role = tc_role();
$email = tc_current_user_email() !== '' ? tc_current_user_email() : 'you@example.com';
$avatar = tc_current_avatar_path();
$avatarUrl = $avatar !== '' ? $avatar : tc_peak_gallery_urls('pulag')[0];
$roleLabel = $role === 'organizer' ? 'Organizer' : 'Hiker';
$rolePill = $role === 'organizer' ? 'Event organizer' : 'Trail hiker';
$profileCompleteness = tc_profile_completeness($profile);
$bioText = trim((string) ($profile['bio'] ?? ''));
$coverImageUrl = tc_asset_url('assets/img/aponi.jpg');
$msg = (string) ($_GET['msg'] ?? '');
$error = (string) ($_GET['error'] ?? '');
$messages = [
    'profile_saved' => 'Profile updated successfully.',
    'profile_reset' => 'Profile reset to default values.',
    'avatar_saved' => 'Profile image uploaded successfully.',
];
$errors = [
    'avatar_missing' => 'Please choose an image before uploading.',
    'avatar_upload' => 'Upload failed. Please try again.',
    'avatar_size' => 'Image is too large. Maximum size is 5MB.',
    'avatar_type' => 'Unsupported image type. Use JPG, PNG, or WEBP.',
    'db' => 'Unable to save changes right now. Please try again.',
];
include 'partials/header.php';
include 'partials/navbar.php';
?>
<div class="container container--app container--landscape profile-shell">
    <header class="page-head page-head--profile">
        <p class="page-head__kicker">Your trail identity</p>
        <h1 class="page-title">Profile</h1>
        <p class="page-lede">Reputation and trail history across the <strong>Philippines</strong> — Cordillera ridges, Visayas technical lines, and Mindanao majors.</p>
    </header>
    <?php if (isset($messages[$msg])) : ?>
        <div class="banner-safety" role="status" style="margin-bottom:1rem">
            <strong>Notification:</strong> <?php echo htmlspecialchars($messages[$msg], ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>
    <?php if (isset($errors[$error])) : ?>
        <p class="form-error" role="alert" style="margin-bottom:1rem"><?php echo htmlspecialchars($errors[$error], ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>

    <section
        class="profile-card"
        aria-label="Your profile"
        style="--profile-cover: url('<?php echo htmlspecialchars($coverImageUrl, ENT_QUOTES, 'UTF-8'); ?>')"
    >
        <div class="profile-card__cover" role="img" aria-label="Landscape cover image"></div>

        <div class="profile-card__body">
            <div class="profile-card__hero">
                <div class="profile-card__avatar-block">
                    <div class="profile-card__avatar">
                        <img src="<?php echo htmlspecialchars($avatarUrl, ENT_QUOTES, 'UTF-8'); ?>" alt="Profile photo of <?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>" width="120" height="120" decoding="async">
                    </div>
                    <form class="profile-card__upload" method="post" action="index.php?page=profile" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="upload_avatar">
                        <label class="profile-card__upload-label" for="pf-avatar">Photo</label>
                        <input id="pf-avatar" class="input profile-card__file" type="file" name="avatar" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" required>
                        <button type="submit" class="btn-secondary btn-secondary--sm profile-card__upload-btn">Update photo</button>
                    </form>
                </div>

                <div class="profile-card__headline">
                    <div class="profile-card__name-row">
                        <h2 class="profile-card__name"><?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?></h2>
                        <span class="profile-card__role-pill"><?php echo htmlspecialchars($rolePill, ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <p class="profile-card__title"><?php echo htmlspecialchars($roleLabel, ENT_QUOTES, 'UTF-8'); ?></p>
                    <ul class="profile-card__meta">
                        <li class="profile-card__meta-item">
                            <span class="profile-card__meta-icon" aria-hidden="true">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            </span>
                            <?php echo htmlspecialchars((string) ($profile['home'] ?? 'Baguio City, Philippines'), ENT_QUOTES, 'UTF-8'); ?>
                        </li>
                        <li class="profile-card__meta-item">
                            <span class="profile-card__meta-icon" aria-hidden="true">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            </span>
                            <?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>
                        </li>
                    </ul>
                    <div class="profile-card__rep-row">
                        <p class="profile-card__rep">
                            <span class="stars" aria-label="4.5 stars">★★★★½</span>
                            <strong>4.5</strong> <span class="profile-card__rep-from">from 12 reviews</span>
                        </p>
                    </div>
                    <div class="profile-card__actions">
                        <button type="button" class="profile-card__btn profile-card__btn--primary" id="profile-edit-open">Edit profile</button>
                        <a href="index.php?page=settings" class="profile-card__btn">Account settings</a>
                    </div>
                </div>
            </div>

            <ul class="profile-stat-chips" aria-label="Profile highlights">
                <li class="profile-stat-chips__item">
                    <span class="profile-stat-chips__value"><?php echo (int) $profileCompleteness; ?>%</span>
                    <span class="profile-stat-chips__label">Profile strength</span>
                </li>
                <li class="profile-stat-chips__item">
                    <span class="profile-stat-chips__value">4.5</span>
                    <span class="profile-stat-chips__label">Community trust</span>
                </li>
                <li class="profile-stat-chips__item">
                    <span class="profile-stat-chips__value"><?php echo htmlspecialchars($role === 'organizer' ? 'Org' : 'Hiker', ENT_QUOTES, 'UTF-8'); ?></span>
                    <span class="profile-stat-chips__label">Active role</span>
                </li>
            </ul>

            <div class="profile-card__grid">
                <div class="profile-card__col profile-card__col--main">
                    <?php if ($bioText !== '') : ?>
                        <div class="profile-card__bio">
                            <h3 class="profile-card__bio-title">About</h3>
                            <p class="profile-card__bio-text"><?php echo nl2br(htmlspecialchars($bioText, ENT_QUOTES, 'UTF-8')); ?></p>
                        </div>
                    <?php else : ?>
                        <div class="profile-card__bio profile-card__bio--empty">
                            <h3 class="profile-card__bio-title">About</h3>
                            <p class="profile-card__bio-placeholder">Add a short bio so organizers and co-hikers know your experience and style.</p>
                            <button type="button" class="profile-card__linklike" id="profile-edit-open-bio">Write bio</button>
                        </div>
                    <?php endif; ?>
                    <div class="profile-card__progress-block">
                        <div class="capacity-bar" role="img" aria-label="Profile completeness <?php echo (int) $profileCompleteness; ?> percent">
                            <div class="capacity-bar__fill" style="width: <?php echo (int) $profileCompleteness; ?>%"></div>
                        </div>
                        <p class="capacity-bar__label">Complete your profile to unlock smoother joins and reviews.</p>
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
                    <?php if ($role === 'hiker') : ?>
                    <div class="profile-card__field">
                        <h3 class="profile-card__field-label">Hiker readiness</h3>
                        <div class="profile-card__tags">
                            <span><?php echo htmlspecialchars(tc_hiking_level_label((string) ($profile['hiking_level'] ?? '')), ENT_QUOTES, 'UTF-8'); ?></span>
                            <span><?php echo (int) ($profile['minor_hikes_completed'] ?? 0); ?> minor hikes</span>
                            <span><?php echo (int) ($profile['major_hikes_completed'] ?? 0); ?> major hikes</span>
                        </div>
                    </div>
                    <?php endif; ?>
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
            <form class="profile-edit__form" method="post" action="index.php?page=profile">
                <input type="hidden" name="action" value="save_profile">
                <label class="field-label" for="pf-name">Display name</label>
                <input id="pf-name" class="input" name="display_name" value="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>">

                <label class="field-label" for="pf-bio">Bio</label>
                <textarea id="pf-bio" class="input" name="bio" rows="3" placeholder="Weekend mountaineer — Cordillera sea of clouds and technical Visayas lines."><?php echo htmlspecialchars((string) ($profile['bio'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></textarea>

                <label class="field-label" for="pf-home">Home base</label>
                <input id="pf-home" class="input" name="home" value="<?php echo htmlspecialchars((string) ($profile['home'] ?? 'Baguio City, Philippines'), ENT_QUOTES, 'UTF-8'); ?>">

                <label class="field-label" for="pf-phone">Phone number</label>
                <input id="pf-phone" class="input" name="phone_number" value="<?php echo htmlspecialchars((string) ($profile['phone_number'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">

                <label class="field-label" for="pf-current-address">Current address</label>
                <input id="pf-current-address" class="input" name="current_address" value="<?php echo htmlspecialchars((string) ($profile['current_address'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">

                <label class="field-label" for="pf-level">Hiking level</label>
                <select id="pf-level" class="input input--select" name="hiking_level">
                    <?php $currentLevel = (string) ($profile['hiking_level'] ?? ''); ?>
                    <option value="" <?php echo $currentLevel === '' ? 'selected' : ''; ?>>Not set</option>
                    <option value="beginner" <?php echo $currentLevel === 'beginner' ? 'selected' : ''; ?>>Beginner</option>
                    <option value="minor" <?php echo $currentLevel === 'minor' ? 'selected' : ''; ?>>Minor hikes level</option>
                    <option value="intermediate" <?php echo $currentLevel === 'intermediate' ? 'selected' : ''; ?>>Intermediate</option>
                    <option value="advanced" <?php echo $currentLevel === 'advanced' ? 'selected' : ''; ?>>Advanced</option>
                </select>

                <label class="field-label" for="pf-minor-count">Minor hikes completed</label>
                <input id="pf-minor-count" class="input" type="number" min="0" step="1" name="minor_hikes_completed" value="<?php echo (int) ($profile['minor_hikes_completed'] ?? 0); ?>">

                <label class="field-label" for="pf-major-count">Major hikes completed</label>
                <input id="pf-major-count" class="input" type="number" min="0" step="1" name="major_hikes_completed" value="<?php echo (int) ($profile['major_hikes_completed'] ?? 0); ?>">

                <label class="field-label" for="pf-emergency-name">Emergency contact name</label>
                <input id="pf-emergency-name" class="input" name="emergency_contact_name" value="<?php echo htmlspecialchars((string) ($profile['emergency_contact_name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">

                <label class="field-label" for="pf-emergency-number">Emergency contact number</label>
                <input id="pf-emergency-number" class="input" name="emergency_contact_number" value="<?php echo htmlspecialchars((string) ($profile['emergency_contact_number'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">

                <label class="field-label" for="pf-medical-notes">Medical notes (optional)</label>
                <textarea id="pf-medical-notes" class="input" rows="3" name="medical_notes" placeholder="Allergies, asthma, medication reminders, injury history..."><?php echo htmlspecialchars((string) ($profile['medical_notes'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></textarea>

                <div class="inline-actions">
                    <button type="submit" class="btn-primary">Save changes</button>
                </div>
            </form>
            <form method="post" action="index.php?page=profile">
                <input type="hidden" name="action" value="delete_profile">
                <button type="submit" class="btn-secondary btn-secondary--sm">Reset profile</button>
            </form>
        </div>
    </details>

    <section class="card card--stack glass-stack profile-trails-card">
        <div class="profile-trails-card__head">
            <span class="profile-trails-card__icon" aria-hidden="true">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 3l4 8 5-5 5 15H2L8 3z"/></svg>
            </span>
            <h2 class="section-title profile-trails-card__title">Recent trails</h2>
        </div>
        <p class="profile-trails-card__lede">Highlights from your Philippines hiking journey.</p>
        <ul class="detail-list profile-trails-list">
            <li><strong>Mt. Pulag · Akiki–Ambangeg</strong><span class="profile-trails-list__meta">Benguet / Ifugao · Cordillera Guides</span></li>
            <li><strong>Mt. Tabayoc · Mossy forest</strong><span class="profile-trails-list__meta">Benguet · Kabayan</span></li>
            <li><strong>Mt. Halcon · Technical ascent</strong><span class="profile-trails-list__meta">Mindoro · Oriental Mindoro Peaks</span></li>
        </ul>
    </section>
</div>
<script>
(function () {
    var details = document.getElementById('profile-edit');
    var openBtn = document.getElementById('profile-edit-open');
    var closeBtn = document.getElementById('profile-edit-close');
    function openEdit() {
        if (!details) return;
        details.open = true;
        details.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        var nameInput = document.getElementById('pf-name');
        if (nameInput) {
            setTimeout(function () {
                nameInput.focus();
            }, 280);
        }
    }
    var openBio = document.getElementById('profile-edit-open-bio');
    if (details && openBtn) {
        openBtn.addEventListener('click', openEdit);
    }
    if (details && openBio) {
        openBio.addEventListener('click', openEdit);
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
