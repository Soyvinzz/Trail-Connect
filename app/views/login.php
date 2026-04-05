<?php
declare(strict_types=1);
if (tc_logged_in()) {
    header('Location: index.php?page=dashboard');
    exit;
}
$pageTitle = 'Login — TrailConnect';
$bodyClass = 'auth-body';
$error = $_GET['error'] ?? '';
$errMsg = match ($error) {
    'email' => 'Please enter your email address.',
    'password' => 'Please enter your password.',
    'badpass' => 'Incorrect password for this account.',
    default => '',
};
if (isset($_GET['required'])) {
    $errMsg = 'Please sign in to continue.';
}
include 'partials/header.php';
?>
<main class="auth-page">
    <p class="auth-brand"><a href="index.php?page=landing">TrailConnect</a></p>
    <form method="post" action="index.php?page=login" novalidate>
        <input type="hidden" name="action" value="login">
        <h2>Login</h2>
        <?php if ($errMsg !== '') : ?>
            <p class="form-error" role="alert"><?php echo htmlspecialchars($errMsg, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        <?php if (isset($_GET['registered'])) : ?>
            <p class="form-success" role="status">Registration complete. You can sign in now.</p>
        <?php endif; ?>
        <label class="field-label" for="login-email">Email</label>
        <input id="login-email" class="input" type="email" name="email" placeholder="you@example.com" autocomplete="email" required>
        <label class="field-label" for="login-password">Password</label>
        <div class="password-field">
            <input id="login-password" class="input password-field__input" type="password" name="password" placeholder="Password" autocomplete="current-password" required>
            <button type="button" class="password-field__toggle" aria-pressed="false" aria-label="Show password" data-password-toggle>Show</button>
        </div>
        <button class="btn-primary" type="submit">Login</button>
        <p class="auth-links">
            <a href="index.php?page=forgot_password">Forgot password</a>
            <span class="auth-links__sep">·</span>
            <a href="index.php?page=register">Create account</a>
        </p>
        <p class="auth-hint">Note: use any email except <code>wrong@example.com</code> (simulates wrong password).</p>
    </form>
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
