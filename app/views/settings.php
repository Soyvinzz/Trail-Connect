<?php
declare(strict_types=1);
$pageTitle = 'Account settings — TrailConnect';
$bodyClass = 'app-body';
include 'partials/header.php';
include 'partials/navbar.php';
?>
<div class="container container--app container--narrow">
    <header class="page-head">
        <h1 class="page-title">Account settings</h1>
        <p class="page-lede">Security and notifications for your TrailConnect account — hikes around Bacolod &amp; Negros Occ.</p>
        <a class="text-link" href="index.php?page=profile">← Profile</a>
    </header>

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
            <li><label><input type="checkbox" name="digest"> Weekly digest: new hikes near Murcia, Silay, DSB &amp; Victorias</label></li>
        </ul>
        <button type="submit" class="btn-secondary">Save preferences</button>
    </form>
</div>
<?php include 'partials/footer.php'; ?>
