<?php
declare(strict_types=1);
$pageTitle = 'Forgot password — TrailConnect';
$bodyClass = 'auth-body';
$sent = isset($_GET['sent']);
$error = (string) ($_GET['error'] ?? '');
$demoLink = isset($_SESSION['tc_reset_demo_link']) ? (string) $_SESSION['tc_reset_demo_link'] : '';
include 'partials/header.php';
?>
<main class="auth-page">
    <p class="auth-brand"><?php $brandHref = 'index.php?page=landing'; $brandVariant = 'nav'; include __DIR__ . '/partials/brand_lockup.php'; ?></p>
    <?php if ($sent) : ?>
        <div class="auth-card-msg">
            <h2>Check your email</h2>
            <p>We sent a time-limited reset link. If you do not see it within a few minutes, check spam or request again.</p>
            <?php if ($demoLink !== '') : ?>
                <p class="field-hint">Local demo link: <a class="text-link" href="<?php echo htmlspecialchars($demoLink, ENT_QUOTES, 'UTF-8'); ?>">Reset password now</a></p>
            <?php endif; ?>
            <a class="btn-primary btn-primary--inline" href="index.php?page=login">Back to login</a>
        </div>
    <?php else : ?>
        <form method="post" action="index.php?page=forgot_password">
            <input type="hidden" name="action" value="request_password_reset">
            <h2>Reset password</h2>
            <p class="form-lede">Enter the email you use for your TrailConnect account.</p>
            <?php if ($error === 'email') : ?>
                <p class="form-error" role="alert">Please enter your email address.</p>
            <?php elseif ($error === 'db') : ?>
                <p class="form-error" role="alert">Unable to process reset request right now. Please try again.</p>
            <?php endif; ?>
            <label class="field-label" for="fp-email">Email</label>
            <input id="fp-email" class="input" type="email" name="email" placeholder="you@example.com" autocomplete="email" required>
            <button class="btn-primary" type="submit">Send reset link</button>
            <p class="auth-links auth-links--center">
                <a href="index.php?page=login">Return to login</a>
            </p>
        </form>
    <?php endif; ?>
</main>
<?php include 'partials/footer.php'; ?>
