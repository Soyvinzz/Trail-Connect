<?php
declare(strict_types=1);
if (tc_logged_in()) {
    header('Location: index.php?page=dashboard');
    exit;
}
$pageTitle = 'Register — TrailConnect';
$bodyClass = 'auth-body';
$error = (string) ($_GET['error'] ?? '');
$errMsg = match ($error) {
    'name' => 'Please enter your full name.',
    'email' => 'Please enter your email address.',
    'phone' => 'Please enter your contact number.',
    'phone_invalid' => 'Please enter a valid contact number.',
    'current_address' => 'Please enter your current address.',
    'hiking_level' => 'Please select your hiking level.',
    'minor_hikes_completed' => 'Please enter how many minor hikes you have completed.',
    'major_hikes_completed' => 'Please enter how many major hikes you have completed.',
    'bio_note' => 'Please add a short bio note about your hiking background.',
    'password_short' => 'Password must be at least 8 characters.',
    'password_mismatch' => 'Password and confirm password do not match.',
    'email_exists' => 'Email is already registered. Please login instead.',
    'db' => 'Unable to save your account right now. Please try again.',
    default => '',
};
include 'partials/header.php';
?>
<main class="auth-page auth-page--wide">
    <p class="auth-brand"><?php $brandHref = 'index.php?page=landing'; $brandVariant = 'nav'; include __DIR__ . '/partials/brand_lockup.php'; ?></p>
    <form class="register-form" method="post" action="index.php?page=register" novalidate>
        <input type="hidden" name="action" value="register">
        <h2>Create account</h2>
        <p class="form-lede">Join hikers and organizers across the <strong>Philippines</strong>. Pick your role once — it shapes your home screen.</p>
        <?php if ($errMsg !== '') : ?>
            <p class="form-error" role="alert"><?php echo htmlspecialchars($errMsg, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>

        <fieldset class="field-group">
            <legend class="field-label">Full name</legend>
            <input class="input" type="text" name="name" placeholder="e.g. Juan dela Cruz" autocomplete="name" required>
        </fieldset>

        <label class="field-label" for="reg-email">Email</label>
        <input id="reg-email" class="input" type="email" name="email" placeholder="you@example.com" autocomplete="email" required>

        <fieldset class="field-group" data-hiker-fields>
            <legend class="field-label">Hiker / Joiner profile</legend>
            <label class="field-label" for="reg-phone">Contact number</label>
            <input id="reg-phone" class="input" type="tel" name="phone" placeholder="+63 9XX XXX XXXX" autocomplete="tel" data-hiker-required>

            <label class="field-label" for="reg-current-address">Current address</label>
            <input id="reg-current-address" class="input" type="text" name="current_address" placeholder="City, Province" autocomplete="address-level2" data-hiker-required>

            <label class="field-label" for="reg-hiking-level">Hiking level</label>
            <select id="reg-hiking-level" class="input input--select" name="hiking_level" data-hiker-required>
                <option value="">Select level</option>
                <option value="beginner">Beginner</option>
                <option value="minor">Minor hikes level</option>
                <option value="intermediate">Intermediate</option>
                <option value="advanced">Advanced</option>
            </select>

            <label class="field-label" for="reg-minor-hikes">Minor hikes completed</label>
            <input id="reg-minor-hikes" class="input" type="number" name="minor_hikes_completed" min="0" step="1" placeholder="e.g. 3" inputmode="numeric" data-hiker-required>

            <label class="field-label" for="reg-major-hikes">Major hikes completed</label>
            <input id="reg-major-hikes" class="input" type="number" name="major_hikes_completed" min="0" step="1" placeholder="e.g. 1" inputmode="numeric" data-hiker-required>

            <label class="field-label" for="reg-bio-note">Bio note</label>
            <textarea id="reg-bio-note" class="input" name="bio_note" rows="3" placeholder="Share your hiking background, strengths, and pacing style." data-hiker-required></textarea>
            <p class="field-hint">Required for Hiker / Joiner accounts so organizers can assess trail readiness.</p>
        </fieldset>

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
  var roleInputs = document.querySelectorAll('input[name="role"]');
  var hikerFieldsWrap = document.querySelector('[data-hiker-fields]');
  var hikerRequiredFields = document.querySelectorAll('[data-hiker-required]');
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

  function syncHikerRequirements() {
    var selected = document.querySelector('input[name="role"]:checked');
    var isHiker = !selected || selected.value !== 'organizer';
    hikerRequiredFields.forEach(function (field) {
      field.required = isHiker;
    });
    if (hikerFieldsWrap) {
      hikerFieldsWrap.style.display = isHiker ? '' : 'none';
    }
  }

  roleInputs.forEach(function (input) {
    input.addEventListener('change', syncHikerRequirements);
  });
  syncHikerRequirements();
})();
</script>
<?php include 'partials/footer.php'; ?>
