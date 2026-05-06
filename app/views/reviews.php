<?php
declare(strict_types=1);
$pageTitle = 'Reviews — TrailConnect';
$bodyClass = 'app-body';
$name = tc_display_name();
$events = array_values(tc_events_published());
$reviews = array_values(tc_reviews());
usort($reviews, static function (array $a, array $b): int {
    return strcmp((string) ($b['posted_at'] ?? ''), (string) ($a['posted_at'] ?? ''));
});
$myReviews = [];
foreach ($reviews as $review) {
    if ((string) ($review['author_name'] ?? '') === $name) {
        $myReviews[] = $review;
    }
}
$editingReviewId = (int) ($_GET['edit_review'] ?? 0);
$editingReview = null;
foreach ($myReviews as $review) {
    if ((int) $review['id'] === $editingReviewId) {
        $editingReview = $review;
        break;
    }
}
$msg = (string) ($_GET['msg'] ?? '');
$messages = [
    'review_created' => 'Review submitted successfully.',
    'review_updated' => 'Review updated successfully.',
    'review_deleted' => 'Review deleted successfully.',
];
include 'partials/header.php';
include 'partials/navbar.php';
?>
<div class="container container--app container--narrow">
    <header class="page-head">
        <h1 class="page-title">Reviews</h1>
        <p class="page-lede">Thank organizers and co-hikers after <strong>Philippines</strong> outings — ratings build trust on major hikes and technical lines.</p>
    </header>

    <section class="card card--stack glass-stack review-form">
        <h2 class="section-title"><?php echo $editingReview ? 'Edit your review' : 'Write a review'; ?></h2>
        <?php if (isset($messages[$msg])) : ?>
            <div class="banner-safety" role="status" style="margin-bottom:0.75rem">
                <strong>Notification:</strong> <?php echo htmlspecialchars($messages[$msg], ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])) : ?>
            <p class="form-error" role="alert">Recipient and review message are required.</p>
        <?php endif; ?>
        <form method="post" action="index.php?page=reviews">
            <input type="hidden" name="action" value="save_review">
            <input type="hidden" name="review_id" value="<?php echo (int) ($editingReview['id'] ?? 0); ?>">

            <label class="field-label" for="rev-who">Review recipient</label>
            <?php $selectedRecipient = (string) ($editingReview['recipient'] ?? ''); ?>
            <select id="rev-who" class="input input--select" name="recipient" required>
                <option value="">Select person or group…</option>
                <?php
                $recipients = [
                    'Cordillera Guides (organizer)',
                    'Mindanao Ascents (organizer)',
                    'Sibuyan Expeditions (organizer)',
                    'Philippine High Peaks Club',
                    'Co-hiker — Alex Reyes',
                ];
                foreach ($recipients as $recipient) :
                ?>
                    <option value="<?php echo htmlspecialchars($recipient, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $selectedRecipient === $recipient ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($recipient, ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label class="field-label" for="rev-event">Related event <span class="text-muted">(optional)</span></label>
            <select id="rev-event" class="input input--select" name="event_id">
                <option value="0">No specific event</option>
                <?php foreach ($events as $event) : ?>
                    <option value="<?php echo (int) $event['id']; ?>" <?php echo ((int) ($editingReview['event_id'] ?? 0) === (int) $event['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars((string) $event['title'], ENT_QUOTES, 'UTF-8'); ?> — <?php echo htmlspecialchars((string) $event['date'], ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <span class="field-label">Rating</span>
            <?php $selectedStars = (int) ($editingReview['stars'] ?? 5); ?>
            <div class="star-picker" role="group" aria-label="Star rating">
                <label class="star-picker__opt"><input type="radio" name="stars" value="5" <?php echo $selectedStars === 5 ? 'checked' : ''; ?>> ★★★★★ Excellent</label>
                <label class="star-picker__opt"><input type="radio" name="stars" value="4" <?php echo $selectedStars === 4 ? 'checked' : ''; ?>> ★★★★☆ Very good</label>
                <label class="star-picker__opt"><input type="radio" name="stars" value="3" <?php echo $selectedStars === 3 ? 'checked' : ''; ?>> ★★★☆☆ Okay</label>
                <label class="star-picker__opt"><input type="radio" name="stars" value="2" <?php echo $selectedStars === 2 ? 'checked' : ''; ?>> ★★☆☆☆ Needs improvement</label>
                <label class="star-picker__opt"><input type="radio" name="stars" value="1" <?php echo $selectedStars === 1 ? 'checked' : ''; ?>> ★☆☆☆☆ Poor</label>
            </div>

            <label class="field-label" for="rev-text">Your review</label>
            <textarea id="rev-text" class="input" name="text" rows="4" placeholder="Clear communication, safe pacing above the tree line, and solid logistics on a major hike — thank you!" required><?php echo htmlspecialchars((string) ($editingReview['text'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></textarea>

            <button type="submit" class="btn-primary"><?php echo $editingReview ? 'Save review' : 'Submit review'; ?></button>
        </form>
    </section>

    <section class="card card--stack glass-stack">
        <h2 class="section-title">My review history</h2>
        <?php if (empty($myReviews)) : ?>
            <p class="text-muted">No reviews yet. Submit your first review above.</p>
        <?php endif; ?>
        <?php foreach ($myReviews as $review) : ?>
            <article class="review-block">
                <div class="review-block__head">
                    <span><strong>You</strong> → <?php echo htmlspecialchars((string) $review['recipient'], ENT_QUOTES, 'UTF-8'); ?></span>
                    <span class="stars"><?php echo str_repeat('★', max(0, min(5, (int) $review['stars']))); ?><?php echo str_repeat('☆', max(0, 5 - (int) $review['stars'])); ?></span>
                    <time><?php echo htmlspecialchars((string) $review['posted_at'], ENT_QUOTES, 'UTF-8'); ?></time>
                </div>
                <p class="feed-item__body" style="margin:0"><?php echo htmlspecialchars((string) $review['text'], ENT_QUOTES, 'UTF-8'); ?></p>
                <div class="inline-actions" style="margin-top:0.75rem">
                    <a class="text-link" href="index.php?page=reviews&edit_review=<?php echo (int) $review['id']; ?>">Edit</a>
                    <form method="post" action="index.php?page=reviews">
                        <input type="hidden" name="action" value="delete_review">
                        <input type="hidden" name="review_id" value="<?php echo (int) $review['id']; ?>">
                        <button type="submit" class="btn-secondary btn-secondary--sm">Delete</button>
                    </form>
                </div>
            </article>
        <?php endforeach; ?>
    </section>
</div>
<?php include 'partials/footer.php'; ?>
