<?php
declare(strict_types=1);
$pageTitle = 'Forgot password — TrailConnect';
$bodyClass = 'auth-body';
$sent = isset($_GET['sent']);
include 'partials/header.php';
?>
<main class="auth-page">
    <p class="auth-brand"><a href="index.php?page=landing">TrailConnect</a></p>
    <?php if ($sent) : ?>
        <div class="auth-card-msg">
            <h2>Check your email</h2>
            <p>We sent a time-limited reset link. If you do not see it within a few minutes, check spam or request again.</p>
            <a class="btn-primary btn-primary--inline" href="index.php?page=login">Back to login</a>
        </div>
    <?php else : ?>
        <form method="get" action="index.php">
            <input type="hidden" name="page" value="forgot_password">
            <input type="hidden" name="sent" value="1">
            <h2>Reset password</h2>
            <p class="form-lede">Enter the email you use for your TrailConnect account.</p>
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
