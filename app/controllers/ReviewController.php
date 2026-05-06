<?php
declare(strict_types=1);

require_once __DIR__ . '/../Models/ReviewModel.php';

final class ReviewController
{
    private ReviewModel $reviews;

    public function __construct()
    {
        $this->reviews = new ReviewModel();
    }

    public function handle(string $action): bool
    {
        return match ($action) {
            'save_review' => $this->saveReview(),
            'delete_review' => $this->deleteReview(),
            default => false,
        };
    }

    private function saveReview(): bool
    {
        if (tc_role() !== 'hiker') {
            header('Location: index.php?page=dashboard');
            exit;
        }
        $reviewId = (int) ($_POST['review_id'] ?? 0);
        $recipient = trim((string) ($_POST['recipient'] ?? ''));
        $text = trim((string) ($_POST['text'] ?? ''));
        if ($recipient === '' || $text === '') {
            header('Location: index.php?page=reviews&error=review_required');
            exit;
        }
        $existing = $reviewId > 0 ? ($this->reviews->all()[$reviewId] ?? null) : null;
        if (is_array($existing) && (string) ($existing['author_name'] ?? '') !== tc_display_name()) {
            header('Location: index.php?page=reviews');
            exit;
        }
        $this->reviews->save([
            'id' => $reviewId > 0 ? $reviewId : null,
            'author_name' => tc_display_name(),
            'recipient' => $recipient,
            'event_id' => (int) ($_POST['event_id'] ?? 0),
            'stars' => max(1, min(5, (int) ($_POST['stars'] ?? 5))),
            'text' => $text,
            'posted_at' => date('Y-m-d H:i:s'),
        ]);
        header('Location: index.php?page=reviews&msg=' . ($reviewId > 0 ? 'review_updated' : 'review_created'));
        exit;
    }

    private function deleteReview(): bool
    {
        if (tc_role() !== 'hiker') {
            header('Location: index.php?page=dashboard');
            exit;
        }
        $reviewId = (int) ($_POST['review_id'] ?? 0);
        $review = $this->reviews->all()[$reviewId] ?? null;
        if ($reviewId > 0 && is_array($review) && (string) ($review['author_name'] ?? '') === tc_display_name()) {
            $this->reviews->delete($reviewId);
        }
        header('Location: index.php?page=reviews&msg=review_deleted');
        exit;
    }
}
