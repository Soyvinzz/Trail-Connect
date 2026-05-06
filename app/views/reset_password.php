<?php
declare(strict_types=1);
$pageTitle = 'Reset password — TrailConnect';
$bodyClass = 'auth-body';
$token = trim((string) ($_GET['token'] ?? ''));
$error = (string) ($_GET['error'] ?? '');
$errMsg = match ($error) {
    'token' => 'Missing reset token. Use the reset link again.',
    'token_expired' => 'Reset token is invalid or expired. Request a new reset link.',
    'password_short' => 'Password must be at least 8 characters.',
    'password_mismatch' => 'Password and confirm password do not match.',
    'db' => 'Unable to reset password right now. Please try again.',
    default => '',
};
include 'partials/header.php';
?>
<main class="auth-page">
    <p class="auth-brand"><?php $brandHref = 'index.php?page=landing'; $brandVariant = 'nav'; include __DIR__ . '/partials/brand_lockup.php'; ?></p>
    <form method="post" action="index.php?page=reset_password" novalidate>
        <input type="hidden" name="action" value="reset_password">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token, ENT_QUOTES, 'UTF-8'); ?>">
        <h2>Set new password</h2>
        <p class="form-lede">Create a strong new password for your account.</p>
        <?php if ($errMsg !== '') : ?>
            <p class="form-error" role="alert"><?php echo htmlspecialchars($errMsg, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        <label class="field-label" for="rp-password">New password</label>
        <input id="rp-password" class="input" type="password" name="password" autocomplete="new-password" minlength="8" required>
        <label class="field-label" for="rp-password2">Confirm new password</label>
        <input id="rp-password2" class="input" type="password" name="password2" autocomplete="new-password" minlength="8" required>
        <button class="btn-primary" type="submit">Reset password</button>
        <p class="auth-links auth-links--center"><a href="index.php?page=login">Back to login</a></p>
    </form>
</main>
<?php include 'partials/footer.php'; ?>
