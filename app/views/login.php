<?php
declare(strict_types=1);
if (tc_logged_in() && !isset($_GET['two_factor'])) {
    header('Location: index.php?page=dashboard');
    exit;
}
$pageTitle = 'Login — TrailConnect';
$bodyClass = 'auth-body';
$error = $_GET['error'] ?? '';
$selectedRole = (string) ($_GET['role'] ?? 'hiker');
$selectedRole = $selectedRole === 'organizer' ? 'organizer' : 'hiker';
$pending2fa = tc_two_factor_pending_context();
$isTwoFactorStep = isset($_GET['two_factor']) && is_array($pending2fa);
$twoFactorRole = (string) ($pending2fa['role'] ?? $selectedRole);
$errMsg = match ($error) {
    'email' => 'Please enter your email address.',
    'password' => 'Please enter your password.',
    'badpass' => 'Incorrect password for this account.',
    'rate_limited' => 'Too many failed sign-in attempts. Please wait 5 minutes and try again.',
    'email_unverified' => 'Your email is not verified yet. Verify your account before signing in.',
    'account_disabled' => 'Your account is currently disabled. Contact support or admin.',
    'db' => 'Unable to connect to account storage right now. Please try again.',
    'two_factor_lib_missing' => '2FA API package is not installed. Run: composer require pragmarx/google2fa-qrcode bacon/bacon-qr-code',
    'two_factor_invalid' => 'Invalid verification code. Please try again.',
    'two_factor_expired' => 'Your verification session expired. Please sign in again.',
    default => '',
};
if (isset($_GET['required'])) {
    $errMsg = 'Please sign in to continue.';
}
if (isset($_GET['switch_required'])) {
    $errMsg = $selectedRole === 'organizer'
        ? 'Please sign in again as Organizer to continue.'
        : 'Please sign in again as Hiker to continue.';
}
include 'partials/header.php';
?>
<main class="auth-page">
    <p class="auth-brand"><?php $brandHref = 'index.php?page=landing'; $brandVariant = 'nav'; include __DIR__ . '/partials/brand_lockup.php'; ?></p>
    <?php if ($isTwoFactorStep) : ?>
    <form method="post" action="index.php?page=login" novalidate>
        <input type="hidden" name="action" value="verify_2fa">
        <h2>Two-factor verification</h2>
        <?php if ($errMsg !== '') : ?>
            <p class="form-error" role="alert"><?php echo htmlspecialchars($errMsg, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        <p class="field-hint">Enter the 6-digit code from your authenticator app for the <?php echo htmlspecialchars(ucfirst($twoFactorRole), ENT_QUOTES, 'UTF-8'); ?> account.</p>
        <label class="field-label" for="otp-code">Authenticator code</label>
        <input id="otp-code" class="input" type="text" name="otp_code" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" placeholder="123456" required>
        <button class="btn-primary" type="submit">Verify and continue</button>
        <p class="auth-links"><a href="index.php?page=login&role=<?php echo htmlspecialchars($twoFactorRole, ENT_QUOTES, 'UTF-8'); ?>">Back to login</a></p>
    </form>
    <?php else : ?>
    <form method="post" action="index.php?page=login" novalidate>
        <input type="hidden" name="action" value="login">
        <h2>Login</h2>
        <p class="form-lede auth-lede">Welcome back. Continue your trail planning, approvals, and hike updates.</p>
        <?php if ($errMsg !== '') : ?>
            <p class="form-error" role="alert"><?php echo htmlspecialchars($errMsg, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        <?php if (isset($_GET['registered'])) : ?>
            <p class="form-success" role="status">Registration complete. You can sign in now.</p>
        <?php endif; ?>
        <?php if (isset($_GET['reset'])) : ?>
            <p class="form-success" role="status">Password reset complete. You can sign in now.</p>
        <?php endif; ?>
        <?php if (isset($_GET['verified'])) : ?>
            <p class="form-success" role="status">Email verified successfully. You can sign in now.</p>
        <?php endif; ?>
        <?php if ($error === 'email_unverified') : ?>
            <form method="post" action="index.php?page=login" style="margin-bottom:0.65rem">
                <input type="hidden" name="action" value="resend_verification">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars((string) ($_GET['email'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                <button type="submit" class="btn-secondary btn-secondary--sm">Resend verification link</button>
            </form>
        <?php endif; ?>
        <label class="field-label" for="login-email">Email</label>
        <input id="login-email" class="input" type="email" name="email" placeholder="you@example.com" autocomplete="email" required>
        <label class="field-label" for="login-password">Password</label>
        <div class="password-field">
            <input id="login-password" class="input password-field__input" type="password" name="password" placeholder="Password" autocomplete="current-password" required>
            <button type="button" class="password-field__toggle" aria-pressed="false" aria-label="Show password" data-password-toggle>Show</button>
        </div>
        <fieldset class="role-toggle">
            <legend class="field-label">Login as</legend>
            <label class="role-toggle__option">
                <input type="radio" name="role" value="hiker" <?php echo $selectedRole === 'hiker' ? 'checked' : ''; ?>>
                <span class="role-toggle__card">
                    <strong>Hiker / Joiner</strong>
                    <span>Manage join requests, your profile, and your reviews.</span>
                </span>
            </label>
            <label class="role-toggle__option">
                <input type="radio" name="role" value="organizer" <?php echo $selectedRole === 'organizer' ? 'checked' : ''; ?>>
                <span class="role-toggle__card">
                    <strong>Organizer</strong>
                    <span>Manage events, join approvals, and updates.</span>
                </span>
            </label>
        </fieldset>
        <button class="btn-primary" type="submit">Login</button>
        <p class="auth-links">
            <a href="index.php?page=forgot_password">Forgot password</a>
            <span class="auth-links__sep">·</span>
            <a href="index.php?page=register">Create account</a>
        </p>
    </form>
    <?php endif; ?>
</main>
<script>
(function () {
    var btn = document.querySelector('[data-password-toggle]');
    var input = document.querySelector('#login-password');
    if (!btn || !input) return;
    btn.addEventListener('click', function () {
    var show = input.type === 'password';
    input.type = show ? 'text' : 'password';
    btn.textContent = show ? 'Hide' : 'Show';
    btn.setAttribute('aria-pressed', show ? 'true' : 'false');
    });
})();
</script>
<?php include 'partials/footer.php'; ?>
