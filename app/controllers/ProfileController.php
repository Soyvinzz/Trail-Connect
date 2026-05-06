<?php
declare(strict_types=1);

require_once __DIR__ . '/../Models/ProfileModel.php';

final class ProfileController
{
    private ProfileModel $profiles;

    public function __construct()
    {
        $this->profiles = new ProfileModel();
    }

    public function handle(string $action): bool
    {
        return match ($action) {
            'save_profile' => $this->saveProfile(),
            'upload_avatar' => $this->uploadAvatar(),
            'delete_profile' => $this->deleteProfile(),
            default => false,
        };
    }

    private function saveProfile(): bool
    {
        $name = trim((string) ($_POST['display_name'] ?? ''));
        $this->profiles->save([
            'display_name' => $name !== '' ? $name : tc_display_name(),
            'bio' => trim((string) ($_POST['bio'] ?? '')),
            'home' => trim((string) ($_POST['home'] ?? '')),
            'phone_number' => trim((string) ($_POST['phone_number'] ?? '')),
            'current_address' => trim((string) ($_POST['current_address'] ?? '')),
            'hiking_level' => trim((string) ($_POST['hiking_level'] ?? '')),
            'minor_hikes_completed' => max(0, (int) ($_POST['minor_hikes_completed'] ?? 0)),
            'major_hikes_completed' => max(0, (int) ($_POST['major_hikes_completed'] ?? 0)),
            'emergency_contact_name' => trim((string) ($_POST['emergency_contact_name'] ?? '')),
            'emergency_contact_number' => trim((string) ($_POST['emergency_contact_number'] ?? '')),
            'medical_notes' => trim((string) ($_POST['medical_notes'] ?? '')),
        ]);
        header('Location: index.php?page=profile&msg=profile_saved');
        exit;
    }

    private function uploadAvatar(): bool
    {
        $userId = tc_current_user_id();
        if ($userId <= 0) {
            header('Location: index.php?page=profile');
            exit;
        }
        if (!isset($_FILES['avatar']) || !is_array($_FILES['avatar'])) {
            header('Location: index.php?page=profile&error=avatar_missing');
            exit;
        }
        $file = $_FILES['avatar'];
        if ((int) ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            header('Location: index.php?page=profile&error=avatar_upload');
            exit;
        }
        $tmpPath = (string) ($file['tmp_name'] ?? '');
        $size = (int) ($file['size'] ?? 0);
        if ($size <= 0 || $size > 5 * 1024 * 1024) {
            header('Location: index.php?page=profile&error=avatar_size');
            exit;
        }
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = (string) $finfo->file($tmpPath);
        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];
        if (!isset($allowed[$mime])) {
            header('Location: index.php?page=profile&error=avatar_type');
            exit;
        }
        $uploadDir = __DIR__ . '/../../public/assets/uploads/avatars';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $filename = 'u' . $userId . '_' . bin2hex(random_bytes(8)) . '.' . $allowed[$mime];
        $destPath = $uploadDir . '/' . $filename;
        if (!move_uploaded_file($tmpPath, $destPath)) {
            header('Location: index.php?page=profile&error=avatar_upload');
            exit;
        }
        $relativePath = 'assets/uploads/avatars/' . $filename;
        try {
            $this->profiles->updateAvatarPath($userId, $relativePath);
        } catch (\Throwable $e) {
            header('Location: index.php?page=profile&error=db');
            exit;
        }
        $_SESSION['tc_avatar_path'] = $relativePath;
        header('Location: index.php?page=profile&msg=avatar_saved');
        exit;
    }

    private function deleteProfile(): bool
    {
        $this->profiles->delete();
        header('Location: index.php?page=profile&msg=profile_reset');
        exit;
    }
}
