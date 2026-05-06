<?php
declare(strict_types=1);

final class ReviewModel
{
    public function all(): array
    {
        return tc_reviews();
    }

    public function save(array $review): void
    {
        tc_save_review($review);
    }

    public function delete(int $reviewId): void
    {
        tc_delete_review($reviewId);
    }
}
