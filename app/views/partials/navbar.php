<?php
declare(strict_types=1);
$role = tc_role();
$cur = $_GET['page'] ?? '';
?>
<header class="site-header">
    <nav class="navbar" aria-label="Primary">
        <a class="brand" href="index.php?page=landing">TrailConnect</a>
        <ul class="nav-links">
            <li><a href="index.php?page=dashboard"<?php echo $cur === 'dashboard' ? ' class="is-active"' : ''; ?>>Home</a></li>
            <?php if ($role === 'organizer') : ?>
                <li><a href="index.php?page=create_event"<?php echo $cur === 'create_event' ? ' class="is-active"' : ''; ?>>Create Event</a></li>
            <?php else : ?>
                <li><a href="index.php?page=find_hikes"<?php echo $cur === 'find_hikes' ? ' class="is-active"' : ''; ?>>Find Hikes</a></li>
            <?php endif; ?>
            <li><a href="index.php?page=my_event"<?php echo $cur === 'my_event' ? ' class="is-active"' : ''; ?>>My Events</a></li>
            <li><a href="index.php?page=profile"<?php echo $cur === 'profile' ? ' class="is-active"' : ''; ?>>Profile</a></li>
            <li>
                <form class="nav-logout" method="post" action="index.php">
                    <input type="hidden" name="action" value="logout">
                    <button type="submit" class="nav-logout__btn">Log out</button>
                </form>
            </li>
        </ul>
    </nav>
</header>
