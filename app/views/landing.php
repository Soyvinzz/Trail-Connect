<?php
$pageTitle = 'TrailConnect — Explore';
$bodyClass = 'landing-body';

require_once __DIR__ . '/../includes/paths.php';

$heroImageUrl = tc_asset_url('assets/img/aponi.jpg');

include 'partials/header.php';
?>
<main class="landing" aria-label="Welcome">
    <div class="landing__media" aria-hidden="true">
        <img
            class="landing__photo"
            src="<?php echo htmlspecialchars($heroImageUrl, ENT_QUOTES, 'UTF-8'); ?>"
            alt=""
            width="1920"
            height="1080"
            decoding="async"
            fetchpriority="high"
        >
    </div>
    <div class="landing__overlay landing__overlay--photo" aria-hidden="true"></div>

    <div class="landing__inner">
        <header class="landing__brand-row">
            <?php
            $brandHref = tc_url('page=landing');
            $brandVariant = 'landing';
            include __DIR__ . '/partials/brand_lockup.php';
            ?>
        </header>

        <div class="landing__hero">
            <p class="landing__kicker">Explore</p>
            <h1 class="landing__title">Trails</h1>
            <p class="landing__lede">Major hikes across the <strong>Philippines</strong> — join a group and stay trail-safe together.</p>
            <a class="btn-glass btn-glass--explore" href="<?php echo htmlspecialchars(tc_url('page=login'), ENT_QUOTES, 'UTF-8'); ?>">Explore</a>
        </div>
    </div>
</main>
<?php include 'partials/footer.php'; ?>
