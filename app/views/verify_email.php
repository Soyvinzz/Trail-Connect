<?php
declare(strict_types=1);
$pageTitle = 'Verify email — TrailConnect';
$bodyClass = 'auth-body';
$sent = isset($_GET['sent']);
$token = trim((string) ($_GET['token'] ?? ''));
$email = trim((string) ($_GET['email'] ?? ''));
$error = (string) ($_GET['error'] ?? '');
$errMsg = match ($error) {
    'token' => 'Missing verification token. Use your latest verification link.',
    'token_expired' => 'Verification link is invalid or expired. Request a new one.',
    'db' => 'Unable to verify email right now. Please try again.',
    default => '',
};
$demoLink = isset($_SESSION['tc_verify_demo_link']) ? (string) $_SESSION['tc_verify_demo_link'] : '';
include 'partials/header.php';
?>
<main class="auth-page">
    <p class="auth-brand"><?php $brandHref = 'index.php?page=landing'; $brandVariant = 'nav'; include __DIR__ . '/partials/brand_lockup.php'; ?></p>
    <?php if ($sent) : ?>
        <div class="auth-card-msg">
            <h2>Verify your email</h2>
            <p>We sent a verification link to your email. Verify first before signing in.</p>
            <?php if ($demoLink !== '') : ?>
                <p class="field-hint">Local demo link: <a class="text-link" href="<?php echo htmlspecialchars($demoLink, ENT_QUOTES, 'UTF-8'); ?>">Verify email now</a></p>
            <?php endif; ?>
            <p class="auth-links auth-links--center"><a href="index.php?page=login">Back to login</a></p>
        </div>
    <?php else : ?>
        <form method="post" action="index.php?page=verify_email" novalidate>
            <input type="hidden" name="action" value="verify_email">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token, ENT_QUOTES, 'UTF-8'); ?>">
            <h2>Email verification</h2>
            <p class="form-lede">Confirm your email to activate sign-in access.</p>
            <?php if ($errMsg !== '') : ?>
                <p class="form-error" role="alert"><?php echo htmlspecialchars($errMsg, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
            <button class="btn-primary" type="submit">Verify email</button>
            <p class="auth-links auth-links--center">
                <a href="index.php?page=login">Back to login</a>
            </p>
        </form>
        <?php if ($email !== '') : ?>
        <form method="post" action="index.php?page=verify_email" style="margin-top:0.75rem">
            <input type="hidden" name="action" value="resend_verification">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>">
            <button class="btn-secondary btn-secondary--sm" type="submit">Resend verification link</button>
        </form>
        <?php endif; ?>
    <?php endif; ?>
</main>
<?php include 'partials/footer.php'; ?>
