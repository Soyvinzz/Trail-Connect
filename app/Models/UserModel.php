<?php
declare(strict_types=1);

final class UserModel
{
    public function findByEmail(string $email): ?array
    {
        return tc_db_find_user_by_email($email);
    }

    public function create(string $name, string $email, string $password, string $role, array $meta = []): array
    {
        return tc_db_create_user($name, $email, $password, $role, $meta);
    }

    public function setDisabled(int $userId, bool $disabled): void
    {
        tc_db_user_set_disabled($userId, $disabled);
    }

    public function updateProfile(int $userId, array $fields): void
    {
        tc_db_update_user_profile($userId, $fields);
    }
}
