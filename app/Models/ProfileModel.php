<?php
declare(strict_types=1);

final class ProfileModel
{
    public function save(array $profileData): void
    {
        tc_save_profile($profileData);
    }

    public function delete(): void
    {
        tc_delete_profile();
    }

    public function updateAvatarPath(int $userId, string $path): void
    {
        tc_db_update_user_profile($userId, ['avatar_path' => $path]);
    }
}
