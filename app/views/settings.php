<?php
declare(strict_types=1);
$pageTitle = 'Account settings — TrailConnect';
$bodyClass = 'app-body';
$msg = (string) ($_GET['msg'] ?? '');
$error = (string) ($_GET['error'] ?? '');
$status = tc_two_factor_status(tc_role());
$setupSecret = $status['temp_secret'];
if (!tc_two_factor_library_ready()) {
    $setupSecret = '';
} elseif ($setupSecret === '' && !$status['enabled']) {
    $setupSecret = tc_two_factor_begin_setup(tc_role());
    $status = tc_two_factor_status(tc_role());
}
$activeSecret = $status['enabled'] ? $status['secret'] : $setupSecret;
$qrInline = tc_two_factor_qr_inline($activeSecret, tc_role());
$messages = [
    'two_factor_scan' => 'Scan the QR code and enter the generated code to finish setup.',
    'two_factor_enabled' => 'Two-factor authentication is now enabled.',
    'two_factor_disabled' => 'Two-factor authentication has been disabled.',
];
$errors = [
    'two_factor_invalid' => 'Invalid code. Make sure your authenticator time is synced and try again.',
    'two_factor_setup' => 'Unable to start 2FA setup. Please try again.',
    'two_factor_lib_missing' => 'Required API packages are missing. Install with: composer require pragmarx/google2fa-qrcode bacon/bacon-qr-code',
];
include 'partials/header.php';
include 'partials/navbar.php';
?>
<div class="container container--app container--narrow">
    <header class="page-head">
        <h1 class="page-title">Account settings</h1>
        <p class="page-lede">Security and notifications for your TrailConnect account — hikes anywhere in the Philippines.</p>
        <a class="text-link" href="index.php?page=profile">← Profile</a>
    </header>

    <?php if (isset($messages[$msg])) : ?>
        <div class="banner-safety" role="status" style="margin-bottom:1rem">
            <strong>Notification:</strong> <?php echo htmlspecialchars($messages[$msg], ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>
    <?php if (isset($errors[$error])) : ?>
        <p class="form-error" role="alert" style="margin-bottom:1rem"><?php echo htmlspecialchars($errors[$error], ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>

    <form class="card card--stack glass-stack" method="get" action="index.php">
        <input type="hidden" name="page" value="settings">
        <h2 class="section-title">Password</h2>
        <p class="card-lede">Update your password to keep your organizer and join-request access secure.</p>
        <label class="field-label" for="cur-pw">Current password</label>
        <input id="cur-pw" class="input" type="password" name="cur" autocomplete="current-password">
        <label class="field-label" for="new-pw">New password</label>
        <input id="new-pw" class="input" type="password" name="new" autocomplete="new-password">
        <button type="button" class="btn-primary">Update password</button>
    </form>

    <form class="card card--stack glass-stack" method="get" action="index.php">
        <input type="hidden" name="page" value="settings">
        <h2 class="section-title">Notifications</h2>
        <ul class="check-list check-list--form">
            <li><label><input type="checkbox" name="email_join" checked> Email when a join request is approved or declined</label></li>
            <li><label><input type="checkbox" name="email_updates" checked> Push + email for <strong>safety</strong> and weather updates on joined hikes</label></li>
            <li><label><input type="checkbox" name="digest"> Weekly digest: new hikes in Luzon, Visayas, Mindanao &amp; Palawan</label></li>
        </ul>
        <button type="submit" class="btn-secondary">Save preferences</button>
    </form>

    <section class="card card--stack glass-stack">
        <h2 class="section-title">Two-factor authentication (2FA)</h2>
        <p class="card-lede">Secure your account with a 6-digit code from Google Authenticator or compatible apps.</p>
        <?php if (!tc_two_factor_library_ready()) : ?>
            <p class="form-error" role="alert">2FA setup is blocked until you install the API packages required by your chapter file.</p>
            <p class="field-hint"><code>composer require pragmarx/google2fa-qrcode bacon/bacon-qr-code</code></p>
        <?php elseif ($status['enabled']) : ?>
            <p class="field-hint">2FA is currently <strong>enabled</strong> for your <?php echo htmlspecialchars(tc_role(), ENT_QUOTES, 'UTF-8'); ?> account.</p>
            <form method="post" action="index.php?page=settings">
                <input type="hidden" name="action" value="disable_2fa">
                <button type="submit" class="btn-secondary">Disable 2FA</button>
            </form>
        <?php else : ?>
            <?php if ($qrInline !== '') : ?>
                <img src="<?php echo htmlspecialchars($qrInline, ENT_QUOTES, 'UTF-8'); ?>" alt="2FA QR code" width="220" height="220" style="max-width:100%;border-radius:12px;">
            <?php endif; ?>
            <p class="field-hint">Secret key: <code><?php echo htmlspecialchars($setupSecret, ENT_QUOTES, 'UTF-8'); ?></code></p>
            <form method="post" action="index.php?page=settings">
                <input type="hidden" name="action" value="enable_2fa">
                <label class="field-label" for="otp-enable-code">Enter code from authenticator</label>
                <input id="otp-enable-code" class="input" type="text" name="otp_code" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" placeholder="123456" required>
                <button type="submit" class="btn-primary">Enable 2FA</button>
            </form>
            <form method="post" action="index.php?page=settings" style="margin-top:0.75rem">
                <input type="hidden" name="action" value="start_2fa_setup">
                <button type="submit" class="btn-secondary btn-secondary--sm">Generate new setup QR</button>
            </form>
        <?php endif; ?>
    </section>
</div>
<?php include 'partials/footer.php'; ?>
