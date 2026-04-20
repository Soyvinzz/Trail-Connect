<?php
$pageTitle = 'TrailConnect — Explore';
$bodyClass = 'landing-body';

require_once __DIR__ . '/../includes/paths.php';

$projectRoot = dirname(__DIR__, 2);
$publicDir = $projectRoot . DIRECTORY_SEPARATOR . 'public';
$videoCandidates = [
    ['path' => $publicDir . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'video' . DIRECTORY_SEPARATOR . 'landing.mp4', 'rel' => 'assets/video/landing.mp4'],
    ['path' => $publicDir . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'video' . DIRECTORY_SEPARATOR . 'LANDINGPAGE.mp4', 'rel' => 'assets/video/LANDINGPAGE.mp4'],
    ['path' => $publicDir . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'vid' . DIRECTORY_SEPARATOR . 'LANDINGPAGE.mp4', 'rel' => 'assets/vid/LANDINGPAGE.mp4'],
    ['path' => $publicDir . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'vid' . DIRECTORY_SEPARATOR . 'landing.mp4', 'rel' => 'assets/vid/landing.mp4'],
];
$landingVideoRel = $videoCandidates[0]['rel'];
foreach ($videoCandidates as $c) {
    if (is_file($c['path'])) {
        $landingVideoRel = $c['rel'];
        break;
    }
}
$landingVideoUrl = tc_asset_url($landingVideoRel);
$landingPosterUrl = tc_asset_url('assets/img/mountain-landingpage.jpg');

include 'partials/header.php';
?>
<main class="landing" aria-label="Welcome">
    <div class="landing__media" aria-hidden="true">
        <video
            class="landing__video"
            autoplay
            muted
            loop
            playsinline
            preload="auto"
            poster="<?php echo htmlspecialchars($landingPosterUrl, ENT_QUOTES, 'UTF-8'); ?>"
        >
            <source src="<?php echo htmlspecialchars($landingVideoUrl, ENT_QUOTES, 'UTF-8'); ?>" type='assets/vid/LANDINGPAGE.mp4'>
        </video>
        <div
            class="landing__bg landing__bg--fallback"
            style="--img-hero: url('<?php echo htmlspecialchars($landingPosterUrl, ENT_QUOTES, 'UTF-8'); ?>')"
        ></div>
    </div>
    <div class="landing__overlay landing__overlay--video" aria-hidden="true"></div>

    <div class="landing__inner">
        <header class="landing__brand-row">
            <span class="landing__logo-mark" aria-hidden="true"></span>
            <span class="landing__wordmark">TrailConnect</span>
        </header>

        <div class="landing__hero">
            <p class="landing__kicker">Explore</p>
            <h1 class="landing__title">Trails</h1>
            <p class="landing__lede">Major hikes across the <strong>Philippines</strong> — join a group and stay trail-safe together.</p>
            <a class="btn-glass btn-glass--explore" href="<?php echo htmlspecialchars(tc_url('page=login'), ENT_QUOTES, 'UTF-8'); ?>">Explore</a>
        </div>
    </div>
</main>
<script>
(function () {
    var v = document.querySelector('.landing__video');
    if (!v) return;
    v.muted = true;
    var p = v.play();
    if (p && typeof p.then === 'function') p.catch(function () {});
})();
</script>
<?php include 'partials/footer.php'; ?>
