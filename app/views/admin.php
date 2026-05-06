<?php
declare(strict_types=1);
$pageTitle = 'Admin moderation — TrailConnect';
$bodyClass = 'app-body';
$msg = (string) ($_GET['msg'] ?? '');
$users = [];
try {
    $users = tc_db_users_all();
} catch (\Throwable $e) {
    $users = [];
}
include 'partials/header.php';
include 'partials/navbar.php';
?>
<div class="container container--app">
    <header class="page-head">
        <h1 class="page-title">Admin moderation</h1>
        <p class="page-lede">Manage account access and enforce platform safety.</p>
    </header>
    <?php if ($msg === 'user_updated') : ?>
        <div class="banner-safety" role="status" style="margin-bottom:1rem"><strong>Notification:</strong> User status updated.</div>
    <?php elseif ($msg === 'update_failed') : ?>
        <div class="banner-safety banner-safety--warn" role="alert" style="margin-bottom:1rem"><strong>Could not update user.</strong> Try again. If this persists, check the database connection.</div>
    <?php endif; ?>
    <section class="card card--stack glass-stack">
        <h2 class="section-title">Users</h2>
        <?php if ($users === []) : ?>
            <p class="text-muted">No users available.</p>
        <?php else : ?>
            <?php foreach ($users as $user) : ?>
                <?php
                $uid = (int) ($user['id'] ?? 0);
                $disabled = !empty($user['is_disabled']);
                ?>
                <article class="event-row" style="margin-bottom:0.85rem">
                    <div class="event-row__main">
                        <p class="request-card__who"><strong><?php echo htmlspecialchars((string) ($user['full_name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></strong> · <?php echo htmlspecialchars((string) ($user['email'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>
                        <p class="request-card__when">
                            Role: <?php echo htmlspecialchars(ucfirst((string) ($user['role'] ?? 'hiker')), ENT_QUOTES, 'UTF-8'); ?>
                            · Status: <?php echo $disabled ? 'Disabled' : 'Active'; ?>
                        </p>
                    </div>
                    <div class="inline-actions">
                        <form method="post" action="index.php?page=admin">
                            <input type="hidden" name="action" value="admin_toggle_user">
                            <input type="hidden" name="user_id" value="<?php echo $uid; ?>">
                            <input type="hidden" name="disable" value="<?php echo $disabled ? '0' : '1'; ?>">
                            <button class="btn-secondary btn-secondary--sm" type="submit"><?php echo $disabled ? 'Enable' : 'Disable'; ?></button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>
</div>
<?php include 'partials/footer.php'; ?>
