<?php
declare(strict_types=1);
if (tc_logged_in()) {
    header('Location: index.php?page=dashboard');
    exit;
}
$pageTitle = 'Register — TrailConnect';
$bodyClass = 'auth-body';
include 'partials/header.php';
?>
<main class="auth-page auth-page--wide">
    <p class="auth-brand"><a href="index.php?page=landing">TrailConnect</a></p>
    <form class="register-form" method="post" action="index.php?page=register" novalidate>
        <input type="hidden" name="action" value="register">
        <h2>Create account</h2>
        <p class="form-lede">Join hikers and organizers across the <strong>Philippines</strong>. Pick your role once — it shapes your home screen.</p>

        <fieldset class="field-group">
            <legend class="field-label">Full name</legend>
            <input class="input" type="text" name="name" placeholder="e.g. Juan dela Cruz" autocomplete="name" required>
        </fieldset>

        <label class="field-label" for="reg-email">Email</label>
        <input id="reg-email" class="input" type="email" name="email" placeholder="you@example.com" autocomplete="email" required>

        <label class="field-label" for="reg-password">Password</label>
        <input id="reg-password" class="input" type="password" name="password" autocomplete="new-password" required minlength="8" aria-describedby="pw-strength-hint">
        <div class="password-meter" aria-live="polite">
            <div class="password-meter__bar" id="pw-meter-bar"></div>
            <span class="password-meter__label" id="pw-meter-label">Strength</span>
        </div>
        <p class="field-hint" id="pw-strength-hint">At least 8 characters. Mix letters, numbers, and symbols for a stronger password.</p>

        <label class="field-label" for="reg-password2">Confirm password</label>
        <input id="reg-password2" class="input" type="password" name="password2" autocomplete="new-password" required>

        <fieldset class="role-toggle">
            <legend class="field-label">I am a</legend>
            <label class="role-toggle__option">
                <input type="radio" name="role" value="hiker" checked>
                <span class="role-toggle__card">
                    <strong>Hiker</strong>
                    <span>Discover Pulag, Apo, G2, and more — join hikes and build your trail reputation.</span>
                </span>
            </label>
            <label class="role-toggle__option">
                <input type="radio" name="role" value="organizer">
                <span class="role-toggle__card">
                    <strong>Organizer</strong>
                    <span>Publish Philippines-wide events, review join requests, and post safety updates.</span>
                </span>
            </label>
        </fieldset>

        <button class="btn-primary" type="submit">Register</button>
        <p class="auth-links auth-links--center">
            <a href="index.php?page=login">Already have an account? Login</a>
        </p>
    </form>
</main>
<script>
(function () {
  var pw = document.getElementById('reg-password');
  var bar = document.getElementById('pw-meter-bar');
  var label = document.getElementById('pw-meter-label');
  if (!pw || !bar || !label) return;
  function score(s) {
    var sc = 0;
    if (s.length >= 8) sc++;
    if (s.length >= 12) sc++;
    if (/[0-9]/.test(s)) sc++;
    if (/[^A-Za-z0-9]/.test(s)) sc++;
    if (/[a-z]/.test(s) && /[A-Z]/.test(s)) sc++;
    return Math.min(sc, 4);
  }
  function words(sc) {
    return ['Weak', 'Fair', 'Good', 'Strong', 'Strong'][sc] || 'Weak';
  }
  pw.addEventListener('input', function () {
    var sc = score(pw.value);
    bar.dataset.level = String(sc);
    label.textContent = words(sc);
  });
})();
</script>
<?php include 'partials/footer.php'; ?>
